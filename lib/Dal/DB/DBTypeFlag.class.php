<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright (c) 2009 phoebius.org
 *
 * This program is free software; you can redistribute it and/or modify it under the terms
 * of the GNU Lesser General Public License as published by the Free Software Foundation;
 * either version 3 of the License, or (at your option) any later version.
 *
 * You should have received a copy of the GNU Lesser General Public License along with
 * this program; if not, see <http://www.gnu.org/licenses/>.
 *
 ************************************************************************************************/

/**
 * TODO: add HAS_PARENT_TYPE for composite types (arrays)
 * @ingroup Dal_DB
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