<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright (c) 2009 Scand Ltd.
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
 * Rerpresents a custom nestable renderable control
 *
 * @ingroup UI
 */
class UIUserControl extends UITemplateControl
{
	/**
	 * @var UITemplateControl|null
	 */
	private $parentControl;

	/**
	 * Sets the parent control
	 *
	 * @return UIUserControl itself
	 */
	function setParentControl(UITemplateControl $parentControl)
	{
		$this->parentControl = $parentControl;

		return $this;
	}

	function getParentControl()
	{
		return $this->parentControl;
	}
}

?>