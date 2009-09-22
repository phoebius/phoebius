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
 * Represents a sql join method
 * @ingroup SelectQueryHelpers
 * @internal
 */
final class SqlJoinMethod extends Enumeration implements ISqlCastable
{
	const LEFT = 'LEFT';
	const LEFT_OUTER = 'LEFT OUTER';
	const RIGHT = 'RIGHT';
	const RIGHT_OUTER = 'RIGHT OUTER';
	const INNER = 'INNER';
	const CROSS = 'CROSS';

	/**
	 * Casts an object to the SQL dialect string
	 * @return string
	 */
	function toDialectString(IDialect $dialect)
	{
		return $this->getValue() . ' JOIN';
	}
}

?>