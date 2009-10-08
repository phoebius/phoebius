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

abstract class CacheSlot
{
	private $tags = array();

	abstract protected function getSlotId();

	function save($data)
	{
		Assert::notImplemented();
	}

	function get($data)
	{
		Assert::notImplemented();
	}

	function drop()
	{
		Assert::notImplemented();
	}

	/**
	 * @return CacheSlot
	 */
	function addTag(CacheTag $tag)
	{
		$this->tags[] = $tag;

		return $this;
	}

	/**
	 * @return CacheSlot
	 */
	function addTags(array $tags)
	{
		foreach($tags as $tag)
		{
			$this->addTag($tag);
		}

		return $this;
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