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
 * @ingroup Web
 */
interface IWebResponse extends IAppResponse
{
	/**
	 * @return boolean
	 */
	function isHeadersSent();

	/**
	 * @return array
	 */
	function getHeaders();

	/**
	 * @return IWebResponse
	 */
	function addHeader($header, $value);

	/**
	 * @return IWebResponse
	 */
	function addHeaders(array $headers);

	/**
	 * @return IWebResponse
	 */
	//function setStatus(HttpStatus $status);

	/**
	 * @return void
	 */
	function redirect(WebRequest $request, Url $url = null);
}

?>