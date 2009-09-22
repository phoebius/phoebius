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
 * @ingroup Dao
 */
abstract class CacheableDao extends RdbmsDao
{
	/**
	 * @return CachePeer
	 */
	protected function getCachePeer()
	{
		return Cache::getDefaultPeer();
	}
}

?>