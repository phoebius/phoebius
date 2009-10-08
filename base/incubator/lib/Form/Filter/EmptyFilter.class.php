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
 * Determines whether the value is empty
 *
 */
class EmptyFilter implements IFilter
{
	/**
	 * Passes the variable throgh the filter and determines whether it passed or failed to pass
	 *
	 * @param mixed $value
	 * @return boolean
	 */
	function filter($value)
	{
		return strval($value) !== '';
	}
}
?>