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
 * @ingroup CoreTypes
 */
interface IIdentifierMappable extends IBoxed
{
	function toScalarId();
	function __toString();
}

?>