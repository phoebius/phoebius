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
abstract class Numeric extends Scalar
{
	/**
	 * @return Numeric
	 */
	static function create($value)
	{
		return new self ($value);
	}

	/**
	 * @return Numeric
	 */
	static function cast($value)
	{
		return new self ($value);
	}

	/**
	 * @return BuiltInType
	 */
	function setValue($value)
	{
		if (is_scalar($value)) {
			$value = str_replace(',', '.', $value);
		}

		return parent::setValue($value);
	}

	/**
	 * @return boolean
	 */
	protected function isValidValue($value)
	{
		return
			   parent::isValidValue($value)
			&& is_numeric($value);
	}
}

?>