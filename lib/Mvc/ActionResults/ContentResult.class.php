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
 * Represents a textual result
 *
 * @ingroup Mvc_ActionResults
 */
class ContentResult implements IActionResult
{
	/**
	 * @var string
	 */
	private $content;

	/**
	 * @param string $content texutal data to be passed to response
	 */
	function __construct($content = null)
	{
		Assert::isScalarOrNull($content);

		$this->content = $content;
	}

	function handleResult(IViewContext $context)
	{
		$response = $context->getResponse();

		$response
			->write($this->content)
			->finish();
	}
}

?>