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
 * @ingroup UI
 */
class UIMasterPage extends UIPage
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
	 * @var IOutput
	 */
	private $output;

	/**
	 * @var string
	 */
	private $defaultContent;

	/**
	 * @throws ArgumentException
	 * @return UIMasterPage
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
	 * @return UIMasterPage an object itsef
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
	 * @return UIMasterPage an object itsef
	 */
	function addContentBuffers(array $contentBuffers)
	{
		foreach ($contentBuffers as $contentId => $content) {
			$this->addContentBuffer($contentId, $content);
		}

		return $this;
	}

	/**
	 * @return UIMasterPage an object itsef
	 */
	function setContentBuffers(array $contentBuffers)
	{
		$this->dropContentBuffers()->addContentBuffers($contentBuffers);

		return $this;
	}

	/**
	 * @return UIMasterPage an object itsef
	 */
	function dropContentBuffers()
	{
		$this->contentBuffers = array();

		return $this;
	}

	/**
	 * @return void
	 */
	function render(IOutput $output)
	{
		$this->output = $output;

		parent::render($output);

		if ($this->contentPlaceholderCurrentId) {
			$this->endContent();
		}

		$this->output = null;
	}

	/**
	 * @return UIMasterPage
	 */
	function setDefaultContent($content)
	{
		Assert::isScalar($content);

		$this->defaultContent = $content;

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
	 * @return string
	 */
	function getDefaultContent()
	{
		return $this->defaultContent;
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
			'unknown buffers inside %s are not closed',
			get_class($this)
		);

		$buffer = ob_get_clean();

		$this->output->write(
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
			'unknown buffers inside %s are not closed',
			get_class($this)
		);

		ob_end_clean();

		$this->contentPlaceholderCurrentId = null;
		$this->contentPlaceholderCurrentLevel = 0;
	}
}

?>