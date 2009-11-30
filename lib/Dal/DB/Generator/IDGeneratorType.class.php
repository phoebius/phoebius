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
 * Represents an IIDGenerator type. A generator type defines the logic the generator should
 * be called for new primary key retrieval:
 * - "PRE" type defines a generator that should be invoked before actual insert
 * - "POST" type defines a generator that should be invoked after an insert
 * - "BOTH" type requires generator to be called in both cases
 *
 * @ingroup Dal_DB_Generator
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