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
abstract class BuiltInType implements IObjectMappable, IHandled
{
	/**
	 * @var scalar
	 */
	private $value;

	/**
	 * @param scalar $value
	 */
	function __construct($value)
	{
		$this->setValue($value);
	}

	/**
	 * @return BuiltInType
	 */
	function setValue($value)
	{
		if ($this->isValidValue($value)) {
			$this->value = $value;
		}
		else {
			throw new TypeCastException(Type::typeof($this), $value);
		}

		return $this;
	}

	/**
	 * @return scalar
	 */
	function getValue()
	{
		return $this->value;
	}

	/**
	 * @return string
	 */
	function toString()
	{
		return (string)$this->getValue();
	}

	/**
	 * @return string
	 */
	function __toString()
	{
		return $this->toString();
	}

	/**
	 * @return boolean
	 */
	protected function isValidValue($value)
	{
		return is_scalar($value);
	}
}

?>