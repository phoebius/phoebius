<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright (c) 2009 phoebius.org
 *
 * This program is free software; you can redistribute it and/or modify it under the terms
 * of the GNU Lesser General Public License as published by the Free Software Foundation;
 * either version 3 of the License, or (at your option) any later version.
 *
 * You should have received a copy of the GNU Lesser General Public License along with
 * this program; if not, see <http://www.gnu.org/licenses/>.
 *
 ************************************************************************************************/

/**
 * TODO: move parent view setter to a ctor
 * @ingroup Mvc_Exceptions
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