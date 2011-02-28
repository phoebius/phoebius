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
 * Represents a builder for creating an object representation of database schema based on graph
 * of ORM entites (aka OrmClass)
 *
 * @ingroup Orm_Domain_Notation
 */
class DBSchemaBuilder
{
	/**
	 * @var OrmDomain
	 */
	private $ormDomain;

	/**
	 * @var DBSchema
	 */
	private $dbSchema;

	/**
	 * @var DBTable
	 */
	private $dbTable;

	/**
	 * @var OrmClass
	 */
	private $ormClass;

	/**
	 * @var OrmProperty
	 */
	private $ormIdentifier;

	/**
	 * @var OrmProperty
	 */
	private $ormProperty;

	/**
	 * @param OrmDomain $ormDomain all we need to know is a graph or ORM-related entity' internals
	 */
	function __construct(OrmDomain $ormDomain)
	{
		$this->ormDomain = $ormDomain;
		$this->dbSchema = new DBSchema();
	}

	/**
	 * Creates an DBSchema object - an object representation of database schema based on ORM-related
	 * entity graph
	 *
	 * @return DBSchema
	 */
	function build()
	{
		foreach ($this->ormDomain->getClasses() as $class) {
			if (!$class->hasDao()) {
				continue;
			}

			$this->ormClass = $class;
			$this->importClass($class);
			$this->ormClass = null;
		}

		foreach ($this->ormDomain->getClasses() as $class) {
			if (!$class->hasDao()) {
				continue;
			}

			$this->ormClass = $class;
			$this->dbTable = $this->dbSchema->getTable($this->ormClass->getTable());
			$this->ormIdentifier = $class->getIdentifier();
			foreach ($class->getProperties() as $property) {
				if ($this->ormIdentifier !== $property) {
					$this->ormProperty = $property;
					$this->importProperty();
					$this->ormProperty = null;
				}
			}
			$this->ormClass = $class;
		}

		return $this->dbSchema;
	}

	/**
	 * @return void
	 */
	private function importClass()
	{
		$this->dbTable = new DBTable($this->ormClass->getTable());

		if (($this->ormIdentifier = $this->ormClass->getIdentifier())) {
			$this->ormProperty = $this->ormIdentifier;
			$this->importProperty();
			$this->ormProperty = null;
		}

		$this->dbSchema->addTable($this->dbTable);
		$this->dbTable = null;
	}

	/**
	 * @return void
	 */
	private function importProperty()
	{
		if (!sizeof($this->ormProperty->getFields())) {
			// columnless properties are skipped
			return;
		}

		$columns = array_combine(
			$this->ormProperty->getFields(),
			$this->ormProperty->getType()->getSqlTypes()
		);

		foreach ($columns as $name => $dbType) {
			$this->dbTable->addColumn(new DBColumn($name, $dbType));
		}
		
		// now add constraints and indexes
		
		$this->importConstraints($this->ormProperty);
	}
	
	private function importConstraints(OrmProperty $property)
	{
		$fields = $property->getFields();
		
		if ($property->isIdentifier()) {
			$this->dbTable->addConstraint(
				new DBPrimaryKeyConstraint($fields, $property->getName() . '_pk')
			);
		}
		else if ($property->isUnique()) {
			$this->dbTable->addConstraint(
				new DBUniqueConstraint($fields, $property->getName() . '_uq')
			);
		}

		$type = $property->getType();
		if ($type instanceof AssociationPropertyType) {
			$this->dbTable->addConstraint(
				new DBOneToOneConstraint(
					$fields,
					$this->dbSchema->getTable($property->getType()->getContainer()->getTable()),
					$property->getType()->getAssociationBreakAction(),
					$property->getName() . '_fk'
				)
			);
		}
		else if ($type instanceof CompositePropertyType) {
			foreach ($type->getProperties($property) as $_property) {
				$this->importConstraints($_property);
			}
		}
		
		if ($property->isQueryable()) {
			$this->dbTable->addIndex(
				new DBIndex($fields, $property->getName() . '_idx')
			);
		}
	}
}

?>