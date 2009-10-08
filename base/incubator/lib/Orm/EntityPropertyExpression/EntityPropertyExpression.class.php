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
	 * @var EntityProperty
	 */
	private $ep;

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
	static function create(EntityProperty $ep, IExpression $expression)
	{
		return new self ($ep, $expression);
	}

	function __construct(EntityProperty $ep, IExpression $expression)
	{
		$this->ep = $ep;
		$this->expression = $expression;
	}

	/**
	 * @return EntityProperty
	 */
	function getEntityProperty()
	{
		return $this->ep;
	}

	/**
	 * @return OrmProperty
	 */
	function getProperty()
	{
		Return $this->ep->getProperty();
	}

	/**
	 * @return IExpression
	 */
	function getExpression()
	{
		return $this->expression;
	}

	/**
	 * @return string
	 */
	protected function getTableOrAlias()
	{
		return $this->ep->getEntityQuery()->getAlias();
	}

	/**
	 * @return array
	 */
	protected function makeRawValue($value)
	{
		return array_combine(
			$this->getProperty()->getDBFields(),
			$this->getProperty()->getType()->makeRawValue($value)
		);
	}
}

?>