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

class RadioFormComponent  extends FormComponent
{
	static function create($name)
	{
		return new self($name);
	}

	function getType()
	{
		return "single";
	}
}

?>