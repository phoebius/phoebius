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
 * Represents a dumper of object representation of database schema
 *
 * @ingroup Orm_Domain_CodeGenerator
 */
class SqlSchemaConstructor
{
	/**
	 * @var DBSchema
	 */
	private $dbSchema;

	/**
	 * @param DBSchema $dbSchema object representation of database schema
	 */
	function __construct(DBSchema $dbSchema)
	{
		$this->dbSchema = $dbSchema;
	}

	/**
	 * Dumps schema
	 *
	 * @param IOutput $writeStream stream to write the dump to
	 * @param IDialect $dialect database dialect to use
	 *
	 * @return void
	 */
	function make(IOutput $writeStream, IDialect $dialect)
	{
		$now = date('d.m.y H:i');
		$product = PHOEBIUS_FULL_PRODUCT_NAME;

		$start = <<<EOT
--
-- {$product}
-- Generated at {$now} for {$dialect->getDBDriver()->getValue()}
--


EOT;

		$writeStream
			->write($start)
			->write($this->dbSchema->toDialectString($dialect));
	}
}

?>