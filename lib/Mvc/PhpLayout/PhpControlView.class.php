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
 * TODO: move parent view setter to a ctor
 * @ingroup PhpLayout
 */
class PhpControlView extends PhpView
{
	/**
	 * @var PhpView
	 */
	private $parentView;

	/**
	 * @return PhpControlView an object itself
	 */
	function setParentView(PhpView $parentView)
	{
		$this->parentView = $parentView;

		return $this;
	}

	/**
	 * @return PhpView
	 */
	function getParentView()
	{
		Assert::isNotEmpty($this->parentView, 'parent view not yet set');

		return $this->parentView;
	}
}

?>