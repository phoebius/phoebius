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
 * Represents an execution context of a presentation view
 *
 * @ingroup Mvc
 */
interface IViewContext
{
	/**
	 * Gets the model passed to the presentation
	 *
	 * @return Model
	 */
	function getModel();

	/**
	 * Gets the handled Trace which caused view invokation
	 *
	 * @return Trace
	 */
	function getTrace();

	/**
	 * Gets the response presentation should be rendered to
	 *
	 * @return IWebResponse
	 */
	function getResponse();
}

?>