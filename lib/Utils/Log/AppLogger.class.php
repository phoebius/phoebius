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
 * Wrapper over file logger. Logs the messages to a file found at APP_ROOT/tmp/<scope>.log
 * where <scope> is the ctor argument.
 *
 * @ingroup Utils_Log
 */
final class AppLogger extends FileLogger
{
	function __construct($scope)
	{
		parent::__construct(
			APP_ROOT . DIRECTORY_SEPARATOR
			. 'tmp' . DIRECTORY_SEPARATOR
			. $scope . '.log'
		);
	}
}

?>