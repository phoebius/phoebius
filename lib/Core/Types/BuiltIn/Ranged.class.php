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
 * @ingroup BuiltInCoreTypes
 */
abstract class Ranged extends Decimal
{
	/**
	 * @return integer
	 */
	abstract protected function getMin();

	/**
	 * @return integer
	 */
	abstract protected function getMax();

	/**
	 * @return BuiltInType
	 */
	function setValue($value)
	{
		$this->checkLimits($value);

		return parent::setValue($value);
	}

	/**
	 * @return boolean
	 */
	protected function checkLimits($value)
	{
		Assert::isTrue($this->getMin() < $this->getMax());

		if ($this->getMin() > $value) {
			throw new TypeCastException(Type::typeof($this), $value, 'value is out of range');
		}

		if ($this->getMax() < $value) {
			throw new TypeCastException(Type::typeof($this), $value, 'value is out of range');
		}
	}
}

?>