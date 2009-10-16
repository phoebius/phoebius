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
 * @ingroup OrmDomain
 */
final class OrmDomain
{
	/**
	 * @var string
	 */
	private $dbSchema;

	/**
	 * @var array of {@link OrmClass}
	 */
	private $classes = array();

	/**
	 * @return string|null
	 */
	function getDbSchema()
	{
		return $this->dbSchema;
	}

	/**
	 * @return OrmDomain
	 */
	function setDbSchema($dbSchema = null)
	{
		Assert::isScalarOrNull($dbSchema);

		$this->dbSchema = $dbSchema;

		return $this;
	}

	/**
	 * @return OrmDomain
	 */
	function dropClasses()
	{
		$this->classes = array();

		return $this;
	}

	/**
	 * @return array of {@link OrmClass}
	 */
	function getClasses()
	{
		return $this->classes;
	}

	/**
	 * @return OrmDomain
	 */
	function addClasses(array $classes)
	{
		foreach ($classes as $class) {
			$this->addClass($class);
		}

		return $this;
	}

	/**
	 * @return OrmDomain
	 */
	function setClasses(array $classes)
	{
		$this->dropClasses()->addClasses($classes);

		return $this;
	}

	/**
	 * @return OrmDomain
	 */
	function setClass(OrmClass $class)
	{
		$this->classes[$class->getName()] = $class;

		return $this;
	}

	/**
	 * @return OrmDomain
	 */
	function addClass(OrmClass $class)
	{
		$name = $class->getName();

		if (isset($this->classes[$name])) {
			throw new OrmModelIntegrityException("Class {$class->getName()} already defined");
		}

		$this->classes[$name] = $class;

		return $this;
	}

	/**
	 * @return OrmClass
	 */
	function getClass($name)
	{
		if (!isset($this->classes[$name])) {
			throw new OrmModelIntegrityException("Class {$name} is not defined");
		}

		return $this->classes[$name];
	}

	/**
	 * @return boolean
	 */
	function classExists($name)
	{
		return isset($this->classes[$name]);
	}
}

?>