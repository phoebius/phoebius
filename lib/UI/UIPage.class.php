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
 * Represents a renderable page
 *
 * @ingroup UI
 */
class UIPage extends UITemplateControl
{
	/**
	 * @var UIMasterPage|null
	 */
	private $masterPage = null;

	/**
	 * Sets the master page for the control
	 *
	 * @return UIPage an object itself
	 */
	function setMasterPage(UIMasterPage $masterPage)
	{
		$this->masterPage = $masterPage;

		return $this;
	}

	/**
	 * Gets the master page for the control
	 *
	 * @return UIMasterPage
	 */
	function getMasterPage()
	{
		return $this->masterPage;
	}

	function getParentControl()
	{
		return $this->masterPage;
	}

	function render(IOutput $output)
	{
		$memoryBuffer = new MemoryStream;

		parent::render($memoryBuffer);

		if ($this->masterPage) {
			$this->masterPage->setDefaultContent($memoryBuffer->getBuffer());
			$this->masterPage->render($output);
		}
		else {
			$output->write($memoryBuffer->getBuffer());
		}
	}
}

?>