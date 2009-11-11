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
 * @internal
 * @ingroup App_Routing_Web
 */
final class WebRequestVariable
{
	/**
	 * @var array
	 */
	private $predefinedValues = array();

	/**
	 * @var WebRequestPart
	 */
	private $requestType;

	/**
	 * @var boolean
	 */
	private $isOptional;

	/**
	 * @var mixed|null
	 */
	private $defaultValue;

	/**
	 * @return WebRequestVariable
	 */
	static function create(
			WebRequestPart $part = null,
			array $predefinedValues = array(),
			$isOptional = false,
			$defaultValue = null
		)
	{
		return new self ($part, $predefinedValues, $isOptional, $defaultValue);
	}

	function __construct(
			WebRequestPart $part = null,
			array $predefinedValues = array(),
			$isOptional = false,
			$defaultValue = null
		)
	{
		$this->requestType = $part
			? $part
			: WebRequestPart::get();
		$this->predefinedValues = $predefinedValues;
		$this->isOptional = $isOptional;
		$this->defaultValue = $defaultValue;
	}

	/**
	 * @return array
	 */
	function getPredefinedValues()
	{
		return $this->predefinedValues;
	}

	/**
	 * @return itneger
	 */
	function getPredefinedValuesCount()
	{
		return sizeof($this->predefinedValues);
	}

	/**
	 * @return mixed|null
	 */
	function getDefaultValue()
	{
		return $this->defaultValue;
	}

	/**
	 * @return boolean
	 */
	function hasDefaultValue()
	{
		return !is_null($this->defaultValue);
	}

	/**
	 * @return boolean
	 */
	function isOptional()
	{
		return $this->isOptional;
	}

	/**
	 * @return boolean
	 */
	function isRequired()
	{
		return !$this->isOptional;
	}

	/**
	 * @return WebRequestPart
	 */
	function getWebRequestPart()
	{
		return $this->requestType;
	}
}

?>