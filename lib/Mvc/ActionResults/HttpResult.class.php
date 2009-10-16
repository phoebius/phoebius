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
 * @ingroup ActionResults
 */
class HttpResult implements IActionResult
{
	/**
	 * @var IActionResult
	 */
	private $childResult;

	/**
	 * @var array of headers
	 */
	private $headers = array();

	function __construct(IActionResult $childResult, array $headers = array())
	{
		$this->childResult = $childResult;
		$this->headers = $headers;
	}
	/**
	 * @return void
	 */
	function handleResult(IViewContext $context)
	{
		$context->getAppContext()->getResponse()->addHeaders($this->headers);
		$this->childResult->handleResult($context);
	}



}

?>