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
 * Contract for the bi-directional IWebContext rule.
 *
 * This rule analyzes an IWebContext and produced named parameters,
 * or composes a HttpUrl (as a part of an IWebContext) from a passed named parameters.
 *
 * @ingroup App_Web_Routing_Rules
 */
interface IRewriteRule
{
	/**
	 * Gets the (required) list of parameters that rule produces while rewriting or excepects
	 * while composing.
	 *
	 * @return array
	 */
	function getParameterList($requiredOnly = true);

	/**
	 * Analyzes an IWebContext and produces the list of named parameters.
	 *
	 * @param IWebContext $webContext context to analyze
	 *
	 * @throws RewriteException when IWebContext mismatch the rule and cannot being rewritten
	 * @return array list of resulting parameters
	 */
	function rewrite(IWebContext $webContext);

	/**
	 * Composes the SiteUrl by rewriting parameters back to the parts of IWebContext's URL
	 *
	 * @param SiteUrl $url URL to compose
	 * @param array $parameters parameters to rewrite back to URL
	 *
	 * @return void
	 */
	function compose(SiteUrl $url, array $parameters);
}

?>