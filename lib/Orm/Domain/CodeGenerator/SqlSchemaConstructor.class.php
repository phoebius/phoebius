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
 * @ingroup Orm_Domain_CodeGenerator
 */
class SqlSchemaConstructor
{
	/**
	 * @var DBSchema
	 */
	private $dbSchema;

	/**
	 * @return SqlSchemaConstructor
	 */
	static function create(DBSchema $dbSchema)
	{
		return new self ($dbSchema);
	}

	function __construct(DBSchema $dbSchema)
	{
		$this->dbSchema = $dbSchema;
	}

	/**
	 * @return void
	 */
	function make(IWriteStream $writeStream, IDialect $dialect)
	{
		$now = date('d.m.y H:i');

		$start = <<<EOT
--
-- Phoebius Framework Autogenerator
-- Generated at {$now} for {$dialect->getDBDriver()->getValue()}
--

EOT;

		$writeStream
			->write($start)
			->write(
				$this->dbSchema->toDialectString($dialect)
			);
	}
}

?>