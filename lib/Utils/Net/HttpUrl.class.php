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
 * @ingroup Utils_Net
 */
class HttpUrl extends Url
{
	private $baseHost = null;
	private $basePath = '/';

	/**
	 * @return HttpUrl
	 */
	static function import(HttpUrlDictionary $dictionary, $baseHost = null, $baseUri = '/')
	{
		$url = new self;

		$url->setBaseHost($baseHost);

		$url
			->setScheme(
				$dictionary->getField(HttpUrlDictionary::HTTPS)
					? "https"
					: "http"
			)
			->setHost($dictionary->getField(HttpUrlDictionary::HOST))
			->setPort($dictionary->getField(HttpUrlDictionary::PORT));

		//get the URI itself
		$uri = $dictionary->getField(HttpUrlDictionary::URI);
		if (!preg_match('/^https?:\/\//', $uri)) {
			$uri = '/' . ltrim($uri, '/');
		}
		$parts = parse_url($uri);

		if (isset($parts['path'])) {
			$path = urldecode($parts['path']);

			if ($baseUri != '/') {
				$baseUri = $url->setBase($baseUri)->getBase();
				$path = $url->setPath($path)->getPath();

				$path = substr(
					$path,
					strlen($baseUri)
				);
			}

			$url->setPath($path);
		}

		if (isset($parts['query'])) {
			$newQuery = $query = array();
			parse_str($parts['query'], $query);

			foreach ($query as $k => $v) {
				$newQuery[urldecode($k)] =
					is_array($v)
						? $v
						: urldecode($v);
			}

			$url->setQuery($newQuery);
		}

		return $url;
	}

	/**
	 * @param string $host
	 * @return Url an object itself
	 */
	function setHost($host)
	{
		parent::setHost($host);

		if (!$this->baseHost) {
			$this->baseHost = $this->host;
		}

		return $this;
	}

	/**
	 * @return string
	 */
	function getSubdomain()
	{
		if ($this->baseHost) {
			return substr($this->host, strlen($this->baseHost) + 1);
		}
		else {
			return $this->host;
		}
	}

	/**
	 * @return Url
	 */
	function setSubdomain($subdomain)
	{
		Assert::isScalar($subdomain);

		$this->setHost($subdomain . '.' . $this->baseHost);

		return $this;
	}

	/**
	 * @param string $baseHost
	 * @return Url an object itself
	 */
	function setBaseHost($baseHost = null)
	{
		Assert::isScalarOrNull($baseHost);

		$this->baseHost = $baseHost;

		return $this;
	}

	/**
	 * @return string|null
	 */
	function getBaseHost()
	{
		return $this->baseHost;
	}

	/**
	 * @return string
	 */
	function getBasePath()
	{
		return $this->basePath;
	}

	/**
	 * @return Url an object itself
	 */
	function setBasePath($basePath = '/')
	{
		Assert::isScalar($basePath);

		//avoid multiple slashing (i.e. setBase('////////')
		$this->basePath = '/' . trim($basePath, '/');

		return $this;
	}

	/**
	 * @return string
	 */
	function getVirtualPath()
	{
		$path = $this->getPath();

		if ('/' == $this->basePath) {
			return $path;
		}

		$prefix = substr($path, 0, strlen($this->basePath));
		if ($this->basePath == $prefix) {
			$path = substr($path, strlen($this->basePath));
		}

		return $path;
	}

	/**
	 * @return HttpUrl
	 */
	function spawnBase()
	{
		$clone = clone $this;
		$clone->setPath('/');
		$clone->setSubdomain(null);

		return $clone;
	}
}

?>