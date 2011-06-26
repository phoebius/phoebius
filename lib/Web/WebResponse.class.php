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
 * @ingroup App_Web
 */
class WebResponse
{
	/**
	 * @var WebRequest
	 */
	private $request;
	
	/**
	 * @var boolean
	 */
	private $isStarted = false;
	
	/**
	 * Status code of the response
	 */
	private $status;
	
	/**
	 * Headers to send
	 * @var array
	 */
	private $headers = array();
	
	/**
	 * Cookies to send
	 * @var array
	 */
	private $cookies = array();
	
	/**
	 * @var array of Session
	 */
	private $sessions = array();

	/**
	 * @var boolean
	 */
	private $isFinished = false;

	/**
	 * Constructs a web response for the given request
	 * @param WebRequest $request incoming request
	 */
	function __construct(WebRequest $request)
	{
		$this->request = $request;
	}

	/**
	 * Sets the status of response
	 * @return WebResponse itself
	 */
	function setStatus(HttpStatus $status)
	{
		Assert::isFalse($this->isStarted, 'response headers already sent');
		
		$this->status = $status;
		
		return $this;
	}
	
	/**
	 * Adds a cookie. Note that it is better to use WebResponse::getSession() to store temporary
	 * data
	 * @return WebResponse itself
	 */
	function addCookie(Cookie $cookie)
	{
		Assert::isFalse($this->isStarted, 'response headers already sent');
		
		$this->cookies[] = $cookie;
		
		return $this;
	}

	/**
	 * Adds a response header
	 * @param string $header
	 * @param string $value
	 */
	function addHeader($header, $value)
	{
		Assert::isFalse($this->isStarted, 'response headers already sent');
		Assert::isScalar($header);
		Assert::isScalar($value);
		
		$this->headers[$header] = $value;

		return $this;
	}
	
	/**
	 * Gets the response session. Don't forget to save it (Session::save()).
	 * @return Session
	 */
	function getSession($id)
	{
		Assert::isFalse($this->isStarted, 'request already started');

		$id .= sha1(PHOEBIUS_APP_ID);
		
		if (!isset($this->sessions[$id])) {
			$this->sessions[$id] = $s = new Session($id, $this);
			$s->import($this->request->getCookieVars());
		}
		
		return $this->sessions[$id];
	}

	/**
	 * Pushes response headers to perform redirect to a specified url.
	 * @return WebResponse itself
	 */
	function redirect(HttpUrl $url, HttpStatus $status = null)
	{
		if ($status) {
			$this->setStatus($status);
		}
		else {
			$protocol = $this->request->getProtocol();
	
			if ($protocol == 'HTTP/1.1') {
				$this->setStatus(new HttpStatus(HttpStatus::CODE_303));
			}
			else {
				$this->setStatus(new HttpStatus(HttpStatus::CODE_302));
			}
		}

		$this->addHeader('Location', (string)$url);
		
		return $this;
	}

	/**
	 * Writes a string to response. Note: this forces a response object to initialize 
	 * response by sending response headers (until response buffer is turned on - currently
	 * unimplemented).
	 * @param string $string
	 * @return WebResponse itself
	 */
	function write($string)
	{
		if (!$this->isStarted) {
			$this->start();
		}

		echo $string;

		return $this;
	}

	/**
	 * Writes a file contents directly to response. Note: this forces a response object to initialize 
	 * response by sending response headers (until response buffer is turned on - currently
	 * unimplemented).
	 * @param string $filepath
	 * @return WebResponse itself
	 */
	function writeFile($filepath)
	{
		if (!$this->isStarted) {
			$this->start();
		}
		
		readfile($filepath);

		return $this;
	}

	/**
	 * Closes a response. Note: it also forces to send buffered headers.
	 * @return void
	 */
	function finish()
	{
		Assert::isFalse($this->isFinished, 'already finished');
		
		if (!$this->isStarted) {
			$this->start();
		}

		// http://php-fpm.anight.org/extra_features.html
		// TODO: cut out this functionality to the outer class descendant (e.g., PhpFpmResponse)
		if (function_exists('fastcgi_finish_request')) {
			call_user_func('fastcgi_finish_request');
		}

		$this->isFinished = true;
	}
	
	final protected function start()
	{
		Assert::isFalse($this->isStarted, 'response already started');
		
		if ($this->status) {
			$protocol =
				$this->request
				? $this->request->getProtocol()
				: 'HTTP/1.0';
	
			header($protocol . ' ' . $this->status->getValue() . ' ' . $this->status->getStatusMessage(), true);
		}
		
		foreach ($this->headers as $name => $value) {
			header($name . ': ' . $value, true);
		}
		
		foreach ($this->cookies as $cookie) {
			setcookie($cookie->getName(), $cookie->getValue(), $cookie->getExpire(), $cookie->getPath());
		}
		
		$this->isStarted = true;
	}
}

?>