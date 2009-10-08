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

class FileFormComponent extends FormComponent
{
	static function create($name)
	{
		return new self($name);
	}

	function getType()
	{
		return "file";
	}
}

?>