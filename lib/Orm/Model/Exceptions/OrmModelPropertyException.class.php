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
 * @ingroup OrmModelExceptions
 */
class OrmModelPropertyException extends OrmModelException
{
	private $property;

	function __construct(OrmProperty $property, $message)
	{
		$this->property = $property;

		parent::__construct($message);
	}

	/**
	 * @return OrmProperty
	 */
	function getProperty()
	{
		return $this->property;
	}
}

?>