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

/**
 * Represents a dialect that conforms PostgreSql SQL syntax
 * @ingroup Dal
 */
class PgSqlDialect extends LazySingleton implements IDialect
{
	private static $baseTypes = array(
		DbType::SMALL_INTEGER   => 'smallint',
		DbType::INTEGER         => 'integer',
		DbType::BIG_INTEGER     => 'bigint',

		/**
		 * Arbitrary Precision number
		 * Postgresql manual: 8.1.2. Arbitrary Precision Numbers
		 */
		DbType::NUMERIC         => 'numeric',

		/**
		 * floating point number
		 * Postgresql manual: 8.1.3. Floating-Point Types
		 */
		DbType::FLOAT           => 'float',

		DbType::STRING          => 'character varying',
		DbType::BOOLEAN         => 'boolean',

		DbType::DATE            => 'date',
		DbType::TIME            => 'time',
		DbType::DATETIME        => 'timestamp',

		DbType::INTERVAL        => 'interval',

		DbType::BINARY          => 'binary',
	);

	/**
	 * @return PgSqlDialect
	 */
	static function getInstance()
	{
		return LazySingleton::instance(__CLASS__);
	}

	/**
	 * @return DBDriver
	 */
	function getDBDriver()
	{
		return DBDriver::pgsql();
	}

	/**
	 * Quotes a string as SQL identifier
	 * @param string $identifier
	 * @return string
	 */
	function quoteIdentifier($identifier)
	{
		Assert::isScalar(
			$identifier,
			'not a scalar, but %s',
			gettype($identifier)
		);

		return '"' . str_replace('"', '""', $identifier) . '"';
	}

	/**
	 * Quotes a string as SQL value
	 * @param string $value
	 * @return string
	 */
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

	/**
	 * Gets the string that is used to concatenate database objects
	 * @return string
	 */
	function getConcatenationOperator()
	{
		return '||';
	}

	/**
	 * FIXME: cut out basic type representation casted to a base DbDialect class
	 * @return string
	 */
	function getTypeRepresentation(DBType $dbType)
	{
		$type = self::$baseTypes[$dbType->getValue()];

		if ($dbType->hasSize() && ($size = $dbType->getSize())) {
			$type .= '(' . $size . ')';
		}
		else if ($dbType->hasPrecision() && ($precision = $dbType->getPrecision())) {
			$type .= '(' . $precision;
			if ($dbType->hasScale() && ($scale = $dbType->getScale())) {
				$type .= ',' . $scale;
			}

			$type .= ')';
		}

		if ($dbType->hasTimezone()) {
			$type .= ' ' . ($dbType->withTimezeone() ? 'with' : 'without') . ' time zone';
		}

		if ($dbType->isNotNullable()) {
			$type .= ' NOT NULL';
		}

		return $type;
	}

	/**
	 * @return string
	 */
	function getSequenceName($tableName, $columnName)
	{
		return $tableName . '_' . $columnName . '_sq';
	}

	/**
	 * @return array
	 */
	function getTableQuerySet(DBTable $table)
	{
		$table = clone $table;

		$preQueries = array();
		$postQueries = array();

		foreach ($table->getColumns() as $column) {
			if ($column->getType()->isGenerated()) {
				$sqName = $this->getSequenceName($table->getName(), $column->getName());

				$preQueries[] = new PlainQuery(
					'CREATE SEQUENCE %s;',
					array(
						new SqlIdentifier($sqName)
					)
				);

				$postQueries[] = new PlainQuery(
					'ALTER SEQUENCE %s OWNED BY %s;',
					array(
						new SqlIdentifier($sqName),
						new SqlPath(
							array(
								$table->getName(),
								$column->getName()
							)
						)
					)
				);

				$column->setDefaultValue(
					SqlFunction::create('nextval')->addArg(new ScalarSqlValue($sqName))
				);
			}
		}

		foreach ($table->getConstraints() as $constraint) {
			$columns = array();

			// create indexes
			foreach ($constraint->getIndexedColumns() as $column) {
				$columns[] = $this->quoteIdentifier($column->getName());
			}

			if (!empty($columns)) {
				$postQueries[] = new PlainQuery(
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
			array(
				CreateTableQuery::create($table)
			),
			$postQueries
		);
	}
}

?>