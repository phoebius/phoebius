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
	 * @throws FileNotFoundException
	 * @param string $layoutPath
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

	/**
	 * @return void
	 */
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

		$me = Type::typeof($this);
		$type = Type::check($type);

		Assert::isTrue(
			$me->getName() == $type->getName() || $me->isDescendantOf($type),
			'fatal error: %s should be the only wrapper for invoked layout to handle it successfully',
			$type->getName()
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