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
 * @ingroup Orm_Types_Id
 */
final class IDGeneratorType extends Enumeration
{
	const PRE = 1;
	const POST = 2;
	const BOTH = 3;

	function isPre()
	{
		return in_array($this->getValue(), array(self::PRE, self::BOTH));
	}

	function isPost()
	{
		return in_array($this->getValue(), array(self::POST, self::BOTH));
	}
}

?>