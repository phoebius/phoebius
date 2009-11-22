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
 * @ingroup Dal_DB
 */
class MySqlDialect extends Dialect
{
	private static $baseTypes = array(
		// primitive
		//DBType::BOOLEAN => 'TINYINT', // handled manually

		// integers
		DBType::INT16 => 'MEDIUMINT',
		DBType::INT32 => 'INTEGER',
		DBType::INT64 => 'BIGINT',

		// unsigned integers
		DBType::UINT16 => 'MEDIUMINT UNSIGNED',
		DBType::UINT32 => 'INTEGER UNSIGNED',
		DBType::UINT64 => 'BIGINT UNSIGNED',

		// floating-point
		DBType::CURRENCY => 'MONEY',
		DBType::DECIMAL => 'NUMERIC',
		DBType::FLOAT => 'decimal',

		// string
		DBType::BINARY => 'BLOB',
		DBType::CHAR => 'CHAR',
		DBType::VARCHAR => 'VARCHAR',

		// date and time
		DBType::DATE => 'DATE',
		DBType::TIME => 'TIME',
		DBType::DATETIME => 'DATETIME',
	);

	/**
	 * @var resource
	 */
	private $link;

	/**
	 * @param resource mysql link
	 */
	function __construct($link = null)
	{
		$this->link = $link;

		foreach (self::$baseTypes as $baseType => $impl) {
			$this->registerType($baseType, $impl);
		}
	}

	function getDBDriver()
	{
		return DBDriver::mysql();
	}

	function quoteIdentifier($identifier)
	{
		Assert::isScalar(
			$identifier,
			'not a scalar, but %s',
			gettype($identifier)
		);

		return '`' . str_replace('`', '\\`', $identifier)  . '`';
	}

	function quoteValue($value)
	{
		Assert::isScalarOrNull(
			$value,
			'not a scalar, but %s',
			gettype($value)
		);

		if (is_null($value)) {
			return 'NULL';
		}

		return '\'' . mysql_real_escape_string($value, $this->link) . '\'';
	}

	function getTypeRepresentation(DBType $dbType)
	{
		switch ($dbType->getValue()) {
			case DBType::BOOLEAN: {
				return $this->compute('TINYINT(1) UNSIGNED', $dbType->isNullable());
			}

			case DBType::BINARY: {
				$size = $dbType->getSize();

				if ($size < 65535) {
					$customType = 'BLOB';
				}
				else if ($size < 16777215) {
					$customType = 'MEDIUMBLOB';
				}
				else {
					$customType = 'LONGTBLOB';
				}

				return $this->compute($customType, $dbType->isNullable());
			}

			case DBType::VARCHAR: {
				$size = $dbType->getSize();

				if (!$size) {
					$dbType->setSize(255);
				}
				else {
					if ($size < 255) {
						 break;
					}
					else if ($size < 65535) {
						$customType = 'TEXT';
					}
					else if ($size < 16777215) {
						$customType = 'MEDIUMTEXT';
					}
					else {
						$customType = 'LONGTEXT';
					}

					return $this->compute($customType, $dbType->isNullable());
				}
			}
		}

		$type = parent::getTypeRepresentation($dbType);

		switch ($dbType->getValue()) {
			case DBType::UINT16:
			case DBType::UINT32:
			case DBType::UINT64: {
				if ($dbType->isGenerated()) {
					$type .= ' AUTO_INCREMENT';
				}
			}
		}

		return $type;
	}

	function getTableQuerySet(DBTable $table)
	{
		$table = clone $table;

		$queries = array(
			CreateTableQuery::create($table)
		);

		foreach ($table->getConstraints() as $constraint) {
			$columns = array();

			// create indexes
			foreach ($constraint->getIndexedColumns() as $column) {
				$columns[] = $this->quoteIdentifier($column->getName());
			}

			if (!empty($columns)) {
				$queries[] = new PlainQuery(
					'CREATE INDEX %s ON %s (' . join($columns) . ');',
					array(
						new SqlIdentifier($constraint->getName() . '_idx'),
						new SqlIdentifier($table->getName())
					)
				);
			}
		}

		return $queries;
	}
}

?>