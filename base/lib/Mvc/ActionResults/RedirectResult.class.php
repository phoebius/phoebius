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
 * Represents a redirection to a new url
 * @ingroup ActionResults
 */
class RedirectResult implements IActionResult
{
	/**
	 * @var HttpUrl
	 */
	private $url;

	function __construct(HttpUrl $url)
	{
		$this->url = $url;
	}

	/**
	 * @return void
	 */
	function handleResult(IViewContext $context)
	{
		$context->getAppContext()->getResponse()->redirect($context->getAppContext()->getRequest(), $this->url);
	}
}

?>