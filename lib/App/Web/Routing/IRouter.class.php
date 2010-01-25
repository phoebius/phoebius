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
 * Router interface.
 *
 * @ingroup App_Web_Routing
 */
interface IRouter
{
	/**
	 * Gets the Trace that can handle IWebContext, or null if no Route found that matches the IWebContext
	 *
	 * @param IWebContext $webContext the route should be matched against
	 *
	 * @return Trace|null
	 */
	function getTrace(IWebContext $webContext);

	/**
	 * Gets the Trace suitable for situation when the matched Trace was unable to handle the IWebContext
	 *
	 * @param Trace $parentTrace trace that was unable to handle the IWebContext
	 *
	 * @return Trace
	 */
	function getFallbackTrace(Trace $parentTrace);
}

?>