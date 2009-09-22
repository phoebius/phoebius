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
 * @ingroup OrmCodeGenerator
 */
class DbSchemaCodeConstructor extends CodeConstructor
{
	/**
	 * @var DBSchema
	 */
	private $dbSchema;

	/**
	 * @return DbSchemaCodeConstructor
	 */
	static function create(DBSchema $dbSchema)
	{
		return new self ($dbSchema);
	}

	function __construct(DBSchema $dbSchema)
	{
		$this->dbSchema = $dbSchema;
	}

	function make(IWriteStream $writeStream)
	{
		$quoutedString = str_replace('\'', '\\\'', serialize($this->dbSchema));
		$code = <<<EOT
/**
 * @return DBSchema
 */
return unserialize('{$quoutedString}');
EOT;

		$writeStream
			->write($this->getFileHeader())
			->write($code)
			->write($this->getFileFooter());
	}
}

?>