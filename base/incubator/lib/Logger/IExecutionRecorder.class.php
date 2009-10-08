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
interface IExecutionRecorder
{
	/**
	 * @return IExecutionRecorder
	 */
	function putLine();

	/**
	 * White
	 * @return IExecutionRecorder
	 */
	function putInfo($msg);

	/**
	 * White
	 * @return IExecutionRecorder
	 */
	function putInfoLine($msg);

	/**
	 * Blue
	 * @return IExecutionRecorder
	 */
	function putMsg($msg);

	/**
	 * Blue
	 * @return IExecutionRecorder
	 */
	function putMsgLine($msg);

	/**
	 * Yellow
	 * @return IExecutionRecorder
	 */
	function putWarning($msg);

	/**
	 * Yellow
	 * @return IExecutionRecorder
	 */
	function putWarningLine($msg);

	/**
	 * Red
	 * @return IExecutionRecorder
	 */
	function putError($msg);

	/**
	 * Red
	 * @return IExecutionRecorder
	 */
	function putErrorLine($msg);
}

?>