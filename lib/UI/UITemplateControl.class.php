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
 * Represents a renderable control that is presented using the presentation layer
 *
 * @ingroup UI
 */
abstract class UITemplateControl extends UIControl
{
	/**
	 * @var UIPresentation
	 */
	private $presentation;

	/**
	 * @param UIPresentation $presentation presentation layer
	 */
	function __construct(UIPresentation $presentation)
	{
		$this->presentation = $presentation;

		if ($presentation instanceof IUIControlBindedPresentation) {
			$presentation->setUIControl($this);
		}
	}

	/**
	 * Gets the presentation layer of the control
	 *
	 * @return UIPresentation
	 */
	function getPresentation()
	{
		return $this->presentation;
	}

	function render(IOutput $output)
	{
		$this->presentation->render($output);
	}
}

?>