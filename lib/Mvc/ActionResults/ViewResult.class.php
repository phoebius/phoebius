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
 * Represents HTML and markup
 * IView + Model
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

	/**
	 * @return void
	 */
	function handleResult(IViewContext $context)
	{
		$this->view->render(
			$context->getResponse()
		);

		$context->getResponse()->finish();
	}

}

?>