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
class OrmClassCodeConstructor extends CodeConstructor
{
	/**
	 * @var OrmClass
	 */
	private $ormClass;

	/**
	 * @var OrmAutoClassCodeConstructor
	 */
	private $auto;

	/**
	 * @return OrmClassCodeConstructor
	 */
	static function create(OrmClass $ormClass, OrmAutoClassCodeConstructor $auto)
	{
		return new self ($ormClass, $auto);
	}

	function __construct(OrmClass $ormClass, OrmAutoClassCodeConstructor $auto)
	{
		$this->ormClass = $ormClass;
		$this->auto = $auto;
	}

	/**
	 * @return void
	 */
	function make(IWriteStream $writeStream)
	{
		$class = <<<EOT
/**
 *
 */
class {$this->ormClass->getName()} extends {$this->auto->getClassName()}
{
	// nothing here
}
EOT;

		$writeStream
			->write($this->getFileHeader('This file won\'t be regenerated explicitly'))
			->write($class)
			->write($this->getFileFooter());
	}
}

?>