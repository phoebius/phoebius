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
 * @ingroup Mvc_ActionResults
 */
class ContentResult implements IActionResult
{
	/**
	 * @var string
	 */
	private $content;

	/**
	 * @return ContentResult
	 */
	static function create($content)
	{
		return new self ($content);
	}

	function __construct($content)
	{
		Assert::isScalarOrNull($content);

		$this->content = $content;
	}

	/**
	 * @return void
	 */
	function handleResult(IViewContext $context)
	{
		$context->getController()->getContext()->getAppContext()->getResponse()
			->write($this->content)
			->finish();
	}
}

?>