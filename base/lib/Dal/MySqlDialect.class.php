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
 * @ingroup Dal
 */
class MySqlDialect implements IDialect
{
	private static $baseTypes = array(
		DbType::SMALL_INTEGER   => 'smallint',
		DbType::INTEGER         => 'int',
		DbType::BIG_INTEGER     => 'bigint',

		/**
		 * Arbitrary Precision number
		 * Postgresql manual: 8.1.2. Arbitrary Precision Numbers
		 */
		DbType::NUMERIC         => 'double',

		/**
		 * floating point number
		 * Postgresql manual: 8.1.3. Floating-Point Types
		 */
		DbType::FLOAT           => 'float',

		DbType::STRING          => 'varchar',
		DbType::BOOLEAN         => 'tinyint',

		DbType::DATE            => 'date',
		DbType::TIME            => 'time',
		DbType::DATETIME        => 'timestamp',

		DbType::INTERVAL        => null,

		DbType::BINARY          => null,
	);

	/**
	 * @var MySqlDB
	 */
	private $db;

	/**
	 * @return MySqlDialect
	 */
	function __construct(MySqlDB $db)
	{
		$this->db = $db;
	}

	/**
	 * @return DBDriver
	 */
	function getDBDriver()
	{
		return DBDriver::mysql();
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

		return '`' . str_replace('`', '\\`', $identifier)  . '`';
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

		return '\'' . mysql_real_escape_string($value, $this->db->getLink()) . '\'';
	}

	/**
	 * FIXME: cut out basic type representation casted to a base DbDialect class
	 * @return string
	 */
	function getTypeRepresentation(DBType $dbType)
	{
		Assert::isTrue(
			isset(
				self::$baseTypes[$dbType->getValue()]
			)
		);

		$type = self::$baseTypes[$dbType->getValue()];

		if ($dbType->hasSize() && ($size = $dbType->getSize())) {
			$type .= '(' . $size . ')';
		}
		else if ($dbType->is(DBType::STRING)) {
			$type = 'text';
		}
		else if ($dbType->is(DBType::BOOLEAN)) {
			$type = 'tinyint(1) UNSIGNED';
		}
		else if ($dbType->hasPrecision() && ($precision = $dbType->getPrecision())) {
			$type .= '(' . $precision;
			if ($dbType->hasScale() && ($scale = $dbType->getScale())) {
				$type .= ',' . $scale;
			}

			$type .= ')';
		}

		if ($dbType->isUnsigned()) {
			$type .= ' UNSIGNED';
		}

		if ($dbType->isNotNullable()) {
			$type .= ' NOT NULL';
		}

		if ($dbType->isGenerated()) {
			$autoIncrementingTypes = array(DBType::SMALL_INTEGER, DBType::INTEGER, DBType::BIG_INTEGER);

			if (in_array($dbType->getValue(), $autoIncrementingTypes)) {
				$type .= ' AUTO_INCREMENT';
			}
		}

		return $type;
	}

	/**
	 * @return array
	 */
	function getTableQuerySet(DBTable $table)
	{
		$table = clone $table;

		$queries = array(
			CreateTableQuery::create($table)
		);

		foreach ($table->getColumns() as $column) {
			if ($column->getType()->isGenerated()) {
				// AUTO_INCREMENT
				$column->setDefaultValue(null);
			}
		}

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

		return array_merge(
			$queries
		);
	}
}

?>