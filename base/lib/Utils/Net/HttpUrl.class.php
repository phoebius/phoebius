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
 * @ingroup Net
 */
class HttpUrl extends Url
{
	/**
	 * @return HttpUrl
	 */
	static function import(HttpUrlDictionary $dictionary, HttpUrl $base = null)
	{
		$url = new self;

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

			if ($base && $base->getBase() && $base->getBase() != '/') {
				$url->setBase($base->getBase());
				$path = substr(
					$path,
					-1 * (strlen($path) - strlen($url->getBase()))
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
}

?>