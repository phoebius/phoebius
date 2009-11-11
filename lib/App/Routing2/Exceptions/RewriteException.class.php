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
 * @ingroup App_Routing_Exceptions
 */
class RewriteException extends StateException
{
	/**
	 * @var IRewriteRule
	 */
	private $rewriteRule;

	/**
	 * @var IWebContext
	 */
	private $webContext;

	function __construct(
			IRewriteRule $rewriteRule,
			IWebContext $webContext,
			$message = 'rule does not match'
		)
	{
		$this->rewriteRule = $rewriteRule;
		$this->webContext = $webContext;

		parent::__construct($message);
	}
}

?>