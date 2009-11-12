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
 * Url rewriting based on pattern.
 *
 * Pattern syntax:
 *
 *  constant_value | [ predefined_value | ( predefined_value_1 | predefined_value_2 | ... ) ] :parameter_name
 *
 * where:
 *  * constant_value - freezed chunk
 *  * predefined_value - possible chunk value the parameter should match
 *  * predefined_value_1, predefined_value_2, ... - set of possbile chunk values the parameter should match
 *  * parameter_name - the name to which the value of the chunk will be assigned to
 *
 * Examples:
 *  * "/" matches the root
 *  * "/blog" matches "/blog", "/blog/" AND "/blog/blah/blah"
 *  * "/blog/" matches "/blog/" only
 *  * "/:controller" matches "/<anything>", "/<anything>/" AND "/<anything>/blah/blah", and assigns "<anything>" to "controller"
 *  * "/:controller*" matches "/<anything>" (controller="<anything>"), "/blog/" (controller="<anything>") and "/<anything>/blah/blah" (controller="<anything>/blah/blah")
 *  * "/:controller/" matches "/<anything>/" and assigns "<anything>" to "controller"
 *  * "/:controller* /" is a nonsense. Will match only "/<anything>/" and nothing else.
 *  * "/blog:controller/ matches "/blog/" only.
 *  * "/:controller/(rss|html):action/" matches "/<anything>/rss/" (controller="<anything>", action="rss") and "/<anything>/html/" (controller="<anything>", action="html")
 *
 * @ingroup App_Web_Routing_Rules
 */
class PathRewriteRule implements IRewriteRule
{
	/**
	 * @var string
	 */
	private $urlPattern;

	/**
	 * @var array of {@link WebUrlRewriteChunk}
	 */
	private $chunkRewriters = array();

	/**
	 * @param string $pattern
	 */
	function __construct($pattern)
	{
		$this->setPattern($pattern);
	}

	/**
	 * @return WebUrlRewriteRule an object itself
	 */
	function setPattern($pattern)
	{
		Assert::isScalar($pattern);

		$this->urlPattern = '/' . ltrim($pattern, '/');

		$this->parsePattern();

		return $this;
	}

	/**
	 * @return string
	 */
	function getPattern()
	{
		return $this->urlPattern;
	}

	/**
	 * @return void
	 */
	function compose(HttpUrl $url, array $parameters)
	{
		$pathChunks = array();

		foreach ($this->chunkRewriters as $chunkRewriter) {
			$values = $chunkRewriter->getValues();

			if (
					($name = $chunkRewriter->getName())
					&& 1 != sizeof($values)
			) {
				Assert::isTrue(
					isset($parameters[$name]),
					'%s expects parameter %s to be specified for pattern `%s`',
					get_class($this),
					$name,
					$this->urlPattern
				);

				if (sizeof($values)) {
					Assert::isTrue(
						in_array($parameters[$name], $values),
						'%s expects parameter %s to be in range as defined by pattern `%s`',
						get_class($this),
						$name,
						$this->urlPattern
					);
				}

				$pathChunks[] = $parameters[$name];
			}
			else {
				$pathChunks[] = reset($values);
			}
		}

		$url->setVirtualPath(
			join('/', $pathChunks)
		);
	}
	/**
	 * @return array
	 */
	function getParameterList($requiredOnly = true)
	{
		$yield = array();

		foreach ($this->chunkRewriters as $chunkRewriter) {
			if (
					($name = $chunkRewriter->getName())
					&& (
						!$chunkRewriter->getValueCount()
						|| !$requiredOnly
					)
			) {
				$yield[] = $chunkRewriter->getName();
			}
		}

		return $yield;
	}

	/**
	 * @throws RewriteException
	 * @return array
	 */
	function rewrite(IWebContext $webContext)
	{
		$pathChunks = explode('/', $webContext->getRequest()->getHttpUrl()->getVirtualPath());
		array_shift($pathChunks);
		reset($pathChunks);

		if (sizeof($this->chunkRewriters) > sizeof($pathChunks)) {
			throw new RewriteException('wrong path chunk count');
		}

		$parameters = array();

		foreach ($this->chunkRewriters as $chunkRewriter) {

			if (($name = $chunkRewriter->getName())) {
				$pathChunk = current($pathChunks);

				if ($chunkRewriter->isLast() && $chunkRewriter->isGreedy()) {
					$pathChunk = array_slice($pathChunks, key($pathChunks));
				}

				if (
						$chunkRewriter->getValueCount()
						&& !in_array($pathChunk, $chunkRewriter->getValues())
				) {
					throw new RewriteException('path chunks` value should be in set of predefined values');
				}

				$parameters[$name] = $pathChunk;
			}

			next($pathChunks);
		}

		return $parameters;
	}

	/**
	 * @return void
	 */
	private function parsePattern()
	{
		$pathChunks = explode('/', $this->urlPattern);
		array_shift($pathChunks);

		foreach ($pathChunks as $pathChunk) {
			$this->chunkRewriters[] = WebUrlRewriteChunk::import($pathChunk);
		}

		if (sizeof($this->chunkRewriters) > 0) {
			end($this->chunkRewriters)->setLast();
		}
	}
}

?>