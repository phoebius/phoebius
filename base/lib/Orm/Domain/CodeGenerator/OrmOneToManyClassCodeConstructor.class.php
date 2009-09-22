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
class OrmOneToManyClassCodeConstructor extends ClassCodeConstructor
{
	/**
	 * @var OrmClass
	 */
	private $ormClass;

	/**
	 * @var OrmProperty
	 */
	private $ormProperty;

	/**
	 * @return OrmOneToManyClassCodeConstructor
	 */
	static function create(OrmClass $ormClass, OrmProperty $ormProperty)
	{
		return new self ($ormClass, $ormProperty);
	}

	function __construct(OrmClass $ormClass, OrmProperty $ormProperty)
	{
		$this->ormClass = $ormClass;
		$this->ormProperty = $ormProperty;
	}

	/**
	 * @return string
	 */
	function getClassName()
	{
		return ucfirst($this->ormProperty->getName());
	}

	/**
	 * @return void
	 */
	function make(IWriteStream $writeStream)
	{
		$writeStream
			->write($this->getFileHeader())
			->write($this->getClassHeader())
			->write($this->getClassBody())
			->write($this->getClassFooter())
			->write($this->getFileFooter());
	}

	/**
	 * @return void
	 */
	private function getClassBody()
	{
		return <<<EOT
	function __construct({$this->ormClass->getName()} \$parent, \$partialFetch = false)
	{
		parent::__construct(
			\$parent,
			{$this->ormClass->getName()}::map(),
			\$partialFetch
		);
	}

	/**
	 * @return OrmProperty
	 */
	function getReferentialProperty()
	{
		return {$this->ormClass->getName()}::map()->getProperty('{$this->ormProperty->getName()}');
	}
EOT;
	}

	/**
	 * @return string
	 */
	private function getClassHeader()
	{
		return <<<EOT
/**
 *
 */
abstract class {$this->getClassName()} extends OneToManyContainer
{

EOT;
	}

	/**
	 * @return string
	 */
	private function getClassFooter()
	{
		return <<<EOT

}
EOT;
	}
}

?>