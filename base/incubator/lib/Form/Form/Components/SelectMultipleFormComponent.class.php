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

class SelectMultipleFormComponent  extends SelectFormComponent
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