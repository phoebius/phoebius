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
class InSetExpression implements IExpression
{
	/**
	 * @var mixed
	 */
	private $subject;

	/**
	 * @var array
	 */
	private $set;

	/**
	 * @var InSetPredicate
	 */
	private $logic;

	function __construct($subject, array $set, InSetPredicate $logic = null)
	{
		$this->subject = $subject;
		$this->set = $set;
		$this->logic =
			$logic
				? $logic
				: InSetPredicate::in();
	}

	/**
	 * @return mixed
	 */
	function getSubject()
	{
		return $this->subject;
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
	 * @return InSetExpression
	 */
	function toExpression(IExpressionSubjectConverter $converter)
	{
		return new self(
			$converter->convert($this->subject, $this),
			$this->convertSet($converter),
			$this->logic
		);
	}

	/**
	 * @return array
	 */
	private function convertSet(IExpressionSubjectConverter $converter)
	{
		$set = array();
		foreach ($this->set as $item) {
			$set[] = $converter->convert($item);
		}

		return $set;
	}

	/**
	 * @return BinaryDalExpression
	 */
	function toDalExpression()
	{
		return new InSetDalExpression($this);
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