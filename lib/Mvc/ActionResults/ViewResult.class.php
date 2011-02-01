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
 * Represents HTML markup and raw PHP code, handled by UITemplateControl.
 *
 * @ingroup Mvc_ActionResults
 */
class ViewResult implements IActionResult
{
	/**
	 * @var UITemplateControl
	 */
	private $view;

	function __construct(UITemplateControl $view)
	{
		$this->view = $view;
	}

	function handleResult(IWebResponse $response)
	{
		$this->view->render($response);

		$response->finish();
	}

}

?>