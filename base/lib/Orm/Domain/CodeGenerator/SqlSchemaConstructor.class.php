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
class SqlSchemaConstructor
{
	/**
	 * @var DBSchema
	 */
	private $dbSchema;

	/**
	 * @return SqlSchemaConstructor
	 */
	static function create(DBSchema $dbSchema)
	{
		return new self ($dbSchema);
	}

	function __construct(DBSchema $dbSchema)
	{
		$this->dbSchema = $dbSchema;
	}

	/**
	 * @return void
	 */
	function make(IWriteStream $writeStream, IDialect $dialect)
	{
		$now = date('d.m.y H:i');

		$start = <<<EOT
--
-- Phoebius Framework Autogenerator
-- Generated at {$now} for {$dialect->getDBDriver()->getValue()}
--

EOT;

		$writeStream
			->write($start)
			->write(
				$this->dbSchema->toDialectString($dialect)
			);
	}
}

?>