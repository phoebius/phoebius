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
 * @ingroup OrmDomainImporter
 */
class DBSchemaImporter
{
	/**
	 * @return DBSchemaImporter
	 */
	static function create()
	{
		return new self;
	}

	/**
	 * @return DBSchema
	 */
	function import(OrmDomain $ormDomain, DBSchema $dbSchema)
	{
		DBSchemaImportWorker::create($ormDomain, $dbSchema)->import();

		return $dbSchema;
	}
}

?>