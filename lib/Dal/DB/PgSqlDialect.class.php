<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright (c) 2009 Scand Ltd.
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
 * Represents a dialect that conforms PostgreSql SQL syntax
 * @ingroup Dal_DB
 */
class PgSqlDialect extends Dialect
{
	private static $baseTypes = array(
		// primitive
		DBType::BOOLEAN => 'boolean',

		// integers
		DBType::INT16 => 'int2',
		DBType::INT32 => 'int4',
		DBType::INT64 => 'int8',

		// unsigned integers
		DBType::UINT16 => 'int2',
		DBType::UINT32 => 'int4',
		DBType::UINT64 => 'int8',

		// floating-point
		DBType::CURRENCY => 'decimal',
		DBType::DECIMAL => 'decimal',
		DBType::FLOAT => 'float',

		// string
		DBType::BINARY => 'binary',
		DBType::CHAR => 'char',
		DBType::VARCHAR => 'character varying',

		// date and time
		DBType::DATE => 'date',
		DBType::TIME => 'time',
		DBType::DATETIME => 'timestamp',
	);

	function __construct()
	{
		foreach (self::$baseTypes as $baseType => $impl) {
			$this->registerType($baseType, $impl);
		}
	}

	function getDBDriver()
	{
		return DBDriver::pgsql();
	}

	function quoteIdentifier($identifier)
	{
		Assert::isScalar(
			$identifier,
			'not a scalar, but %s',
			gettype($identifier)
		);

		return '"' . str_replace('"', '""', $identifier) . '"';
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

		if (is_bool($value)) {
			$value =
				$value
					? 't'
					: 'f';
		}

		return "'" . pg_escape_string($value)  . "'";
	}

	function getTableQuerySet(DBTable $table)
	{
		$table = clone $table;

		$preQueries = array();
		$postQueries = array();

		foreach ($table->getColumns() as $column) {
			$type = $column->getType();
			if ($type instanceof DBType && $type->isGenerated()) {
				$sqName = $this->getSequenceName($table->getName(), $column->getName());

				$preQueries[] = new RawSqlQuery(
					'CREATE SEQUENCE %s;',
					array(
						new SqlIdentifier($sqName)
					)
				);

				$postQueries[] = new RawSqlQuery(
					'ALTER SEQUENCE %s OWNED BY %s;',
					array(
						new SqlIdentifier($sqName),
						new SqlPath($table->getName(), $column->getName())
					)
				);

				$column->setDefaultValue(
					new SqlFunction('nextval', new SqlValue($sqName))
				);
			}
		}

		foreach ($table->getConstraints() as $constraint) {
			$columns = array();

			// create indexes
			foreach ($constraint->getIndexableFields() as $field) {
				$columns[] = $this->quoteIdentifier($field);
			}

			if (!empty($columns)) {
				$postQueries[] = new RawSqlQuery(
					'CREATE INDEX %s ON %s (' . join($columns) . ');',
					array(
						new SqlIdentifier($constraint->getName() . '_idx'),
						new SqlIdentifier($table->getName())
					)
				);
			}
		}

		return array_merge(
			$preQueries,
			array(new CreateTableQuery($table)),
			$postQueries
		);
	}

	/**
	 * Generates the name of the seq
	 *
	 * @return string
	 */
	function getSequenceName($tableName, $columnName)
	{
		return $tableName . '_' . $columnName . '_sq';
	}
}

?>