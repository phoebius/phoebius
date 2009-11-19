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
 * Encapsulates the request invoked over HTTP
 *
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
	 * @var SiteUrl
	 */
	private $httpUrl;

	/**
	 * @var array
	 */
	private $dictionary = array();

	function __construct(
				WebRequestDictionary $dictonary,
				array $getVars,
				array $postVars,
				array $cookieVars,
				array $filesVars,
				$baseHost = null, $baseUri = '/'
		)
	{
		Assert::isScalar($baseUri);

		$this->dictionary = $dictonary->getFields();

		$this->httpUrl = SiteUrl::import($dictonary, $baseHost, $baseUri);

		$this->vars = array(
			WebRequestPart::GET => $getVars,
			WebRequestPart::POST => $postVars,
			WebRequestPart::COOKIE => $cookieVars,
			WebRequestPart::FILES => $filesVars,
		);

		$this->allVars = call_user_func_array(
			'array_merge',
			array(
				$filesVars, $cookieVars, $postVars, $getVars
			)
		);
	}

	function __clone()
	{
		$this->httpUrl = clone $this->httpUrl;
		$this->dictionary = clone $this->dictionary;
	}

	/**
	 * Gets the request method.
	 *
	 * @return RequestMethod
	 */
	function getRequestMethod()
	{
		return new RequestMethod($this->dictionary[WebRequestDictionary::REQUEST_METHOD]);
	}

	/**
	 * Gets the HTTP protocol
	 *
	 * @return string eigher HTTP/1.0 or HTTP/1.1
	 */
	function getProtocol()
	{
		return $this->dictionary[WebRequestDictionary::PROTOCOL];
	}

	/**
	 * Gets the HTTP referer, if any
	 *
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
	 * Specifies whether request is secured and passed over HTTPS protocol.
	 *
	 * @return boolean
	 */
	function isSecured()
	{
		return !!$this->dictionary[WebRequestDictionary::HTTPS];
	}

	/**
	 * Gets the request URL
	 *
	 * @return SiteUrl|null
	 */
	function getHttpUrl()
	{
		return $this->httpUrl;
	}

	/**
	 * Gets the set of variables passed via the query string
	 *
	 * @return array
	 */
	function getGetVars()
	{
		return $this->getVars;
	}

	/**
	 * Gets the set of variables passed via POST part of the request
	 *
	 * @return array
	 */
	function getPostVars()
	{
		return $this->postVars;
	}

	/**
	 * Gets the set of variables that passed via cookies
	 *
	 * @return array
	 */
	function getCookieVars()
	{
		return $this->cookieVars;
	}

	/**
	 * Gets the set of variables that described as incoming files
	 *
	 * @return array
	 */
	function getFilesVars()
	{
		return $this->filesVars;
	}

	/**
	 * Determines whether variable is set in any of the request part
	 *
	 * @return boolean
	 */
	function hasVar($variableName, WebRequestPart $part = null)
	{
		$vars =
			$part
				? $this->vars[$part->getValue()]
				: $this->allVars;

		return isset($vars[$variableName]);
	}

	/**
	 * Gets the variable from the specified request part
	 *
	 * @throws ArgumentException if such variable does not exist
	 * @return scalar
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
	 * Defines an interface for easy access to request variable. Variable is looked up within
	 * all request parts.
	 *
	 * @return boolean
	 */
	function offsetExists($offset)
	{
		return isset($this->allVars[$offset]);
	}

	/**
	 * Defines an interface for easy access to request variable. Variable is looked up within
	 * all request parts.
	 *
	 * @return mixed
	 */
	function offsetGet($offset)
	{
		return $this->allVars[$offset];
	}

	/**
	 * Not implemented, and won't be.
	 *
	 * @return void
	 */
	function offsetSet($offset, $value)
	{
		Assert::isUnreachable('request arguments are read-only');
	}

	/**
	 * Not implemented, and won't be.
	 *
	 * @return void
	 */
	function offsetUnset($offset)
	{
		Assert::isUnreachable('request arguments are read-only');
	}
}

?>