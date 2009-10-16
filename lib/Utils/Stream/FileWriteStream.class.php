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
 * @ingroup Stream
 */
class FileWriteStream implements IWriteStream
{
	/**
	 * @var string
	 */
	private $filename;

	function __construct($filename)
	{
		Assert::isScalar($filename);

		$this->filename = $filename;

		file_put_contents($filename, null);
	}

	/**
	 * @return FileWriteStream
	 */
	function write($buffer)
	{
		file_put_contents($this->filename, $buffer, FILE_APPEND);

		return $this;
	}
}

?>