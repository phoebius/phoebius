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
 * Interface for handling a Trace object.
 *
 * @ingroup App_Web_Routing
 */
interface IRouteDispatcher
{
	/**
	 * Obtains the controller object and invokes it to handle the incoming context
	 *
	 * @param Trace $trace to handle
	 * @throws TraceException when Trace is missing valid parameters
	 * @throws Exception unhandled application fault
	 * @return void
	 */
	function handle(Trace $trace);
}

?>