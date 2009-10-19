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
 * Microsoft ASP.NET Web.UI realization
 * @ingroup Mvc_Exceptions
 */
abstract class PhpView extends SimplePhpView
{
	/**
	 * @var IViewContext|null
	 */
	private $viewContext = null;

	/**
	 * @var boolean
	 */
	private $inRenderingContext = false;

	/**
	 * @return void
	 */
	function render(IViewContext $context)
	{
		$this->assertOutsideRenderingContext();

		$this->inRenderingContext = true;
		$this->viewContext = $context;

		parent::render($context);

		$this->inRenderingContext = false;
		$this->viewContext = null;
	}

	/**
	 * @return void
	 */
	function renderPartial(PhpControlView $control, Model $model = null)
	{
		$this->assertInsideRenderingContext();

		$control->setParentView($this);

		$control->render(
			new ViewContext(
				$this->viewContext->getController(),
				$model ? $model : new Model(),
				$this->viewContext->getRouteContext(),
				$this->viewContext->getAppContext()
			)
		);
	}

	/**
	 * @return IViewContext|null
	 */
	function getViewContext()
	{
		$this->assertInsideRenderingContext();

		return $this->viewContext;
	}

	/**
	 * Wrapped over ASP.NET-like "CodeBehind" and "Inherits" attributes
	 * @return void
	 */
	function codeBehind($type)
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
}

?>