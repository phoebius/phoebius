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
class ConsoleAttribute extends Enumeration
{
	const RESET_ALL = 0;
	const BOLD = 1;
	const HALF_BRIGHT = 2;
	const UNDERSCORE = 4;
	const BLINK = 5;
	const REVERSE_VIDEO = 7;

	// unused attributes: 10, 11, 12, 21, 22, 24, 25, 27
}

?>