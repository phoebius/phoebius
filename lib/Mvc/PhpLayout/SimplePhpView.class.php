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
 * @ingroup Mvc_PhpLayout
 */
class SimplePhpView implements IView
{
	/**
	 * @var string
	 */
	private $layoutPath;

	/**
	 * @throws FileNotFoundException
	 * @param string $layoutPath
	 */
	function __construct($layoutPath)
	{
		Assert::isScalar($layoutPath);

		$this->layoutPath = $layoutPath;

		if (!file_exists($layoutPath)) {
			throw new FileNotFoundException($layoutPath, 'view does not exists at the specified path');
		}
	}

	/**
	 * @return void
	 */
	function render(IViewContext $context)
	{
		// isolate inclusion to introduce a clean scope
		$this->injectLayout();
	}

	/**
	 * Overridden
	 * @return void
	 */
	private function injectLayout()
	{
		require $this->layoutPath;
	}
}

?>