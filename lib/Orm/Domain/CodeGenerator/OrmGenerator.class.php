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
 * Represents a general-purpose generator of auxiliary classes for ORM-related entities
 *
 * @ingroup Orm_Domain_CodeGenerator
 */
class OrmGenerator
{
	/**
	 * @var boolean
	 */
	private $regeneratePublic = false;

	/**
	 * @var string
	 */
	private $schemaDir;

	/**
	 * @var string
	 */
	private $autoClassDir;

	/**
	 * @var string
	 */
	private $publicClassDir;

	/**
	 * @param string $schemaDir path to a directory where sql shema should be located
	 * @param $autoClassDir path to a directory where autogenerated classes should be located
	 * @param $publicClassDir path to a directory where public classes (that won't be
	 * 							regenerated implicitly) should be located
	 */
	function __construct($schemaDir, $autoClassDir, $publicClassDir)
	{
		Assert::isScalar($schemaDir);
		Assert::isScalar($autoClassDir);
		Assert::isScalar($publicClassDir);

		foreach (array('schemaDir', 'autoClassDir', 'publicClassDir') as $_) {
			$path = realpath($$_);
			if (!is_dir($path)) {
				throw new ArgumentException($_);
			}
			else {
				$this->{$_} = $path;
			}
		}
	}

	/**
	 * Gets a path to a directory where sql shema should be located
	 *
	 * @return string
	 */
	function getSchemaDir()
	{
		return $this->schemaDir;
	}

	/**
	 * Gets a path to a directory where autogenerated classes should be located
	 *
	 * @return string
	 */
	function getAutoClassDir()
	{
		return $this->autoClassDir;
	}

	/**
	 * Gets a path to a directory where public classes (that won't be regenerated implicitly)
	 * should be located
	 *
	 * @return string
	 */
	function getPublicClassDir()
	{
		return $this->publicClassDir;
	}

	/**
	 * Forces regeneration of public files
	 *
	 * @return OrmGenerator
	 */
	function regeneratePublic()
	{
		$this->regeneratePublic = true;

		return $this;
	}

	/**
	 * Requres generator to avoid regeration of public files
	 *
	 * @return OrmGenerator
	 */
	function skipPublic()
	{
		$this->regeneratePublic = false;

		return $this;
	}

	/**
	 * Generats auxiliary classes for ORM-related entities defined within the domain
	 *
	 * @param OrmDomain $ormDomain
	 *
	 * @return void
	 */
	function generate(OrmDomain $ormDomain)
	{
		$this->generateDbSchema($ormDomain);

		foreach ($ormDomain->getClasses() as $class) {
			$this->generateClassFiles($class);
		}
	}

	/**
	 * @return void
	 */
	private function generateDbSchema(OrmDomain $ormDomain)
	{
		$schema = new DBSchemaBuilder($ormDomain);
		$dbSchema = $schema->build();

		if (($dbSchemaName = $ormDomain->getDbSchema())) {
			try {
				$db = DBPool::get($dbSchemaName);
			}
			catch (ArgumentException $e){
				return;
			}

			$schema = new SqlSchemaConstructor($dbSchema);

			$schema
				->make(
					new FileWriteStream(
						$this->schemaDir . DIRECTORY_SEPARATOR . $dbSchemaName. '.sql' // FIXME use OrmDomain->name here instead of dbSchemaName
					),
					$db->getDialect()
				);
		}

	}

	/**
	 * @return void
	 */
	private function buildClass(ClassCodeConstructor $ccc)
	{
		$path =
			(
				$ccc->isPublicEditable()
					? $this->publicClassDir
					: $this->autoClassDir
			)
			. DIRECTORY_SEPARATOR
			. $ccc->getClassName()
			. PHOEBIUS_TYPE_EXTENSION;

		if (
				!$ccc->isPublicEditable()
				|| !file_exists($path)
				|| $this->regeneratePublic
		) {
			$ccc->make(new FileWriteStream($path));
		}
	}

	/**
	 * @return void
	 */
	private function generateClassFiles(OrmClass $class)
	{
		$this->buildClass(new OrmAutoClassCodeConstructor($class));
		$this->buildClass(new OrmClassCodeConstructor($class));

		$this->buildClass(new OrmLogicalSchemaClassCodeConstructor($class));

		if ($class->hasDao()) {
			$this->buildClass(new OrmPhysicalSchemaClassCodeConstructor($class));
		}

		$this->buildClass(new OrmAutoEntityClassCodeConstructor($class));
		$this->buildClass(new OrmEntityClassCodeConstructor($class));

		$this->generateContainerFiles($class);
	}

	/**
	 * @return void
	 */
	private function generateContainerFiles(OrmClass $class)
	{
		foreach ($class->getProperties() as $property) {
			$type = $property->getType();

			if ($type instanceof OneToManyContainerPropertyType) {
				$this->buildClass(new OrmOneToManyAutoClassCodeConstructor($class, $property));
				$this->buildClass(new OrmOneToManyClassCodeConstructor($class, $property));
			}
			else if ($type instanceof ManyToManyContainerPropertyType) {
				$this->buildClass(new OrmManyToManyAutoClassCodeConstructor($class, $property));
				$this->buildClass(new OrmManyToManyClassCodeConstructor($class, $property));
			}
		}
	}
}

?>