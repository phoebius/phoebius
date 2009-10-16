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
 * Cast failure
 * @ingroup Core
 * @ingroup Types
 */
class TypeCastException extends ArgumentTypeException
{
	/**
	 * @var Type
	 */
	private $type;

	/**
	 * @var mixed
	 */
	private $value;

	function __construct(Type $failedType, $value, $message = 'type cast failed')
	{
		Assert::isTrue($failedType->isDescendantOf(new Type('IObjectMappable')));

		parent::__construct('value', $failedType->getName(), $message);

		$this->type = $failedType;
		$this->value = $value;
	}

	/**
	 * @return Type
	 */
	function getFailedType()
	{
		return $this->type;
	}

	/**
	 * @return mixed
	 */
	function getValue()
	{
		return $this->value;
	}
}

?>