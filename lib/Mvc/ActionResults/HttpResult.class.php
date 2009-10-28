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
 * @ingroup Mvc_ActionResults
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
		$context->getController()->getContext()->getAppContext()->getResponse()->addHeaders($this->headers);
		$this->childResult->handleResult($context);
	}



}

?>