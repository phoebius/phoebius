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
 * Represents a presentation engine which uses plain PHP files as templates, included in the
 * context of the object. These PHP ``templates'' may use all protected methods of this class.
 *
 * @ingroup UI_Presentation
 */
class UIPhpLayoutPresentation extends UIPresentation
{
	/**
	 * @var string
	 */
	private $layoutPath;

	/**
	 * @var boolean
	 */
	private $inRenderingContext = false;

	/**
	 * @var IOutput
	 */
	private $output;

	/**
	 * @throws FileNotFoundException thrown when the path to a file is wrong
	 * @param string $layoutPath path to PHP file
	 */
	function __construct($layoutPath)
	{
		Assert::isScalar($layoutPath);

		$this->layoutPath = $layoutPath;

		if (!file_exists($layoutPath)) {
			throw new FileNotFoundException(
				$layoutPath,
				'template not found at the specified path'
			);
		}
	}

	function render(IOutput $output)
	{
		$this->assertOutsideRenderingContext();

		$this->inRenderingContext = true;
		$this->output = $output;

		ob_start();

		// isolate inclusion to introduce a clean scope
		$this->injectLayout();

		$output->write(ob_get_clean());

		$this->inRenderingContext = false;
		$this->output = null;
	}

	/**
	 * Wrapper over ASP.NET-like "CodeBehind" and "Inherits" attributes
	 * @return void
	 */
	final protected function codeBehind($type)
	{
		$this->assertInsideRenderingContext();

		Assert::isTrue(
			TypeUtils::isChild($this, $type),
			'fatal error: %s should be the only wrapper for invoked layout to handle it successfully',
			$type
		);
	}

	/**
	 * @return void
	 */
	final protected function assertInsideRenderingContext()
	{
		Assert::isTrue($this->inRenderingContext, 'can be called INSIDE render context only');
	}

	/**
	 * @return void
	 */
	final protected function assertOutsideRenderingContext()
	{
		Assert::isFalse($this->inRenderingContext, 'can be called OUTSIDE render context only');
	}

	/**
	 * @return void
	 */
	private function injectLayout()
	{
		require $this->layoutPath;
	}
}


?>