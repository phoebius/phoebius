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
 * Represents a scalar sql value
 * @ingroup Sql
 */
class ScalarSqlValue extends SqlValue
{
	/**
	 * Sets the value to be casted to SQL value
	 * @param scalar $value
	 * @return ScalarSqlValue an object itself
	 */
	function setValue($value = null)
	{
		Assert::isScalarOrNull($value);

		parent::setValue($value);

		return $this;
	}
}

?>