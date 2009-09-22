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
 * Represents an object, identifiered by an identifier, that could be retrieved or set manually
 * @ingroup Patterns
 */
interface IIdentifiable
{
	/**
	 * Gets the identifier of an object
	 * @return scalar
	 */
	function getId();

	/**
	 * Sets the identifier of an object
	 * @param scalar $id
	 * @return IIdentifiable an object itself
	 */
	function setId($id);
}

?>