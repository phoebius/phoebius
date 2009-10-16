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
 * PK = Unique + NotNull
 * @ingroup DB
 */
class DBPrimaryKeyConstraint extends DBUniqueConstraint
{
	/**
	 * @return DBPrimaryKeyConstraint
	 */
	static function create(array $columns = array())
	{
		return new self ($columns);
	}

	/**
	 * @return string
	 */
	protected function getHead(IDialect $dialect)
	{
		return 'PRIMARY KEY';
	}
}

?>