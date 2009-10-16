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
 * Represents an isolation level of the {@link Transaction}
 * @ingroup Transaction
 */
final class IsolationLevel extends Enumeration implements ISqlCastable
{
	const READ_COMMITTED = 'read commited';
	const READ_UNCOMMITTED = 'read uncommitted';
	const REPEATABLE_READ = 'repeatable read';
	const SERIALIZABLE = 'serializable';

	function toDialectString(IDialect $dialect)
	{
		return $this->getValue();
	}
}

?>