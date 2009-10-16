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
 * Represents an access mode for the {@link Transaction}
 * @ingroup Transaction
 */
final class AccessMode extends Enumeration implements ISqlCastable
{
	const READ_ONLY = 'read only';
	const READ_WRITE = 'read write';

	function toDialectString(IDialect $dialect)
	{
		return $this->getValue();
	}
}

?>