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
 * Represents a dummy dialect
 * @ingroup Dal
 */
final class DummyDialect extends LazySingleton implements IDialect
{
	/**
	 * Gets the instance of the singleton class
	 * @return DummyDialect
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
		return new DBDriver(DBDriver::DUMMY);
	}

	/**
	 * Quotes a string as SQL identifier
	 * @param string $identifier
	 * @return string
	 */
	function quoteIdentifier($identifier)
	{
		Assert::isScalar($identifier);

		return '"' . str_replace('"', '""', $identifier) . '"';
	}

	/**
	 * Quotes a string as SQL value
	 * @param string $value
	 * @return string
	 */
	function quoteValue($value)
	{
		Assert::isScalarOrNull($value);

		if (is_null($value)) {
			return 'NULL';
		}

		return "'" . str_replace("'", "''", $value) . "'";
	}

	/**
	 * @return string
	 */
	function getTypeRepresentation(DBType $dbType)
	{
		$type = $dbType->getId();

		if ($dbType->hasSize()) {
			$type .= '(' . $dbType->getSize() . ')';
		}

		if ($dbType->hasPrecision()) {
			$type .= '(' . $dbType->getPrecision();
			if ($dbType->getScale()) {
				$type .= ',' . $dbType->getScale();
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
}

?>