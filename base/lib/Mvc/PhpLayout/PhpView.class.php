<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright notice
 *
 ************************************************************************************************/

/**
 * Microsoft ASP.NET Web.UI realization
 * @ingroup PhpLayout
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