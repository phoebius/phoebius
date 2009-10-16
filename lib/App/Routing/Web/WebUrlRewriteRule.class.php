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
 * Url rewriting based on pattern.
 *
 * Pattern syntax:
 *
 *  [ [ predefinedValue | ( predefinedValue1 | predefinedValue2 | ... ) ] : parameterName ]
 *
 * where:
 *  `predefinedValue` - predefined set of values chunk should be matched against
 *  `parameterName` is the name of the parameter that grabs a string between slashes
 *
 *  / --> match root
 *  /:controller/:action/ --> /abc/def/ --> parameters { controller => abc, action => def }
 *  /:controller/:action/(add|edit):adminAction --> /abc/def/edit --> parameters { controller => abc, action => def, adminAction => edit }
 *
 *  /:controller/:action/?:id&:action
 *
 * @todo allow different delimiters of chunks, i.e. "-", ";",  etc.
 *
 * @ingroup WebRouting
 */
class WebUrlRewriteRule extends WebRequestRewriteRule
{
	/**
	 * @var string
	 */
	private $pattern;

	/**
	 * @var array of {@link WebUrlRewriteChunk}
	 */
	private $chunkRewriters = array();

	/**
	 * @return WebUrlRewriteRule an object itself
	 */
	static function create($pattern)
	{
		return new self ($pattern);
	}

	/**
	 * @param string $pattern
	 * @return WebUrlRewriteRule an object itself
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

		$this->pattern = '/'.trim($pattern, '/');

		$this->parsePattern();

		return $this;
	}

	/**
	 * @return string
	 */
	function getPattern()
	{
		return $this->pattern;
	}

	/**
	 * @param WebRequest $request
	 * @return boolean
	 */
	function isMatch(IAppRequest $request)
	{
		Assert::isTrue(
			$request instanceof WebRequest,
			'WebRequest is only valid request here'
		);

		do
		{
			if (!parent::isMatch($request)) {
				break;
			}

			$path = $request->getHttpUrl()->getPath();
			$chunks = explode('/', $path);
			array_shift($chunks);

			if (sizeof($chunks) != sizeof($this->chunkRewriters)) {
				break;
			}

			reset($this->chunkRewriters);
			foreach ($chunks as $chunk)
			{
				$chunkRewriter = current($this->chunkRewriters);

				Assert::isTrue($chunkRewriter instanceof WebUrlRewriteChunk);

				if (!$chunkRewriter->isMatch($chunk))
				{
					// break do-while cycle
					break 2;
				}

				next($this->chunkRewriters);
			}

			return true;

		} while (0);

		return false;
	}

	/**
	 * @throws RouteHandleException
	 * @return WebRequest
	 */
	function compose(Route $route, IAppRequest $request)
	{
		Assert::isTrue(
			$request instanceof WebRequest,
			'WebRequest rewriter only'
		);

		parent::compose($route, $request);

		$pathChunks = array();

		$parameters = $route->getParameters();

		foreach ($this->chunkRewriters as $chunkRewriter) {

			Assert::isTrue($chunkRewriter instanceof WebUrlRewriteChunk);

			if (isset($parameters[$chunkRewriter->getName()])) {
				$name = $chunkRewriter->getName();
				$value = $parameters[$name];

				if ($chunkRewriter->getPredefinedValuesCount()) {
					try {
						$pathChunks[] = $chunkRewriter->getMatchedValue(
							$value
						);
					}
					catch (ArgumentException $e) {
						throw new ParameterMatchException(
							$route,
							$name,
							$value,
							$chunkRewriter->getPredefinedValues()
						);
					}
				}
				else {
					$pathChunks[] = $value;
				}
			}
			else {
				if ($chunkRewriter->getPredefinedValuesCount() != 1) {
					throw new ParameterMissingException($route, $name);
				}

				$pathChunks[] = reset($chunkRewriter->getPredefinedValues());
			}
		}

		$request->getHttpUrl()->setPath(join('/', $pathChunks));

		return $request;
	}

	/**
	 * @throws RequestRewriteException
	 * @return Route
	 */
	function rewrite(IAppRequest $request, Route $route)
	{
		Assert::isTrue(
			$request instanceof WebRequest,
			'WebRequest is only valid request here'
		);

		parent::rewrite($request, $route);

		$pathChunks = explode('/', $request->getHttpUrl()->getPath());

		array_shift($pathChunks);
		reset($this->chunkRewriters);

		foreach ($pathChunks as $pathChunk) {
			$chunkRewriter = current($this->chunkRewriters);

			Assert::isTrue($chunkRewriter instanceof WebUrlRewriteChunk);

			try {
				$route->addParameter(
					$chunkRewriter->getName(),
					$chunkRewriter->getMatchedValue($pathChunk)
				);
			}
			catch (ArgumentException $e) {
				throw new ParameterMatchException(
					$route,
					$chunkRewriter->getName(),
					$pathChunk,
					$chunkRewriter->getPredefinedValues()
				);
			}

			next($this->chunkRewriters);
		}

		return $route;
	}

	/**
	 * @return void
	 */
	private function parsePattern()
	{
		$parsedUrlPattern = parse_url($this->pattern);

		if (isset($parsedUrlPattern['path'])) {
			$this->parsePath($parsedUrlPattern['path']);
		}

		if (isset($parsedUrlPattern['query'])) {
			$this->parseQueryString($parsedUrlPattern['query']);
		}
	}

	/**
	 * @return void
	 */
	private function parsePath($path)
	{
		$pathChunks = explode('/', $path);
		array_shift($pathChunks);
		foreach ($pathChunks as $pathChunk) {
			$this->chunkRewriters[] = WebUrlRewriteChunk::import($pathChunk);
		}

		if (sizeof($this->chunkRewriters) > 0) {
			foreach (array_slice($this->chunkRewriters, -1) as $qsChunkRewriter) {
				Assert::isFalse(
					$qsChunkRewriter->isGreedy(),
					'only the last one chunk can be specified as greedy'
				);
			}
		}
	}

	/**
	 * @return void
	 */
	private function parseQueryString($queryString)
	{
		$queryStringVariables = array();
		parse_str($queryString, $queryStringVariables);

		foreach ($queryStringVariables as $key => $value) {
			$qsChunkRewriter = WebUrlRewriteChunk::import($key);

			Assert::isNotEmpty(
				$qsChunkRewriter->getName(),
				'QS parameter should be named'
			);

			$this->addRequestVariable(
				$qsChunkRewriter->getName(),
				WebRequestPart::get(),
				$qsChunkRewriter->getPredefinedValues(),
				!empty($value),
				$value ? $value : null
			);
		}
	}
}

?>