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
 * @ingroup SysConsole
 */
class ConsoleOutput
{
	/**
	 * @var IAppResponse
	 */
	private $response;

	/**
	 * @var string
	 */
	private $eol = PHP_EOL;

	function __construct(IAppResponse $response = null)
	{
		$this->response =
			$response !== null
				? $response
				: new WebResponse(false);
	}

	/**
	 * @return ConsoleOutput
	 */
	function write($text)
	{
		$this->response->out($text);

		return $this;
	}

	/**
	 * @return ConsoleOutput
	 */
	function writeLine($text)
	{
		$this->response->out($this->eol . $text);

		return $this;
	}

	/**
	 * @return ConsoleOutput
	 */
	function newLine()
	{
		$this->response->out($this->eol);

		return $this;
	}

	/**
	 * @return ConsoleOutput
	 */
	function setMode(
			ConsoleAttribute $attribute,
			ConsoleForegroundColor $foreground,
			ConsoleBackgroundColor $background
		)
	{
		$mode =
			  chr(0x1B)
			. '['.$attribute->getValue() . ';'
			. $foreground->getValue() . ';'
			. $background->getValue() . 'm';

		$this->response->out($mode);

		return $this;
	}

	/**
	 * @return ConsoleOutput
	 */
	function setDefaultMode()
	{
		$this->setMode(
			new ConsoleAttribute(ConsoleAttribute::RESET_ALL),
			new ConsoleForegroundColor(ConsoleForegroundColor::WHITE),
			new ConsoleBackgroundColor(ConsoleBackgroundColor::BLACK)
		);

		return $this;
	}

	/**
	 * @return ConsoleOutput
	 */
	function resetAll()
	{
		echo chr(0x1B).'[0m';

		return $this;
	}
}

?>