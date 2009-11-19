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
 * This rule simply imports the specified key=value pairs as parameters into the Trace.
 *
 * @ingroup App_Web_Routing_Rules
 */
class ParameterImportRule implements IRewriteRule
{
	/**
	 * @var string
	 */
	private $name;
	
	/**
	 * @var mixed
	 */
	private $value;
	
	/**
	 * @return array array of ParameterImportRule
	 */
	static function multiple(array $parameters)
	{
		$rules = array();
		
		foreach ($parameters as $parameter => $value) {
			$rules[] = new self($parameter, $value);
		}
		
		return $rules;
	}
	
	function __construct($name, $value)
	{
		Assert::isScalar($name);
		
		$this->name = $name;
		$this->value = $value;
	}
	
	/**
	 * @return array
	 */
	function getParameterList($requiredOnly = true)
	{
		return array();
	}
	
	/**
	 * @throws RewriteException
	 * @return array
	 */
	function rewrite(IWebContext $webContext)
	{
		return array(
			$this->name => $this->value
		);
	}
	
	/**
	 * @return void
	 */
	function compose(SiteUrl $url, array $parameters)
	{
		// nothing
	}
}

?>