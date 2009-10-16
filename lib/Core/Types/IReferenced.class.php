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
interface IReferenced
{
	/**
	 * @return OrmPropertyType
	 */
	static function getRefHandler(AssociationMultiplicity $multiplicity);
}

?>