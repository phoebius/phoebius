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
 * Represents a renderable master page that can define custom layout of other inner controls
 *
 * @ingroup UI
 */
class UIMasterPage extends UIPage
{
	/**
	 * @var IOutput
	 */
	private $output;

	/**
	 * @var string
	 */
	private $defaultContent;

	function render(IOutput $output)
	{
		$this->output = $output;

		parent::render($output);

		$this->output = null;
	}

	/**
	 * Sets the default content - the result of inner control render
	 *
	 * @return UIMasterPage itself
	 */
	function setDefaultContent($content)
	{
		Assert::isScalar($content);

		$this->defaultContent = $content;

		return $this;
	}

	/**
	 * Gets the default content taken from the inner control
	 *
	 * @return string
	 */
	function getDefaultContent()
	{
		return $this->defaultContent;
	}
}

?>