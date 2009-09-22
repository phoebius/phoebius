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

class ButtonFormComponent extends FormComponent
{
	private $callback;

	static function create($name)
	{
		return new self($name);
	}

	function buttonHandlerPrototype(Form $owner)
	{}

	function setCallback(IDelegate $object)
	{
		$this->callback = $object;
	}

	function call(Form $owner)
	{
		$this->callback->Invoke($owner);
	}

	function getType()
	{
		return 'action';
	}
}

?>