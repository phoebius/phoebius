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
 * Maps property raw values to db values and vice versa
 * @ingroup Orm
 */
interface IOrmQuery
{
	/**
	 * $rawValue structure:
	 *  - key is dbColumn name
	 *  - value is SqlValue
	 * @return array
	 */
	function makeColumnValue(OrmProperty $property, array $rawValue);

	/**
	 * @return array
	 */
	function makeRawValue(OrmProperty $property, $dbValues);
}

?>