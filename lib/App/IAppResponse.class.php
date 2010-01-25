<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright (c) 2009 Scand Ltd.
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
 * Basic application response interface
 *
 * @ingroup App
 */
interface IAppResponse extends IOutput
{
	/**
	 * Finishes the request by passing the response to the client and closes a connection.
	 *
	 * This can be useful when the script should do a task that does not depend on the response.
	 *
	 * @return void
	 */
	function finish();

	/**
	 * Checks whether response is finished.
	 *
	 * @return boolean
	 */
	function isFinished();
}

?>