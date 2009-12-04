<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright (c) 2009 phoebius.org
 *
 * This program is free software; you can redistribute it and/or modify it under the terms
 * of the GNU Lesser General Public License as published by the Free Software Foundation;
 * either version 3 of the License, or (at your option) any later version.
 *
 * You should have received a copy of the GNU Lesser General Public License along with
 * this program; if not, see <http://www.gnu.org/licenses/>.
 *
 ************************************************************************************************/

/**
 * sprintf-based SQL query wrapper.
 *
 * Accepts textual representation of SQL query and allows to use sprintf placeholders within a
 * query. Placeholder values should implement ISqlCastable.
 *
 * Example:
 * @code
 * $query = new RawSqlQuery('CREATE INDEX %s ON table.column', array(new SqlIdentifier("a_idx"));
 * @endcode
 *
 * @warning avoid using this class in real-wolrd applications
 *
 * @ingroup Dal_DB_Query
 */
class RawSqlQuery implements ISqlQuery
{
	/**
	 * @var string
	 */
	private $query;

	/**
	 * @var array of ISqlCastable
	 */
	private $placeholderValues = array();

	/**
	 * @param string $query textual representation of a query
	 * @param array $placeholderValues array of ISqlCastable placeholder values
	 */
	function __construct($query, array $placeholderValues = array())
	{
		Assert::isScalar($query);

		$this->query = $query;

		$this->placeholderValues = $placeholderValues;
	}

	/**
	 * Gets the textual representation of a query
	 *
	 * @return string
	 */
	function getQueryAsText()
	{
		return $this->query;
	}

	function toDialectString(IDialect $dialect)
	{
		if (empty($this->placeholderValues)) {
			return $this->query;
		}
		else {
			$args = array(
				$this->query
			);

			foreach ($this->placeholderValues as $placeholderValue) {
				$args[] = $placeholderValue->toDialectString($dialect);
			}

			return call_user_func_array('sprintf', $args);
		}
	}

	function getPlaceholderValues(IDialect $dialect)
	{
		return array ();
	}
}

?>