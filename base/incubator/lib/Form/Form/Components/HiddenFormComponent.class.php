<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright notice
 *
 ************************************************************************************************/

/**
 * Represents the hidden field of the form
 *
 */
class HiddenFormComponent extends FormComponent
{
	function getType()
	{
		return "text";
	}

	private $defaultValue = null;

	/**
	 * Sets the default value for the hidden field.
	 *
	 * @param string $value
	 */
	function setDefaultValue($value)
	{
		$this->defaultValue = $value;
	}

	/**
	 * Gets the default value for the hidden field. If the value has not been set, NULL is return
	 *
	 * @return string
	 */
	function getDefaultvalue()
	{
		return $this->defaultValue;
	}
}

?>