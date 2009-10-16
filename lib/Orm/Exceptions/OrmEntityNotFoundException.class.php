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
 * @ingroup OrmExceptions
 */
class OrmEntityNotFoundException extends ObjectNotFoundException
{
	/**
	 * @var ILogicallySchematic
	 */
	private $logicalSchema;

	function __construct(ILogicallySchematic $logicalSchema)
	{
		$this->logicalSchema = $logicalSchema;

		parent::__construct();
	}

	/**
	 * @return ILogicallySchematic
	 */
	function getLogicalSchema()
	{
		return $this->logicalSchema;
	}
}

?>