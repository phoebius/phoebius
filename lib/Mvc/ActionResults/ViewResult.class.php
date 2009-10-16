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
 * Represents HTML and markup
 * IView + Model
 * @ingroup ActionResults
 */
class ViewResult implements IActionResult
{
	/**
	 * @var IView
	 */
	private $view;

	function __construct(IView $view)
	{
		$this->view = $view;
	}

	/**
	 * @return void
	 */
	function handleResult(IViewContext $context)
	{
		$this->view->render($context);

		$context->getAppContext()->getResponse()->finish();
	}

}

?>