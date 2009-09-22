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
final class RawConsoleOutput extends ConsoleOutput
{
	/**
	 * @return RawConsoleOutput
	 */
	function setMode(
			ConsoleAttribute $attribute,
			ConsoleForegroundColor $foreground,
			ConsoleBackgroundColor $background
		)
	{
		return $this;
	}

	/**
	 * @return ConsoleOutput
	 */
	function setDefaultMode()
	{
		return $this;
	}

	/**
	 * @return RawConsoleOutput
	 */
	function resetAll()
	{
		return $this;
	}
}

?>