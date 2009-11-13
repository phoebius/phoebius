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
 * FIXME implement ArrayAccess for request variables (GPC resolution order)
 * @ingroup App_Web
 */
class WebRequest extends AppRequest implements ArrayAccess
{
	private $vars = array(
		WebRequestPart::GET => array(),
		WebRequestPart::POST => array(),
		WebRequestPart::COOKIE => array(),
		WebRequestPart::FILES => array(),
	);
	
	private $allVars = array();

	/**
	 * @var HttpUrl
	 */
	private $httpUrl;

	/**
	 * @var array
	 */
	private $dictionary = array();

	/**
	 * @return WebRequest
	 */
	static function create(WebRequestDictionary $dictonary, $baseHost = null, $baseUri = '/')
	{
		return new self ($dictonary, $baseHost, $baseUri);
	}

	function __construct(WebRequestDictionary $dictonary, $baseHost = null, $baseUri = '/')
	{
		Assert::isScalar($baseUri);

		$this->dictionary = $dictonary->getFields();

		$this->httpUrl = HttpUrl::import($dictonary, $baseHost, $baseUri);
	}

	function __clone()
	{
		$this->httpUrl = clone $this->httpUrl;
		$this->dictionary = clone $this->dictionary;
	}

	/**
	 * @return RequestMethod
	 */
	function getRequestMethod()
	{
		return new RequestMethod($this->dictionary[WebRequestDictionary::REQUEST_METHOD]);
	}

	/**
	 * @return WebRequest an object itself
	 */
	function setRequestMethod(RequestMethod $method)
	{
		$this->dictionary[WebRequestDictionary::REQUEST_METHOD] = $method->getValue();

		return $this;
	}

	/**
	 * @return WebRequest an object itself
	 */
	function setProtocol($protocol)
	{
		Assert::isScalar($protocol);

		$this->dictionary[WebRequestDictionary::PROTOCOL] = $protocol;

		return $this;
	}

	/**
	 * @return string
	 */
	function getProtocol()
	{
		return $this->dictionary[WebRequestDictionary::PROTOCOL];
	}

	/**
	 * @return HttpUrl|null
	 */
	function getHttpReferer()
	{
		if ($this->dictionary[WebRequestDictionary::HTTP_REFERER]) {
			return new HttpUrl($this->dictionary[WebRequestDictionary::HTTP_REFERER]);
		}
		else {
			return null;
		}
	}

	/**
	 * @return boolean
	 */
	function isSecured()
	{
		return !!$this->dictionary[WebRequestDictionary::HTTPS];
	}

	/**
	 * @return WebRequest an object itself
	 */
	function setHttps($flag)
	{
		Assert::isBoolean($flag);

		$this->dictionary[WebRequestDictionary::HTTPS] = $flag;
		$this->httpUrl->setScheme($flag ? 'https' : 'http');

		return $this;
	}

	/**
	 * @return WebRequest an object itself
	 */
	function setSecured()
	{
		$this->setHttps(true);

		return $this;
	}

	/**
	 * @return WebRequest an object itself
	 */
	function setNotSecured()
	{
		$this->setHttps(false);

		return $this;
	}

	/**
	 * @return HttpUrl|null
	 */
	function getHttpUrl()
	{
		return $this->httpUrl;
	}

	/**
	 * @return array
	 */
	function getGetVars()
	{
		return $this->getVars;
	}

	/**
	 * @return array
	 */
	function getPostVars()
	{
		return $this->postVars;
	}

	/**
	 * @return array
	 */
	function getCookieVars()
	{
		return $this->cookieVars;
	}

	/**
	 * @return array
	 */
	function getFilesVars()
	{
		return $this->filesVars;
	}

	/**
	 * @return WebRequest an object itself
	 */
	function setGetVars(array $getVars)
	{
		$this->vars[WebRequestPart::GET] = $getVars;
		
		$this->regenerateAllVars();

		return $this;
	}

	/**
	 * @return WebRequest an object itself
	 */
	function setPostVars(array $postVars)
	{
		$this->vars[WebRequestPart::POST] = $postVars;
		
		$this->regenerateAllVars();

		return $this;
	}

	/**
	 * @return WebRequest an object itself
	 */
	function setCookieVars(array $cookieVars)
	{
		$this->vars[WebRequestPart::COOKIE] = $cookieVars;
		
		$this->regenerateAllVars();

		return $this;
	}

	/**
	 * @return WebRequest an object itself
	 */
	function setFilesVars(array $filesVars)
	{
		$this->vars[WebRequestPart::FILES] = $filesVars;
		
		$this->regenerateAllVars();

		return $this;
	}
	
	/**
	 * @return boolean
	 */
	function hasVar($variableName, WebRequestPart $part = null)
	{
		return array_key_exists(
			$variableName,
			$part
				? $this->vars[$part->getValue()]
				: $this->allVars
		);
	}

	/**
	 * @return ArgumentException
	 */
	function getVar($variableName, WebRequestPart $part = null)
	{
		$vars =
			$part
				? $this->vars[$part->getValue()]
				: $this->allVars;
		
		if (isset($variableName, $vars)) {
			return $vars[$variableName];
		}
		
		return null;
	}
	
	/**
	 * @return boolean
	 */
	function offsetExists($offset)
	{
		return array_key_exists($this->allVars[$offset]);
	}
	
	/**
	 * @return mixed
	 */
	function offsetGet($offset)
	{
		return $this->allVars[$offset];
	}
	
	/**
	 * @return void
	 */
	function offsetSet($offset, $value)
	{
		Assert::isUnreachable();
	}
	
	/**
	 * @return void
	 */
	function offsetUnset($offset)
	{
		Assert::isUnreachable();
	}

	/**
	 * @return void
	 */
	private function regenerateAllVars()
	{
		$this->allVars = call_user_func_array('array_merge', array_reverse($this->vars));
	}
}

?>