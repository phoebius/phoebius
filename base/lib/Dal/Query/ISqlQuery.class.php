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
 * Represents an object that can be casted to a plain string SQL query
 * @ingroup Query
 */
interface ISqlQuery extends ISqlCastable
{
	/**
	 * @return array
	 */
	function getCastedParameters(IDialect $dialect);
}

?>