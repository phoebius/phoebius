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
final class WebRequest extends AppRequest implements ArrayAccess
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

	function __construct(
				WebRequestDictionary $dictonary, $baseHost = null, $baseUri = '/',
				array $getVars,
				array $postVars,
				array $cookieVars,
				array $filesVars
		)
	{
		Assert::isScalar($baseUri);

		$this->dictionary = $dictonary->getFields();

		$this->httpUrl = HttpUrl::import($dictonary, $baseHost, $baseUri);
		
		$this->allVars = array(
			WebRequestPart::GET => $getVars,
			WebRequestPart::POST => $postVars,
			WebRequestPart::COOKIE => $cookieVars,
			WebRequestPart::FILES => $filesVars,	
		);
		
		$this->regenerateAllVars();
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
	 * @return boolean
	 */
	function hasVar($variableName, WebRequestPart $part = null)
	{
		return isset(
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
		
		throw new ArgumentException('variableName', 'argument is not defined');
	}
	
	/**
	 * @return boolean
	 */
	function offsetExists($offset)
	{
		return isset($this->allVars[$offset]);
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
		Assert::isUnreachable('request arguments are read-only');
	}
	
	/**
	 * @return void
	 */
	function offsetUnset($offset)
	{
		Assert::isUnreachable('request arguments are read-only');
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