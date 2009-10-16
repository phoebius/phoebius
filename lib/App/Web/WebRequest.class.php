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
 * TODO: Authentication layer for apache
 * @ingroup Web
 */
class WebRequest extends AppRequest
{
	/**
	 * @var array
	 */
	private $getVars = array();

	/**
	 * @var array
	 */
	private $postVars = array();

	/**
	 * @var array
	 */
	private $cookieVars = array();

	/**
	 * @var array
	 */
	private $filesVars = array();

	/**
	 * @var HttpUrl
	 */
	private $httpUrl;

	/**
	 * @var HttpUrl
	 */
	private $baseHttpUrl;

	/**
	 * @var array
	 */
	private $dictionary = array();

	/**
	 * @return WebRequest
	 */
	static function create(WebRequestDictionary $dictonary, HttpUrl $baseHttpUrl = null)
	{
		return new self ($dictonary, $baseHttpUrl);
	}

	function __construct(WebRequestDictionary $dictonary, HttpUrl $baseHttpUrl = null)
	{
		$this->dictionary = $dictonary->getFields();

		$this->baseHttpUrl =
			$baseHttpUrl
				? $baseHttpUrl
				: HttpUrl::import($dictonary)->setBase('/')->setPath('/');

		$this->httpUrl = HttpUrl::import($dictonary, $this->baseHttpUrl);
	}

	function __clone()
	{
		$this->httpUrl = clone $this->httpUrl;
		$this->baseHttpUrl = clone $this->baseHttpUrl;
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
	 * @return string|null
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
	function getBaseHttpUrl()
	{
		return $this->baseHttpUrl;
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
		$this->getVars = $getVars;

		$this->httpUrl->setQuery($getVars);

		return $this;
	}

	/**
	 * @return WebRequest an object itself
	 */
	function setPostVars(array $postVars)
	{
		$this->postVars = $postVars;

		return $this;
	}

	/**
	 * @return WebRequest an object itself
	 */
	function setCookieVars(array $cookieVars)
	{
		$this->cookieVars = $cookieVars;

		return $this;
	}

	/**
	 * @return WebRequest an object itself
	 */
	function setFilesVars(array $filesVars)
	{
		$this->filesVars = $filesVars;

		return $this;
	}

	/**
	 * @return ArgumentException
	 */
	function getVariable($variableName, WebRequestPart $type = null)
	{
		foreach ($this->getAllVars() as $varsType => $vars) {
			if (!is_null($type) && $type->isNot($varsType)) {
				continue;
			}

			if (isset($vars[$variableName])) {
				return $vars[$variableName];
			}
		}

		throw new ArgumentException('variableName', "unknown variable {$variableName}");
	}

	/**
	 * @return WebRequest
	 */
	function setVariable($variableName, WebRequestPart $type, $variableValue)
	{
		switch ($type->getValue()) {
			case WebRequestPart::GET: {
				$this->getVars[$variableName] = $variableValue;
				$this->httpUrl->addQueryArgument($variableName, $variableValue);
				break;
			}
			case WebRequestPart::POST: {
				$this->postVars[$variableName] = $variableValue;
				break;
			}
			case WebRequestPart::COOKIE: {
				$this->cookieVars[$variableName] = $variableValue;
				break;
			}
			case WebRequestPart::FILES: {
				$this->filesVars[$variableName] = $variableValue;
				break;
			}
		}

		return $this;
	}

	/**
	 * @throws ArgumentException
	 * @return mixed
	 */
	function getAnyVariable($variable)
	{
		return $this->getVariable($variable);
	}

	/**
	 * @return IAppRequest
	 */
	function getCleanCopy()
	{
		$copy = clone $this;

		$copy
			->setGetVars(array())
			->setPostVars(array())
			->setFilesVars(array())
			->setCookieVars(array());

		$copy->httpUrl->setQuery(array());

		return $copy;
	}

	/**
	 * @return array of {@link array}
	 */
	private function getAllVars()
	{
		return array(
			WebRequestPart::GET => $this->getGetVars(),
			WebRequestPart::POST => $this->getPostVars(),
			WebRequestPart::COOKIE => $this->getCookieVars(),
			WebRequestPart::FILES => $this->getFilesVars()
		);
	}

	/**
	 * @return array
	 */
	private function getVars(WebRequestPart $type)
	{
		$set = $this->getAllVars();

		Assert::isTrue(isset($set[$type->getValue()]));

		return $set[$type->getValue()];
	}
}

?>