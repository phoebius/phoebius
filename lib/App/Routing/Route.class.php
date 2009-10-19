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
 * @ingroup App_Routing
 */
class Route
{
	/**
	 * @var array
	 */
	private $parameters = array();

	/**
	 * @var boolean
	 */
	private $isHandled = false;

	/**
	 * @return boolean
	 */
	function isHandled()
	{
		return $this->isHandled;
	}

	/**
	 * @return Route an object itself
	 */
	function setHandled()
	{
		$this->isHandled = true;

		return $this;
	}

	/**
	 * @return Route an object itself
	 */
	function setNotHandled()
	{
		$this->isHandled = false;

		return $this;
	}

	/**
	 * @return Route an object itself
	 */
	function setParameters(array $parameters)
	{
		$this->parameters = $parameters;

		return $this;
	}

	/**
	 * @return Route an object itself
	 */
	function setParameter($parameterName, $value)
	{
		$this->parameters[$parameterName] = $value;

		return $this;
	}

	/**
	 * @return Route an object itself
	 */
	function addParameters(array $parameters)
	{
		$this->parameters += $parameters;

		return $this;
	}

	/**
	 * @return Route an object itself
	 */
	function addParameter($parameterName, $value)
	{
		$this->parameters[$parameterName] = $value;

		return $this;
	}

	/**
	 * @return array
	 */
	function getParameters()
	{
		return $this->parameters;
	}

	/**
	 * @throws ArgumentException
	 * @return mixed
	 */
	function getParameter($parameterName, IAppRequest $lookup = null)
	{
		if (isset($this->parameters[$parameterName])) {
			return $this->parameters[$parameterName];
		}

		if ($lookup) {
			return $lookup->getAnyVariable($parameterName);
		}

		throw new ArgumentException('parameter', 'requested parameter not found in parameter list');
	}
}

?>