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
 * Basic web-server response implementation.
 *
 * @todo introduce buffering
 *
 * @ingroup App_Web
 */
class WebResponse implements IWebResponse
{
	/**
	 * @var WebRequest
	 */
	private $request;

	/**
	 * @var boolean
	 */
	private $isFinished = false;

	/**
	 * @param WebRequest $request WebResponse *MAY* now about the request to provide accure results
	 */
	function __construct(WebRequest $request = null)
	{
		$this->request = $request;
	}

	function isFinished()
	{
		return $this->isFinished;
	}

	function write($string)
	{
		echo $string;

		return $this;
	}

	/**
	 * Writes the contents of the file to the response.
	 *
	 * This method is WebResponse::write() optimized for files.
	 *
	 * @return WebResponse itself
	 */
	function writeFile($filepath)
	{
		readfile($filepath);

		return $this;
	}

	function finish()
	{
		Assert::isFalse($this->isFinished, 'already finished');

		$this->isFinished = true;

		// http://php-fpm.anight.org/extra_features.html
		// TODO: cut out this functionality to the outer class descendant (e.g., PhpFpmResponse)
		if (function_exists('fastcgi_finish_request')) {
			fastcgi_finish_request();
		}
	}

	function isHeadersSent()
	{
		return headers_sent();
	}

	function getHeaders()
	{
		return headers_list();
	}

	function addHeader($header, $value)
	{
		header($header . ': ' . $value, true);

		return $this;
	}

	function addHeaders(array $headers)
	{
		foreach ($headers as $header => $value) {
			$this->addHeader($header, $value);
		}

		return $this;
	}

	function redirect(HttpUrl $url)
	{
		if ($this->request) {
			$protocol = $this->request->getProtocol();
		}

		if (isset($protocol) && $protocol == 'HTTP/1.1') {
			$status = 303;
			header('HTTP/1.1 303 See Other', true);
		}
		else {
			$status = 302;
			header('HTTP/1.0 302 Found', true);
		}

		//header('Content-Length: 0');
		// FIXME 1. Allow explicit set of Content-Length (by default it should be 0)
		// FIXME 2. Introduct HttpStatus for 302 and 303 status codes and use setStatus() wrt overridden behaviour
		header('Location: ' . ((string) $url), true, $status);

		$this->finish();
	}

	function setStatus(HttpStatus $status)
	{
		$protocol =
			$this->request
			? $this->request->getProtocol()
			: 'HTTP/1.0';

		header($protocol . ' ' . $status->getValue() . ' ' . $status->getStatusMessage(), true);
	}
}

?>