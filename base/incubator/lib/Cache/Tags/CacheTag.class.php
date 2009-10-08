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

abstract class CacheTag
{
	abstract protected function getTagId();

	function drop()
	{
		Assert::notImplemented();
	}

	function renew()
	{
		Assert::notImplemented();
	}

	/**
	 * @return CachePeer
	 */
	protected function getCachePeer()
	{
		return Cache::getDefaultPeer();
	}
}

?>