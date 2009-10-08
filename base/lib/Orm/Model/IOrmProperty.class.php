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
 * @ingroup OrmModel
 */
interface IOrmProperty
{
	/**
	 * @return string
	 */
	function getGetter();

	/**
	 * @return string
	 */
	function getSetter();

	/**
	 * @return string
	 */
	function getName();

	/**
	 * @return boolean
	 */
	function isUnique();

	/**
	 * @return OrmPropertyType
	 */
	function getType();

	/**
	 * @return OrmPropertyVisibility
	 */
	function getVisibility();

	/**
	 * @return array of columnName
	 */
	function getDBFields();
}

?>