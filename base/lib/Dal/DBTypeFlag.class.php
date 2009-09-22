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
 * TODO: add HAS_PARENT_TYPE for composite types (arrays)
 * @ingroup Dal
 */
final class DBTypeFlag extends StaticClass
{
	const HAS_SIZE		= 0x000100;
	const HAS_PRECISION	= 0x000200;
	const HAS_SCALE		= 0x000400;
	const HAS_TIMEZONE	= 0x000800;

	const CAN_BE_UNSIGNED	= 0x010000;
	const CAN_BE_GENERATED	= 0x020000;
}

?>