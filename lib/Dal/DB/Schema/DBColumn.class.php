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
 * Aggregated by:
 *  - DBTable
 *  - DBConstraint
 *  - DBIndex (currently unimplemented)
 * @ingroup DB
 */
class DBColumn
{
	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var ISqlValueExpression
	 */
	private $defaultValue;

	/**
	 * @var DBType
	 */
	private $type;

	/**
	 * @return DBColumn
	 */
	static function create()
	{
		return new self;
	}

	/**
	 * @return string
	 */
	function getName()
	{
		return $this->name;
	}

	/**
	 * @return DBColumn
	 */
	function setName($name)
	{
		Assert::isScalar($name);

		$this->name = $name;

		return $this;
	}

	/**
	 * @return DBType
	 */
	function getType()
	{
		return $this->type;
	}

	/**
	 * @return DBColumn
	 */
	function setType(DBType $type)
	{
		$this->type = $type;

		return $this;
	}

	/**
	 * @return ISqlValueExpression
	 */
	function getDefaultValue()
	{
		return $this->defaultValue;
	}

	/**
	 * @return ISqlValueExpression
	 */
	function setDefaultValue(ISqlValueExpression $defaultValue = null)
	{
		$this->defaultValue = $defaultValue;

		return $this;
	}
}

?>