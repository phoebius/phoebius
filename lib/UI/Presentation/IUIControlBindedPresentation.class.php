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
 * Controact for presentation binded with the UIControl.
 *
 * This may be important if the presentation should use the features of custom hierarchy
 * of UIControl objects
 *
 * @ingroup UI_Presentation
 */
interface IUIControlBindedPresentation
{
	/**
	 * Sets the binded UIControl
	 *
	 * @param UIControl $control
	 *
	 * @return IUIControlBindedPresentation itself
	 */
	function setUIControl(UIControl $control);

	/**
	 * Gets the binded UIControl, if any
	 *
	 * @return UIControl|null
	 */
	function getUIControl();
}

?>