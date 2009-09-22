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
abstract class Decimal extends Numeric
{
	/**
	 * @return Decimal
	 */
	static function create($value)
	{
		return new self ($value);
	}

	/**
	 * @return Decimal
	 */
	static function cast($value)
	{
		return new self ($value);
	}

	/**
	 * @return boolean
	 */
	protected function isValidValue($value)
	{
		if (strlen((string)$value) > 0 && $value{0} == '+') {
			$value = substr($value, 1);
			if (empty($value)) {
				$value = 0;
			}
		}

		return
			// http://www.php.net/manual/en/function.is-numeric.php#76094
			((string)(float)$value) === (preg_replace('/([\.]0*)$/', '', (string)$value));
	}
}

?>