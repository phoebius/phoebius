<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright notice
 *
 ************************************************************************************************/

define('SQLWORKBENCH_INFINITY', log(0));

/**
 * @ingroup PlainQuery
 */
class SqlWorkbench extends ResultWorkbench
{
	/**
	 * Placeholder argument.
	 * A placeholder that has this value semaphores to the parser that the block of SQL code
	 * surrounded with figure brackets should be cropped.
	 *
	 * Example:
	 * <code>
	 *  $db->query('SELECT * FROM db { WHERE id = ? }', "1");
	 *  $db->query('SELECT * FROM db { WHERE id = ? }', SqlWorkbench::SKIP_BLOCK);
	 * </code>
	 *
	 * Resulting queries:
	 * <code>
	 *  mysql> SELECT * FROM db WHERE id = '1'
	 *  mysql> SELECT * FROM db
	 * </code>
	 */
	const SKIP_BLOCK = SQLWORKBENCH_INFINITY;

	/**
	 * Avoid common query processing: just cache the placeholder positions for the queries passed in
	 */
	private static $compiledQueryTemplates = array();

	/**
	 * Collection of options, needed to be transfered between placeholder regular expression
	 * holder and its' callback function. Poor php, it has no delegates... :(
	 */
	private $opts = array();

	/**
	 * Passes the query to the database and fetches the set of resulting rows. If nothing to
	 * fetch, empty array is returned
	 * @param string $rawQuery
	 * @param mixed ...
	 * @see DB::rawGetRowSet
	 * @return array
	 */
	function select($rawQuery)
	{
		$args = func_get_args();
		$query = $this->obtainRawQuery($args);
		$result = $this->getDB()->rawGetRowSet($query);
		return $this->prepareResult($result);
	}

	/**
	 * Passes the query to the database and fetches a single-row result. Returns the array
	 * representing a row, or DB::NOTHING_FOUND constant if nothing is found
	 * @param string $rawQuery
	 * @param mixed ...
	 * @see DB::rawGetRow
	 * @return array
	 */
	function selectRow($query)
	{
		$args = func_get_args();
		$query = $this->obtainRawQuery($args);
		$result = $this->getDB()->rawGetRow($query);
		return $result;
	}

	/**
	 * PPasses the query to the database and fetches the first field from a single-row result. If
	 * nothing is found, DB::NOTHING_FOUND constant is returned.
	 * @param string $rawQuery
	 * @param mixed ...
	 * @see DB::rawGetCell
	 */
	function selectCell($query)
	{
		$args = func_get_args();
		$query = $this->obtainRawQuery($args);
		$result = $this->getDB()->rawGetCell($query);
		return $result;
	}

	/**
	 * Passes the query to the database and fetches the first field of each row from a set of rows.
	 * Returns the array representing the set of column values, or empty array if no rows found.
	 * @param string $rawQuery
	 * @param mixed ...
	 * @see DB::rawGetColumn
	 * @return array
	 */
	function selectColumn($query)
	{
		$args = func_get_args();
		$query = $this->obtainRawQuery($args);
		$result = $this->getDB()->rawGetColumn($query);
		return $result;
	}

	/**
	 * Sends a query and returns:
	 *  - result resource ID (for SELECT statements)
	 *  - number of affected rows (for UPDATE/DELETE statements)
	 */
	function query($query)
	{
		$args = func_get_args();
		$query = $this->obtainRawQuery($args);
		$result = $this->getDB()->rawQuery($query);
		return $result;
	}


	/**
	 * Principle SQL query processor. Prepares the query for sending, preprocesses the
	 * placaholders, caches the statement and handles the appropriate result according to the
	 * arguments
	 */
	protected function obtainRawQuery(array $args)
	{
		// Serialize is much faster than placeholder expansion. So use caching.
		$cacheCode = crc32(serialize($args));

		if ( isset(self::$compiledQueryTemplates[$cacheCode]) )
		{
			$query = self::$compiledQueryTemplates[$cacheCode];
		}
		else
		{
			$query = $this->expandPlaceholders($args);
			self::$compiledQueryTemplates[$cacheCode] = $query;
		}

		return $query;
	}

	/**
	 * Placaholder preprocessor. Initiates placeholders queries (build-in or database-native) and
	 * caches the results.
	 */
	private function expandPlaceholders(array $args)
	{
		$this->opts = array();
		$this->opts['placeholderArgs'] = array_reverse($args);

		$query = array_pop($this->opts['placeholderArgs']); // array_pop is faster than array_shift

		// Do all the work.
		$query = $this->expandPlaceholdersFlow($query);

		return $query;
	}

	private $expansionRe = null;

