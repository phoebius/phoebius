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
 * @ingroup Utils_Sys
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