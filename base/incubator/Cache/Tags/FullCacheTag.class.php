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

abstract class FullCacheTag
{
	private $peer;
	private $tagId;

	function __construct($tagId, CachePeer $peer = null)
	{
		$this->tagId = $tagId;
		$this->peer = $peer;
	}

	protected function getTagId()
	{
		Assert::isNotEmpty(
			$this->tagId,
			sprintf(
				'Your descendant %s should call %s::__construct() to initialize the object',
				get_class($this), __CLASS__
			)
		);

		return $this->tagId;
	}

	/**
	 * @return CachePeer
	 */
	protected function getCachePeer()
	{
		if ($this->peer)
		{
			return $this->peer;
		}
		else
		{
			return parent::getCachePeer();
		}
	}
}

?>