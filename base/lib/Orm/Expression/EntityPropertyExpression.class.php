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
 * @ingroup OrmExpression
 */
abstract class EntityPropertyExpression implements IEntityPropertyExpression
{
	/**
	 * @var string
	 */
	private $table;

	/**
	 * @var OrmProperty
	 */
	private $property;

	/**
	 * @var IExpression
	 */
	private $expression;

	/**
	 * @return EntityExpression
	 */
	static function create($table, OrmProperty $property, IExpression $expression)
	{
		return new self ($table, $property, $expression);
	}

	function __construct($table, OrmProperty $property, IExpression $expression)
	{
		$this->table = $table;
		$this->property = $property;
		$this->expression = $expression;
	}

	/**
	 * @return string
	 */
	function getTable()
	{
		return $this->table;
	}

	/**
	 * @return OrmProperty
	 */
	function getProperty()
	{
		Return $this->property;
	}

	/**
	 * @return IExpression
	 */
	function getExpression()
	{
		return $this->expression;
	}

	/**
	 * @return array
	 */
	protected function makeRawValue($value)
	{
		return array_combine(
			$this->property->getDbColumns(),
			$this->property->getType()->makeRawValue($value)
		);
	}
}

?>