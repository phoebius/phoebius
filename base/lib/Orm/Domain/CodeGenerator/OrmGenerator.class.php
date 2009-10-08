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
		if (!($schemaName = $ormDomain->getName())) {
			$schemaName = 'db-schema';
		}

		$dbSchema = DBSchemaImporter::create()->import($ormDomain, new DBSchema());

		DbSchemaCodeConstructor
			::create($dbSchema)
			->make(
				new FileWriteStream(
					$this->schemaDir . DIRECTORY_SEPARATOR . $schemaName . '.php'
				)
			);

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
						$this->schemaDir . DIRECTORY_SEPARATOR . $schemaName . '.sql'
					),
					$db->getDialect()
				);
		}

	}

	/**
	 * @return void
	 */
	private function generateClassFiles(OrmClass $class)
	{
		$auto = new OrmAutoClassCodeConstructor($class);
		$auto->make(
			new FileWriteStream(
				$this->autoClassDir . DIRECTORY_SEPARATOR . $auto->getClassName() . PHOEBIUS_TYPE_EXTENSION
			)
		);

		$publicFile = $this->publicClassDir . DIRECTORY_SEPARATOR . $class->getName() . PHOEBIUS_TYPE_EXTENSION;
		if (!file_exists($publicFile) || $this->regeneratePublic) {
			OrmClassCodeConstructor::create($class, $auto)
				->make(
					new FileWriteStream(
						$publicFile
					)
				);
		}

		foreach ($class->getProperties() as $property) {
			if ($property->getType() instanceof OneToManyContainerPropertyType) {
				$publicFile = $this->publicClassDir . DIRECTORY_SEPARATOR . ucfirst($property->getName()) . PHOEBIUS_TYPE_EXTENSION;
				if (!file_exists($publicFile) || $this->regeneratePublic) {
					OrmOneToManyClassCodeConstructor::create($class, $property)
						->make(
							new FileWriteStream(
								$publicFile
							)
						);
				}
			}
			else if ($property->getType() instanceof ManyToManyContainerPropertyType) {
				$publicFile = $this->publicClassDir . DIRECTORY_SEPARATOR . ucfirst($property->getName()) . PHOEBIUS_TYPE_EXTENSION;
				if (!file_exists($publicFile) || $this->regeneratePublic) {
					OrmManyToManyClassCodeConstructor::create($class, $property)
						->make(
							new FileWriteStream(
								$publicFile
							)
						);
				}
			}
		}
	}
}

?>