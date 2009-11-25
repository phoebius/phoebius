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
 * @ingroup Orm_Domain_Meta
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
	 * @return DBSchemaBuilder
	 */
	static function create(OrmDomain $ormDomain, DBSchema $dbSchema = null)
	{
		return new self ($ormDomain, $dbSchema);
	}

	function __construct(OrmDomain $ormDomain, DBSchema $dbSchema = null)
	{
		$this->ormDomain = $ormDomain;
		$this->dbSchema =
			$dbSchema
				? $dbSchema
				: new DBSchema();
	}

	/**
	 * @return DBSchema
	 */
	function build()
	{
		foreach ($this->ormDomain->getClasses() as $class) {
			$this->ormClass = $class;
			$this->importClass($class);
			$this->ormClass = null;
		}

		foreach ($this->ormDomain->getClasses() as $class) {
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
		$this->dbTable = new DBTable();
		$this->dbTable->setName($this->ormClass->getTable());

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

		$dbColumns = array();
		foreach ($columns as $name => $dbType) {
			$dbColumns[$name] = new DBColumn($name, $dbType);
		}

		$this->dbTable->addColumns($dbColumns);

		//Assert::notImplemented('broken: AssociationPropertyType not found by fetcher');
		if ($this->ormProperty->getType() instanceof AssociationPropertyType) {
			$this->dbTable->addConstraint(
				new DBOneToOneConstraint(
					$dbColumns,
					$this->dbSchema->getTable($this->ormProperty->getType()->getContainer()->getTable()),
					$this->ormProperty->getType()->getAssociationBreakAction()
				)
			);
		}

		if ($this->ormProperty === $this->ormIdentifier) {
			$this->dbTable->addConstraint(
				new DBPrimaryKeyConstraint($dbColumns)
			);
		}
		else if ($this->ormProperty->isUnique()) {
			$this->dbTable->addConstraint(
				new DBUniqueConstraint($dbColumns)
			);
		}
	}
}

?>