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
 * Represents the IN expression used in query logic
 * @ingroup BaseExpression
 */
final class InSetExpression implements IExpression
{
	/**
	 * @var array
	 */
	private $set;

	/**
	 * @var InSetPredicate
	 */
	private $logic;

	function __construct(array $set, InSetPredicate $logic = null)
	{
		$this->set = $set;
		$this->logic =
			$logic
				? $logic
				: InSetPredicate::in();
	}

	/**
	 * @return array
	 */
	function getSet()
	{
		return $this->set;
	}

	/**
	 * @return InSetPredicate
	 */
	function getPredicate()
	{
		return $this->logic;
	}

	/**
	 * @return ExpressionType
	 */
	function getExpressionType()
	{
		return new ExpressionType(ExpressionType::IN_SET);
	}
}

?>