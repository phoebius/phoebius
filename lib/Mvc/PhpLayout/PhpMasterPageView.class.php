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
 * ContentBuffer getter is not represented here specially
 * @ingroup PhpLayout
 */
class PhpMasterPageView extends PhpPageView
{
	/**
	 * @var array
	 */
	private $contentBuffers = array();

	/**
	 * @var string|null
	 */
	private $contentPlaceholderCurrentId = null;

	/**
	 * @var integer
	 */
	private $contentPlaceholderCurrentLevel = 0;

	/**
	 * @throws ArgumentException
	 * @return PhpMasterPageViewan object itsef
	 */
	function addContentBuffer($contentId, $content)
	{
		if (isset($this->contentBuffers[$contentId])) {
			throw new ArgumentException('contentId', 'already set');
		}

		$this->setContentBuffer($contentId, $content);

		return $this;
	}

	/**
	 * @return PhpMasterPageView an object itsef
	 */
	function setContentBuffer($contentId, $content)
	{
		Assert::isScalar($contentId);
		Assert::isScalar($content);

		$this->contentBuffers[$contentId] = $content;

		return $this;
	}

	/**
	 * @throws ArgumentException
	 * @return PhpMasterPageView an object itsef
	 */
	function addContentBuffers(array $contentBuffers)
	{
		foreach ($contentBuffers as $contentId => $content) {
			$this->addContentBuffer($contentId, $content);
		}

		return $this;
	}

	/**
	 * @return PhpMasterPageView an object itsef
	 */
	function setContentBuffers(array $contentBuffers)
	{
		$this->dropContentBuffers()->addContentBuffers($contentBuffers);

		return $this;
	}

	/**
	 * @return PhpMasterPageView an object itsef
	 */
	function dropContentBuffers()
	{
		$this->contentBuffers = array();

		return $this;
	}

	/**
	 * @return string
	 */
	function getContent($contentId, $defaultContent = null)
	{
		$this->assertInsideRenderingContext();

		Assert::isTrue(
			isset($this->contentBuffers[$contentId]) || is_scalar($defaultContent)
		);

		return isset($this->contentBuffers[$contentId])
			? $this->contentBuffers[$contentId]
			: $defaultContent;
	}

	/**
	 * @return void
	 */
	function beginContentPlaceholder($contentId)
	{
		Assert::isScalar($contentId);

		$this->assertInsideRenderingContext();

		Assert::isEmpty(
			$this->contentPlaceholderCurrentId,
			'already in buffer %s',
			$this->contentPlaceholderCurrentId
		);

		ob_start();

		$this->contentPlaceholderCurrentLevel = ob_get_level();
	}

	/**
	 * @return void
	 */
	function endContentPlaceholder()
	{
		$this->assertInsideRenderingContext();

		Assert::isNotEmpty(
			$this->contentPlaceholderCurrentId,
			'content placeholder not yet started'
		);

		Assert::isTrue(
			$this->contentBufferCurrentLevel == ob_get_level(),
			'unknown buffers inside view are not closed'
		);

		$buffer = ob_get_clean();

		$this->getViewContext()->getAppContext()->getResponse()->out(
			isset($this->contentBuffers[$this->contentPlaceholderCurrentId])
				? $this->contentBuffers[$this->contentPlaceholderCurrentId]
				: $buffer
		);

		$this->contentPlaceholderCurrentId = null;
		$this->contentPlaceholderCurrentLevel = 0;
	}

	/**
	 * @return void
	 */
	function cleanContentPlaceholder()
	{
		$this->assertInsideRenderingContext();

		Assert::isNotEmpty(
			$this->contentPlaceholderCurrentId,
			'content placeholder not yet started'
		);

		Assert::isTrue(
			$this->contentBufferCurrentLevel == ob_get_level(),
			'unknown buffers inside view are not closed'
		);

		ob_end_clean();

		$this->contentPlaceholderCurrentId = null;
		$this->contentPlaceholderCurrentLevel = 0;
	}

	/**
	 * @return void
	 */
	function render(IViewContext $context)
	{
		parent::render($context);

		if ($this->contentPlaceholderCurrentId) {
			$this->endContent();
		}
	}
}

?>