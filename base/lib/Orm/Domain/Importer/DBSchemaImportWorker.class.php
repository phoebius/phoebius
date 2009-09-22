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
 * @ingroup OrmDomainImporter
 * @internal
 */
final class DBSchemaImportWorker
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
	 * @return DBSchemaImportWorker
	 */
	static function create(OrmDomain $ormDomain, DBSchema $dbSchema)
	{
		return new self ($ormDomain, $dbSchema);
	}

	function __construct(OrmDomain $ormDomain, DBSchema $dbSchema)
	{
		$this->ormDomain = $ormDomain;
		$this->dbSchema = $dbSchema;
	}

	/**
	 * @return void
	 */
	function import()
	{
		foreach ($this->ormDomain->getClasses() as $class) {
			$this->ormClass = $class;
			$this->importClass($class);
			$this->ormClass = null;
		}

		foreach ($this->ormDomain->getClasses() as $class) {
			$this->ormClass = $class;
			$this->dbTable = $this->dbSchema->getTable($this->ormClass->getDBTableName());
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
	}

	/**
	 * @return void
	 */
	private function importClass()
	{
		$this->dbTable = new DBTable();
		$this->dbTable->setName($this->ormClass->getDBTableName());

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
		$columns = $this->ormClass->getDbColumns($this->ormProperty);
		if (empty($columns)) {
			// columnless properties are skipped
			return;
		}

		$dbColumns = array();
		foreach ($columns as $name => $dbType) {
			$dbColumn = new DBColumn();
			$dbColumn->setName($name);
			$dbColumn->setType($dbType);

			$dbColumns[$name] = $dbColumn;
		}

		$this->dbTable->addColumns($dbColumns);

		//Assert::notImplemented('broken: AssociationPropertyType not found by fetcher');
		if ($this->ormProperty->getType() instanceof AssociationPropertyType) {
			$this->dbTable->addConstraint(
				new DBOneToOneConstraint(
					$dbColumns,
					$this->dbSchema->getTable($this->ormProperty->getType()->getContainer()->getDBTableName()),
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