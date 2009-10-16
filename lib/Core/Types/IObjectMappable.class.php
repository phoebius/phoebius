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
 * @ingroup CoreTypes
 */
interface IObjectMappable
{
	/**
	 * @param scalar $value
	 * @return IObjectMappable
	 * @throws TypeCastException
	 */
	static function cast($value);
}

?>