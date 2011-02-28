<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright (c) 2011 Scand Ltd.
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
 * Represents a database diff
 *
 * @ingroup Dal_DB_Schema
 */
final class DBDiff implements ISqlCastable
{
	private $newTables = array();
	private $missingTables = array();
	
	private $newColumns = array();
	private $missingColumns = array();
	
	private $newConstraints = array();
	private $missingConstraints = array();
	
	private $newIndexes = array();
	private $missingIndexes = array();
	
	function make(DBSchema $from, DBSchema $to)
	{
		// Firstly, process tables
		// Then, for each table process columns, constraints, indexes
	}
	
	function apply(DBSchema $schema)
	{
		Assert::notImplemented("missing DBTable.drop{Column,Constraint,Index}");
	}
	
	function clear()
	{
		Assert::notImplemented("clean all internal arrays");
	}
	
	function swap()
	{
		Assert::notImplemented("reverse diff direction");
	}
	
	function toDialectString(IDialect $dialect)
	{
		Assert::notImplemented("generate alter queries according to diff");
	}
}
