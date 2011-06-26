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
 * Represents a redirection to a new url
 *
 * @ingroup Mvc_ActionResults
 */
class RedirectResult implements IActionResult
{
	/**
	 * @var HttpUrl
	 */
	private $url;
	
	/**
	 * @var HttpStatus
	 */
	private $status;

	/**
	 * @param HttpUrl $url url the request should be redirected to
	 */
	function __construct($url, HttpStatus $status = null)
	{
		if (is_scalar($url)) 
			$url = new HttpUrl($url);

		$this->url = $url;
		$this->status = $status;
	}

	function handleResult(IWebResponse $response)
	{
		$response->redirect($this->url, $this->status);
	}
}

?>