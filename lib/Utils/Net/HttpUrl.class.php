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
class HttpUrl implements IStringCastable
{
	// ports to be ommited
	const DEFAULT_HTTP_PORT = 80;
	const DEFAULT_HTTPS_PORT = 443;

	private $scheme = 'http';
	private $user = null;
	private $pass = null;
	private $host = null;
	private $port = null;
	private $path = '/';
	private $query = array();
	private $fragment = '';

	function __construct($url = null)
	{
		if ($url) {
			$chunks = parse_url($url);

			if (isset($chunks['scheme'])) {
				$this->setScheme($chunks['scheme']);
			}

			if (isset($chunks['host'])) {
				$this->setHost($chunks['host']);
			}

			if (isset($chunks['port'])) {
				$this->setPort($chunks['port']);
			}

			if (isset($chunks['user'])) {
				$user = $chunks['user'];
				if (isset($chunks['pass'])) {
					$pass = $chunks['pass'];
				}
				else {
					$pass = null;
				}
				$this->setCredentials($user, $pass);
			}

			if (isset($chunks['path'])) {
				$this->setPath($chunks['path']);
			}

			if (isset($chunks['query'])) {
				$query = array();
				parse_str($chunks['query'], $query);
				$this->setQuery($query);
			}

			if (isset($chunks['fragment'])) {
				$this->setFragment($chunks['fragment']);
			}
		}
	}

	/**
	 * @return string
	 */
	function getScheme()
	{
		return $this->scheme;
	}

	/**
	 * @param string $scheme
	 * @return HttpUrl an object itself
	 */
	function setScheme($scheme)
	{
		Assert::isScalar($scheme);

		$this->scheme = $scheme;

		return $this;
	}

	/**
	 * @return string|null
	 */
	function getUser()
	{
		return $this->user;
	}

	/**
	 * @return string|null
	 */
	function getPassword()
	{
		return $this->pass;
	}

	/**
	 * @return HttpUrl an object itself
	 */
	function setCredentials($user, $password)
	{
		Assert::isScalarOrNull($user);
		Assert::isScalarOrNull($password);

		$this->user = $user;
		$this->pass = $password;

		return $this;
	}

	/**
	 * @return string|null
	 */
	function getHost()
	{
		return $this->host;
	}

	/**
	 * @param string $host
	 * @return HttpUrl an object itself
	 */
	function setHost($host)
	{
		Assert::isScalar($host);

		$this->host = $host;

		return $this;
	}

	/**
	 * @return integer|null
	 */
	function getPort()
	{
		return $this->port;
	}

	/**
	 * @param integer $port
	 * @return HttpUrl an object itself
	 */
	function setPort($port)
	{
		Assert::isNumeric($port);

		$this->port = $port;

		return $this;
	}

	/**
	 * @return string
	 */
	function getPath()
	{
		return $this->path;
	}

	/**
	 * @param string $path
	 * @return HttpUrl an object itself
	 */
	function setPath($path)
	{
		Assert::isScalar($path);

		$this->path = '/' . ltrim($path, '/');

		return $this;
	}

	/**
	 * @return array
	 */
	function getQuery()
	{
		return $this->query;
	}

	/**
	 * @param array $query
	 * @return HttpUrl an object itself
	 */
	function setQuery(array $query)
	{
		$this->query = $query;

		return $this;
	}

	/**
	 * @return HttpUrl an object itself
	 */
	function addQueryArgument($key, $value)
	{
		Assert::isScalar($key);
		Assert::isScalar($value);

		$this->query[$key] = $value;

		return $this;
	}

	/**
	 * @return HttpUrl an object itself
	 */
	function mergeQuery(array $query)
	{
		$this->query = $this->query + $query;

		return $this;
	}

	/**
	 * @param string $query
	 * @return HttpUrl an object itself
	 */
	function dropQuery()
	{
		$this->query = array();

		return $this;
	}

	/**
	 * @return string
	 */
	function getQueryAsString()
	{
		$pairs = $this->getQuery();

		if (sizeof($pairs)) {
			$query = http_build_query($pairs);
			$query = '?' . $query;
		}
		else {
			$query = '';
		}

		return $query;
	}

	/**
	 * @return string
	 */
	function getFragment()
	{
		return $this->fragment;
	}

	/**
	 * @param string $fragment
	 * @return HttpUrl an object itself
	 */
	function setFragment($fragment)
	{
		Assert::isScalar($fragment);

		$this->fragment = ltrim($fragment, '#');

		return $this;
	}

	/**
	 * @return string path + qs
	 */
	function getUri()
	{
		return $this->getPath() . $this->getQueryAsString();
	}

	function __toString()
	{
		$out = array();

		// Scheme
		if ($this->getScheme() && $this->getHost()) {
			$out[] = $this->getScheme() . '://';
		}

		// Auth
		if (($user = $this->getUser()) && ($pass = $this->getUser())) {
			$out[] = rawurlencode($user) . ':';
			$out[] = rawurlencode($pass) . '@';
		}
		else if ($user) {
			$out[] = rawurlencode($user) . '@';
		}


		// Host
		$out[] = $this->getHost();

		// Port
		if ($this->getHost() && $this->getPort()) {
			if (
					   !(
					   		   $this->getScheme() == 'http'
					   		&& $this->getPort() == self::DEFAULT_HTTP_PORT
					)
					&& !(
							   $this->getScheme() == 'https'
							&& $this->getPort() == self::DEFAULT_HTTPS_PORT
					)
			) {
				$out[] = ':' . $this->getPort();
			}
		}

		$out[] = $this->encodePath($this->getPath());
		$out[] = $this->getQueryAsString();

		if ($this->getFragment()) {
			$out[] = '#' . urlencode($this->getFragment());
		}

		return join('', $out);
	}

	/**
	 * @return string
	 */
	private function encodePath($path)
	{
		$chunks = preg_split('/([\/;=])/', $path, - 1, PREG_SPLIT_DELIM_CAPTURE);
		$path = '';
		foreach ($chunks as $var) {
			switch ( $var )
			{
				case "/":
				case ";":
				case "=":
					{
						$path .= $var;
						break;
					}
				default:
					{
						$path .= rawurlencode($var);
					}
			}
		}
		// legacy patch for servers that need a literal /~username
		$path = str_replace('/%7E', '/~', $path);

		return $path;
	}
}

?>