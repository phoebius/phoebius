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
 * @ingroup SysLoggers
 */
class HtmlExecutionRecorder implements IExecutionRecorder
{
	private $nlTag = '<br />';
	private $fontTagName = 'font';

	/**
	 * @var IWebResponse
	 */
	private $response;

	function __construct(IWebResponse $response = null)
	{
		$this->response =
			$response
				? $response
				: new WebResponse(false);
	}

	/**
	 * @return HtmlExecutionRecorder
	 */
	function putLine()
	{
		$this->response->out($this->nlTag);

		return $this;
	}

	/**
	 * White
	 * @return HtmlExecutionRecorder
	 */
	function putInfo($msg)
	{
		$this->response->out($msg);

		return $this;
	}

	/**
	 * White
	 * @return HtmlExecutionRecorder
	 */
	function putInfoLine($msg)
	{
		$this->putLine()->putInfo($msg);

		return $this;
	}

	/**
	 * Blue
	 * @return HtmlExecutionRecorder
	 */
	function putMsg($msg)
	{
		$this->response->out($this->colorize($msg, 'blue'));

		return $this;
	}

	/**
	 * Blue
	 * @return HtmlExecutionRecorder
	 */
	function putMsgLine($msg)
	{
		$this->putLine()->putMsg($msg);

		return $this;
	}

	/**
	 * Yellow
	 * @return HtmlExecutionRecorder
	 */
	function putWarning($msg)
	{
		$this->response->out($this->colorize($msg, 'magenta'));

		return $this;
	}

	/**
	 * Yellow
	 * @return HtmlExecutionRecorder
	 */
	function putWarningLine($msg)
	{
		$this->putLine()->putWarning($msg);

		return $this;
	}

	/**
	 * Red
	 * @return HtmlExecutionRecorder
	 */
	function putError($msg)
	{
		$this->response->out($this->colorize($msg, 'red'));

		return $this;
	}

	/**
	 * Red
	 * @return HtmlExecutionRecorder
	 */
	function putErrorLine($msg)
	{
		$this->putLine()->putError($msg);

		return $this;
	}

	/**
	 * @return string
	 */
	private function colorize($msg, $color)
	{
		return
			'<' . $this->fontTagName . ' color="'.$color.'">'
			. $msg
			. '</' . $this->fontTagName . '>';
	}
}

?>