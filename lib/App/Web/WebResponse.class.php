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
 * @ingroup App_Web
 */
class WebResponse implements IWebResponse
{
	/**
	 * @var WebRequest
	 */
	private $request;
	private $bufferOpened = false;
	private $isFinished = false;

	function __construct(WebRequest $request = null)
	{
		$this->request = $request;
	}

	/**
	 * @return boolean
	 */
	function isFinished()
	{
		return $this->isFinished;
	}

	/**
	 * @return IAppResponse an object itself
	 */
	function write($string)
	{
		echo $string;

		return $this;
	}

	/**
	 * @return IAppResponse an object itself
	 */
	function outFile($filepath)
	{
		readfile($filepath);

		return $this;
	}

	/**
	 * @return IAppResponse
	 */
	function flush()
	{
		Assert::isFalse($this->isFinished, 'already finished');

		if ($this->bufferOpened) {
			ob_end_flush();
		}

		$this->clean();

		return $this;
	}

	/**
	 * @return IAppResponse
	 */
	function clean()
	{
		Assert::isFalse($this->isFinished, 'already finished');

		if ($this->bufferOpened) {
			ob_end_clean();
		}

		return $this;
	}

	/**
	 * @return boolean
	 */
	function isBufferOpened()
	{
		return $this->bufferOpened;
	}

	/**
	 * Flush + finish
	 * @return void
	 */
	function finish()
	{
		Assert::isFalse($this->isFinished, 'already finished');

		$this->flush();

		$this->isFinished = true;

		// http://php-fpm.anight.org/extra_features.html
		// TODO: cut out this functionality to the outer class descendant (PhpFpmResponse)
		if (function_exists('fastcgi_finish_request')) {
			fastcgi_finish_request();
		}
	}

	/**
	 * @return IAppResponse
	 */
	function openBuffer($useGzip)
	{
		Assert::isFalse($this->bufferOpened, 'already opened');

		if ($useGzip) {
			ob_start('ob_gzhandler', 5);
		}
		else {
			ob_start();
		}

		return $this;
	}

	/**
	 * @return IAppResponse
	 */
	function flushBuffer()
	{
		Assert::isTrue($this->bufferOpened, 'no buffers opened');

		ob_end_flush();

		return $this;
	}

	/**
	 * @return IAppResponse
	 */
	function dropBuffer()
	{
		Assert::isTrue($this->bufferOpened, 'no buffers opened');

		ob_end_clean();

		return $this;
	}

	/**
	 * @return boolean
	 */
	function isHeadersSent()
	{
		return headers_sent();
	}

	/**
	 * @return array
	 */
	function getHeaders()
	{
		return headers_list();
	}

	/**
	 * @return IWebResponse
	 */
	function addHeader($header, $value)
	{
		header($header . ': ' . $value, true);

		return $this;
	}

	/**
	 * @return IWebResponse
	 */
	function addHeaders(array $headers)
	{
		foreach ($headers as $header => $value)
		{
			$this->addHeader($header, $value);
		}

		return $this;
	}

	/**
	 * @return void
	 */
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
		header('Location: ' . ((string) $url), true, $status);

		$this->finish();
	}
	
	/**
	 * @return IWebResponse
	 */
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