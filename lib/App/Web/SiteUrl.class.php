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
 * Implements the url that provides precise control of various url subparts.
 *
 * This class defines a set of new subparts:
 * - base host
 * - subdomain
 * - base path
 * - virtual path
 *
 * Schema is the following:
 * @code
 * http://<subdomain>.<base host></base path></virtual path>
 * @endcode
 *
 * @ingroup App_Web
 */
class SiteUrl extends HttpUrl
{
	private $baseHost = null;
	private $basePath = null;

	/**
	 * Constructs a SiteUrl object from request variables
	 *
	 * @param HttpUrlDictionary $dictionary dictionary of values to be used in building the SiteUrl
	 * @param string $baseHost optional host to be treated as base host
	 * @param string $baseUri optional uri to be treated as base uri
	 *
	 * @return SiteUrl
	 */
	static function import(HttpUrlDictionary $dictionary, $baseHost = null, $baseUri = '/')
	{
		$url = new self;

		if ($baseHost) {
			$url->setBaseHost($baseHost);
		}

		$url->setBasePath($baseUri);

		$url
			->setScheme(
				$dictionary->getField(HttpUrlDictionary::HTTPS)
					? 'https'
					: 'http'
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

	function setHost($host)
	{
		parent::setHost($host);

		if (!$this->baseHost) {
			$this->baseHost = $this->getHost();
		}

		return $this;
	}

	/**
	 * Gets the subdomain - a left part of the host with stripped base host
	 *
	 * @return string
	 */
	function getSubdomain()
	{
		if ($this->baseHost) {
			return substr($this->host, 0, -(1 + strlen($this->baseHost)));
		}
		else {
			return $this->host;
		}
	}

	/**
	 * Sets the subdomain prepending it to the base host
	 *
	 * @param string|null $subdomain host to be treated as subdomain, or NULL if need
	 * 			to drop the subdomain
	 *
	 * @return SiteUrl itself
	 */
	function setSubdomain($subdomain = null)
	{
		Assert::isScalarOrNull($subdomain);

		$host =
			$subdomain
				? $subdomain . '.' . $this->baseHost
				: $this->baseHost;

		$this->setHost($host);

		return $this;
	}

	/**
	 * Sets the base host
	 *
	 * @param string $baseHost host to be treated as base host
	 * @return SiteUrl itself
	 */
	function setBaseHost($baseHost)
	{
		Assert::isScalar($baseHost);

		$this->baseHost = $baseHost;

		return $this;
	}

	/**
	 * Gets the base host
	 *
	 * @return string|null
	 */
	function getBaseHost()
	{
		return $this->baseHost;
	}

	/**
	 * Gets the base path - a path between host and virtual path
	 *
	 * @return string
	 */
	function getBasePath()
	{
		return $this->basePath;
	}

	/**
	 * Sets the base path
	 *
	 * @param string $basePath path to be treated as base uri
	 *
	 * @return SiteUrl itself
	 */
	function setBasePath($basePath = '/')
	{
		Assert::isScalar($basePath);

		$basePath = '/' . ltrim($basePath, '/');

		if ($basePath == '/') {
			$basePath = null;
		}

		$this->basePath = $basePath;

		return $this;
	}

	/**
	 * Gets the virtual path.
	 *
	 * @return string
	 */
	function getVirtualPath()
	{
		$path = $this->getPath();

		if (!$this->basePath) {
			return $path;
		}

		$prefix = substr($path, 0, strlen($this->basePath));

		if ($this->basePath == $prefix) {
			$path = substr($path, strlen($this->basePath));
		}

		return $path;
	}

	/**
	 * Sets the virtual path
	 *
	 * @param string $path path to be presented after base uri
	 *
	 * @return SiteUrl
	 */
	function setVirtualPath($path)
	{
		$path = $this->basePath .  '/' . ltrim($path, '/');

		$this->setPath($path);

		return $this;
	}

	/**
	 * Clones the SiteUrl object and erases virtual path and subdomain leaving base host and
	 * base path untouched.
	 *
	 * @return SiteUrl
	 */
	function spawnBase()
	{
		$clone = clone $this;

		$clone->setVirtualPath('/');
		$clone->setSubdomain(null);

		return $clone;
	}
}

?>