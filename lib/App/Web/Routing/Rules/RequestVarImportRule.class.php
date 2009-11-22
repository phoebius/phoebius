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
 * Gets the variable from the request and imports it as a parameter into the Trace.
 *
 * @ingroup App_Web_Routing_Rules
 */
class RequestVarImportRule implements IRewriteRule
{
	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var WebRequestPart
	 */
	private $requestPart;

	/**
	 * @var mixed|null
	 */
	private $defaultValue;

	/**
	 * @var boolean
	 */
	private $isOptional;

	/**
	 * @param string $name name of the varible to import
	 * @param WebRequestPart $part part of the request to look up. Default is GET
	 * @param boolean $isOptional whether parameter is optional
	 * @param mixed $defaultValue parameter's default value to use in case when variable is
	 * 			not presented within the IWebContext
	 */
	function __construct(
			$name,
			WebRequestPart $part = null,
			$isOptional = false,
			$defaultValue = null
		)
	{
		Assert::isScalar($name);
		Assert::isBoolean($isOptional);

		$this->name = $name;
		$this->requestPart =
			$part
				? $part
				: new WebRequestPart(WebRequestPart::GET);
		$this->isOptional = $isOptional;
		$this->defaultValue = $defaultValue;
	}

	function getParameterList($requiredOnly = true)
	{
		Assert::isBoolean($requiredOnly);

		if ($requiredOnly && $this->isOptional) {
			return array();
		}

		return array($this->name);
	}

	function rewrite(IWebContext $webContext)
	{
		$request = $webContext->getRequest();

		if (!$request->hasVar($this->name, $this->requestPart)) {
			if ($this->isOptional) {
				$value = $this->defaultValue;
			}
			else {
				throw new RewriteException(
					"variable {$this->name} is not defined by the request",
					$this,
					$webContext
				);
			}
		}
		else {
			$value = $request->getVar($this->name, $this->requestPart);
		}

		return array(
			$this->name => $value
		);
	}

	function compose(SiteUrl $url, array $parameters)
	{
		if ($this->requestPart->is(WebRequestPart::GET)) {
			if (array_key_exists($this->name, $parameters)) {
				$url->addQueryArgument($this->name, (string) $parameters[$this->name]);
			}
			else if (!$this->isOptional) {
				if ($this->defaultValue) {
					$url->addQueryArgument($this->name, $this->defaultValue);
				}
				else {
					// FIXME: use exception
					Assert::isUnreachable(
						'missing %s parameter',
						$this->name
					);
				}
			}
		}
	}
}

?>