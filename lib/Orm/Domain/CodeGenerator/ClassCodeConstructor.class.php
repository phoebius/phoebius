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
 * @ingroup Orm_Domain_CodeGenerator
 */
abstract class ClassCodeConstructor extends CodeConstructor
{
	const NEW_LINE = "\n";

	/**
	 * @var OrmClass
	 */
	protected $ormClass;

	protected $classProperties = array();
	protected $classMethods = array();

	/**
	 * @return string
	 */
	abstract function getClassName();

	/**
	 * @return boolean
	 */
	abstract function isPublicEditable();

	function __construct(OrmClass $ormClass)
	{
		$this->ormClass = $ormClass;
	}

	/**
	 * @return void
	 */
	function make(IOutput $writeStream)
	{
		$this->classMethods = array();
		$this->classProperties = array();

		$this->findMembers();

		$writeStream
			->write($this->getFileHeader())
			->write($this->getClassHeader())
			->write($this->getClassBody())
			->write($this->getClassFooter())
			->write($this->getFileFooter());
	}

	protected function findMembers()
	{
		// nothing here
	}

	/**
	 * @return ClassCodeConstructor
	 */
	protected function addMethod($phpCode)
	{
		$this->classMethods[] = $phpCode;

		return $this;
	}

	/**
	 * @return ClassCodeConstructor
	 */
	protected function addProperty($phpCode)
	{
		$this->classProperties[] = $phpCode;

		return $this;
	}

	/**
	 * @return string final|abstract|null
	 */
	protected function getClassType()
	{
		return null;
	}

	protected function getExtendsClassName()
	{
		return null;
	}

	protected function getImplementsInterfaceNames()
	{
		return array();
	}

	protected function getClassBody()
	{
		return
			join(self::NEW_LINE . self::NEW_LINE, $this->classProperties)
			. (!empty($this->classProperties) && !empty($this->classMethods)
				? self::NEW_LINE . self::NEW_LINE
				: ''
			)
			. join(self::NEW_LINE . self::NEW_LINE, $this->classMethods);
	}

	protected function getClassComment()
	{
		return <<<EOT
/**
 *
 */
EOT;
	}

	/**
	 * @return string
	 */
	protected function getClassHeader()
	{
		if (($type = $this->getClassType())) {
			$type .= ' ';
		}

		if (($extends = $this->getExtendsClassName())) {
			$extends = ' extends ' . $extends;
		}
		else {
			$extends = '';
		}

		$interfaces = $this->getImplementsInterfaceNames();
		if (!empty($interfaces)) {
			$implements = ' implements ' . join(', ', $interfaces);
		}
		else {
			$implements = '';
		}

		return <<<EOT
{$this->getClassComment()}
{$type}class {$this->getClassName()}{$extends}{$implements}
{

EOT;
	}

	/**
	 * @return string
	 */
	protected function getClassFooter()
	{
		return <<<EOT

}
EOT;
	}
}

?>