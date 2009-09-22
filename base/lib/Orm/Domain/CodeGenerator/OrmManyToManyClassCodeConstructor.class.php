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
class OrmManyToManyClassCodeConstructor extends ClassCodeConstructor
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
	 * @return OrmManyToManyClassCodeConstructor
	 */
	static function create(OrmProperty $ormProperty)
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
			{$this->ormClass->getName()}::orm()->getProperty('{$this->ormProperty->getName()}'),
			\$partialFetch
		);
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
abstract class {$this->getClassName()} extends ManyToManyContainer
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