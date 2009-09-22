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
 * @ingroup CoreExceptions
 */
class DuplicationException extends ArgumentException
{
	private $value;

	function __construct($argName, $argValue, $message = 'key violation')
	{
		parent::__construct($argName, $message);

		$this->value = $argValue;
	}

	/**
	 * @return mixed
	 */
	function getArgumentValue()
	{
		return $this->value;
	}
}

?>