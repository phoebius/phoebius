<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright (c) 2010 phoebius.org
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
 * A logger that pushes the string to be logged into stdout
 *
 * @ingroup Utils_Log
 */
final class StdOutLogger implements ILogger
{
	function log($string)
	{
		echo $string, '<br />', PHP_EOL;

		return $this;
	}
}

?>