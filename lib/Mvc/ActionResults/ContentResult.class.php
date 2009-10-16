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
 * @ingroup ActionResults
 */
class ContentResult implements IActionResult
{
	/**
	 * @var string
	 */
	private $content;

	/**
	 * @return ContentResult
	 */
	static function create($content)
	{
		return new self ($content);
	}

	function __construct($content)
	{
		Assert::isScalarOrNull($content);

		$this->content = $content;
	}

	/**
	 * @return void
	 */
	function handleResult(IViewContext $context)
	{
		$context->getAppContext()->getResponse()
			->out($this->content)
			->finish();
	}
}

?>