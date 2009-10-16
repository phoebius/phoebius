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
 * @ingroup OrmCodeGenerator
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

	function __construct($schemaDir, $autoClassDir, $publicClassDir)
	{
		Assert::isScalar($schemaDir);
		Assert::isScalar($autoClassDir);
		Assert::isScalar($publicClassDir);

		foreach (array('schemaDir', 'autoClassDir', 'publicClassDir') as $_) {
			$$_ = realpath($$_);
			if (!is_dir($$_)) {
				throw new ArgumentException($_);
			}
			else {
				$this->$_ = $$_;
			}
		}
	}

	/**
	 * @return string
	 */
	function getSchemaDir()
	{
		return $this->schemaDir;
	}

	/**
	 * @return string
	 */
	function getAutoClassDir()
	{
		return $this->autoClassDir;
	}

	/**
	 * @return string
	 */
	function getPublicClassDir()
	{
		return $this->publicClassDir;
	}

	/**
	 * @return OrmGenerator
	 */
	function regeneratePublic()
	{
		$this->regeneratePublic = true;

		return $this;
	}

	/**
	 * @return OrmGenerator
	 */
	function skipPublic()
	{
		$this->regeneratePublic = false;

		return $this;
	}

	/**
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
		$dbSchema = DBSchemaBuilder::create($ormDomain)->build();

		if (($dbSchemaName = $ormDomain->getDbSchema())) {
			try {
				$db = DBPool::get($dbSchemaName);
			}
			catch (ArgumentException $e){
				return;
			}

			SqlSchemaConstructor::create($dbSchema)
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
		$this->buildClass(new OrmPhysicalSchemaClassCodeConstructor($class));
		$this->buildClass(new OrmAutoEntityClassCodeConstructor($class));
		$this->buildClass(new OrmEntityClassCodeConstructor($class));

		foreach ($class->getProperties() as $property) {
			$type = $property->getType();

			if ($type instanceof OneToManyContainerPropertyType) {
				$this->buildClass(new OrmOneToManyClassCodeConstructor($class, $property));
			}
			else if ($type instanceof ManyToManyContainerPropertyType) {
				$this->buildClass(new OrmManyToManyClassCodeConstructor($class, $property));
			}
		}
	}
}

?>