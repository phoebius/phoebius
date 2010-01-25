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
 * Represents a JavaScript Object Notation result that can be used in an AJAX application
 *
 * @ingroup Mvc_ActionResults
 */
class JsonResult extends ContentResult
{
	/**
	 * @param array $json data to be presented in JSON
	 */
	function __construct(array $json)
	{
		parent::__construct(json_encode($json));
	}
}

?>