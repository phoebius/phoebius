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

class CheckboxFormComponent extends FormComponent
{
	static function create($name)
	{
		return new self($name);
	}

	function getType()
	{
		return "multiple";
	}

}

?>