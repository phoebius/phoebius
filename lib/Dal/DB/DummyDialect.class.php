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
 * Represents a dummy dialect which does not depend on any DB
 *
 * @ingroup Dal_DB
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

	function getDBDriver()
	{
		return new DBDriver(DBDriver::DUMMY);
	}

	function quoteIdentifier($identifier)
	{
		Assert::isScalar($identifier);

		return '"' . str_replace('"', '""', $identifier) . '"';
	}

	function quoteValue($value)
	{
		Assert::isScalarOrNull($value);

		if (is_null($value)) {
			return 'NULL';
		}

		return "'" . str_replace("'", "''", $value) . "'";
	}

	function getTypeRepresentation(DBType $dbType)
	{
		$type = $dbType->getId();

		if (($size = $dbType->getSize())) {
			$type .= '(' . $size. ')';
		}

		if (($precision = $dbType->getPrecision())) {
			$type .= '(' . $precision;
			if (($scale = $dbType->getScale())) {
				$type .= ',' . $scale;
			}

			$type .= ')';
		}

		if (!$dbType->isNullable()) {
			$type .= ' NOT NULL';
		}

		if ($dbType->isGenerated()) {
			$type .= ' GENERATED';
		}

		return $type;
	}

	function getTableQuerySet(DBTable $table)
	{
		return array(
			CreateTableQuery::create($table)
		);
	}
}

?>