	private function getExpansionRe()
	{
		if ( !$this->expansionRe )
		{
			//ingore placeholders only insed the values framed by the special
			//database-specific chars
			//we need the left and the right quotes due some databases
			//use the different quoting (e.g., MSSQL quote identifiers with "[]": [table_name])
			$spc = $this->getDB()->getDialect()->quoteIdentifier("zz");
			$spc1 = preg_quote($spc{0},'{}');
			$spc2 = preg_quote($spc{1},'{}');

			$spc = $this->getDB()->getDialect()->quoteValue("zzz");
			$spc3 = preg_quote($spc{0},'{}');

			$this->expansionRe = '{
				(?>

					#Ignored chunks
					(?>
						#Comments
						-- [^\r\n]*
					)
					  |
					(?>

					    '.$spc1.' (?> [^'.$spc2.'\\\\]+|\\\\'.$spc2.'|\\\\)* '.$spc2.'  |
					    '.$spc3.' (?> [^'.$spc3.'\\\\]+|\\\\'.$spc3.'|\\\\)* '.$spc3.'  |

						#Multiline comments
						/\* .*? \*/
					)


				)
				  |
				(?>
					#Optional blocks
					\{
						( (?> (?>[^{}]+)  |  (?R) )* )             #1
					\}
				)
				  |
				(?>
					#Placeholder itself
					(\?) ( [dsafn\#]? )                           #2 #3
				)
			}sx';
		}

		return $this->expansionRe;
	}


	/**
	 * Build-in placeholder handler. Transforms the query.
	 */
	private function expandPlaceholdersFlow($query)
	{
		//skip Re if no placeholders are specified
		if ( false === strpos($query, '?') )
		{
			return $query;
		}

		$query = preg_replace_callback(
			$this->getExpansionRe(),
			array($this, 'expandPlaceholdersCallback'),
			$query);

		return $query;
	}

	/**
	 * Regexp callback method for build-in placaholder handler. Raised on each
	 * placeholder match.
	 * @todo Refactor, due this method is TOO long
	 */
	private function expandPlaceholdersCallback($m)
	{
		// Placeholder.
		if ( !empty($m[2]) )
		{
			$type = $m[3];

			// Value-based placeholder.
			if ( !$this->opts['placeholderArgs'] )
			{
				return 'PLACEHOLDER ERROR: NO_APPROPRIATE_ARGS';
			}

			$value = array_pop($this->opts['placeholderArgs']);

			// Skip this value?
			if ( $value === self::SKIP_BLOCK )
			{
				$this->opts['placeholderNoValueFound'] = true;
				return '';
			}

			// First process guaranteed non-native placeholders.
			switch( $type )
			{
				case 'a':
				{
					if ( !$value )
					{
						$this->opts['placeholderNoValueFound'] = true;
					}

					if ( !is_array($value) )
					{
						return "PLACEHOLDER ERROR: VALUE_NOT_AN_ARRAY";
					}

					$parts = array();
					foreach( $value as $k => $v )
					{
						$v = $this->getDB()->getDialect()->quoteValue($v);
						if ( !is_int($k) )
						{
							$k = $this->getDB()->getDialect()->quoteIdentifier($k);
							$parts[] = $k . "=" . $v;
						}
						else
						{
							$parts[] = $v;
						}
					}
					return join(", ", $parts);
				}

				case "#":
				{
					// Identifier.
					if ( is_array($value) )
					{
					//	$value = array_unique($value);
						$eax = array();
						foreach( $value as $prefix => $identifier )
						{
							$id = $this->getDB()->getDialect()->quoteIdentifier($identifier);
							if ( !is_numeric($prefix) )
							{
								$id = $this->getDB()->getDialect()->quoteIdentifier($prefix) .'.'.$id;
							}
							$eax[] = $id;
						}
						return implode(", ",$eax);
					}
					else
					{
						return $this->getDB()->getDialect()->quoteIdentifier($value);
					}
				}

				case 'n':
				{
					// NULL-based placeholder.
					return empty($value)? 'NULL' : $value;
				}
			}

			// In non-native mode arguments are quoted.
			if ( is_null($value) )
			{
				return 'NULL';
			}

			switch ( $type )
			{
				case '':
				{
					if ( !is_scalar($value) )
					{
						return "PLACEHOLDER ERROR: VALUE_NOT_A_SCALAR";
					}
					return $this->getDB()->getDialect()->quoteValue($value);
				}
				case 'd':
				{
					return intval($value);
				}
				case 'f':
				{
					return str_replace(',', '.', floatval($value));
				}
			}

			// By default - escape as string.
			return $this->getDB()->getDialect()->quoteValue($value);
		}

		// Optional block: { ... }
		if ( isset($m[1]) && strlen($m[1])>0 )
		{
			$block = $m[1];
			$prev  = @$this->opts['placeholderNoValueFound'];
			$block = $this->expandPlaceholdersFlow($block);
			$block = $this->opts['placeholderNoValueFound']
				? ''
				: ' ' . $block . ' ';
			$this->opts['placeholderNoValueFound'] = $prev; // recurrent-safe
			return $block;
		}

		// Default: skipped part of the string.
		return $m[0];
	}
}

?>