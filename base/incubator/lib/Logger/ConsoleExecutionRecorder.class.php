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
class ConsoleExecutionRecorder implements IExecutionRecorder
{
	/**
	 * @var ConsoleOutput
	 */
	private $console;

	function __construct(ConsoleOutput $console)
	{
		$this->console = $console;
	}

	/**
	 * @return ConsoleExecutionRecorder
	 */
	function putLine()
	{
		$this->console->newLine();

		return $this;
	}

	/**
	 * White
	 * @return ConsoleExecutionRecorder
	 */
	function putInfo($msg)
	{
		$this->setInfoMode()->write($msg);

		return $this;
	}

	/**
	 * White
	 * @return ConsoleExecutionRecorder
	 */
	function putInfoLine($msg)
	{
		$this->setInfoMode()->writeLine($msg);

		return $this;
	}

	/**
	 * Blue
	 * @return ConsoleExecutionRecorder
	 */
	function putMsg($msg)
	{
		$this->setMsgMode()->write($msg);

		return $this;
	}

	/**
	 * Blue
	 * @return ConsoleExecutionRecorder
	 */
	function putMsgLine($msg)
	{
		$this->setMsgMode()->writeLine($msg);

		return $this;
	}

	/**
	 * Yellow
	 * @return ConsoleExecutionRecorder
	 */
	function putWarning($msg)
	{
		$this->setWarningMode()->write($msg);

		return $this;
	}

	/**
	 * Yellow
	 * @return ConsoleExecutionRecorder
	 */
	function putWarningLine($msg)
	{
		$this->setWarningMode()->writeLine($msg);

		return $this;
	}

	/**
	 * Red
	 * @return ConsoleExecutionRecorder
	 */
	function putError($msg)
	{
		$this->setErrorMode()->write($msg);

		return $this;
	}

	/**
	 * Red
	 * @return ConsoleExecutionRecorder
	 */
	function putErrorLine($msg)
	{
		$this->setErrorMode()->writeLine($msg);

		return $this;
	}

	/**
	 * @return ConsoleOutput
	 */
	private function setInfoMode()
	{
		$this->console->setMode(
			new ConsoleAttribute(ConsoleAttribute::RESET_ALL),
			new ConsoleForegroundColor(ConsoleForegroundColor::WHITE),
			new ConsoleBackgroundColor(ConsoleBackgroundColor::BLACK)
		);

		return $this->console;
	}

	/**
	 * @return ConsoleOutput
	 */
	private function setMsgMode()
	{
		$this->console->setMode(
			new ConsoleAttribute(ConsoleAttribute::RESET_ALL),
			new ConsoleForegroundColor(ConsoleForegroundColor::BLUE),
			new ConsoleBackgroundColor(ConsoleBackgroundColor::BLACK)
		);

		return $this->console;
	}

	/**
	 * @return ConsoleOutput
	 */
	private function setWarningMode()
	{
		$this->console->setMode(
			new ConsoleAttribute(ConsoleAttribute::RESET_ALL),
			new ConsoleForegroundColor(ConsoleForegroundColor::BLUE),
			new ConsoleBackgroundColor(ConsoleBackgroundColor::BLACK)
		);

		return $this->console;
	}

	/**
	 * @return ConsoleOutput
	 */
	private function setErrorMode()
	{
		$this->console->setMode(
			new ConsoleAttribute(ConsoleAttribute::RESET_ALL),
			new ConsoleForegroundColor(ConsoleForegroundColor::RED),
			new ConsoleBackgroundColor(ConsoleBackgroundColor::BLACK)
		);

		return $this->console;
	}

	function __destruct()
	{
		$this->console->resetAll();
	}
}

?>