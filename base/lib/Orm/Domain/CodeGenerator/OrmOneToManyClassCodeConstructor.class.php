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
		$this->ormProperty = $ormProperty;

		parent::__construct($ormClass);
	}

	/**
	 * @return boolean
	 */
	function isPublicEditable()
	{
		return false;
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
	protected function findMembers()
	{
		$this->classMethods[] = <<<EOT
	function __construct({$this->ormClass->getName()} \$parent)
	{
		parent::__construct(
			\$parent,
			{$this->ormClass->getName()}::map()
		);
	}
EOT;

		$this->classMethods[] = <<<EOT
	/**
	 * @return OrmProperty
	 */
	function getReferentialProperty()
	{
		return {$this->ormClass->getName()}::map()->getProperty('{$this->ormProperty->getName()}');
	}
EOT;
	}

	protected function getClassType()
	{
		return 'abstract';
	}

	protected function getExtendsClassName()
	{
		return 'OneToManyContainer';
	}
}

?>