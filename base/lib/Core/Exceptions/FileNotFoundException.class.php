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
class FileNotFoundException extends StateException
{
	private $filePath;

	function __construct($filePath, $message = 'file not found')
	{
		Assert::isScalar($filePath);

		parent::__construct($message);

		$this->filePath = $filePath;
	}
}

?>