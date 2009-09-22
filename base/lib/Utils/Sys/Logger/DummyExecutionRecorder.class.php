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
final class DummyExecutionRecorder implements IExecutionRecorder
{
	/**
	 * @return IExecutionRecorder
	 */
	function putLine()
	{
		return $this;
	}

	/**
	 * White
	 * @return IExecutionRecorder
	 */
	function putInfo($msg)
	{
		return $this;
	}

	/**
	 * White
	 * @return IExecutionRecorder
	 */
	function putInfoLine($msg)
	{
		return $this;
	}

	/**
	 * Blue
	 * @return IExecutionRecorder
	 */
	function putMsg($msg)
	{
		return $this;
	}

	/**
	 * Blue
	 * @return IExecutionRecorder
	 */
	function putMsgLine($msg)
	{
		return $this;
	}

	/**
	 * Yellow
	 * @return IExecutionRecorder
	 */
	function putWarning($msg)
	{
		return $this;
	}

	/**
	 * Yellow
	 * @return IExecutionRecorder
	 */
	function putWarningLine($msg)
	{
		return $this;
	}

	/**
	 * Red
	 * @return IExecutionRecorder
	 */
	function putError($msg)
	{
		return $this;
	}

	/**
	 * Red
	 * @return IExecutionRecorder
	 */
	function putErrorLine($msg)
	{
		return $this;
	}
}

?>