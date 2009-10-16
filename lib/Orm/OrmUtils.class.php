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
 * @ingroup Orm
 */
class OrmUtils extends StaticClass
{
	/**
	 * @return boolean
	 */
	static function isFetchedEntity(IdentifiableOrmEntity $entity)
	{
		$privatePropertyName = "\0IdentifiableOrmEntity\0fetched";
		$arrayCasted = (array) $entity;

		return (boolean) $arrayCasted[$privatePropertyName];
	}
}

?>