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

class DBValueArray extends ValueArray
{
	function append($value)
	{
		Assert::isTrue(
			is_scalar($value) || is_null($value),
			'wrong %s member type: string or null is expected, but %s provided',
			__CLASS__,
			TypeUtils::getName($value)
		);

		parent::append($value);

		return $this;
	}

	function prepend($value)
	{
		Assert::isTrue(
			is_scalar($value) || is_null($value),
			'wrong %s member type: string or null is expected, but %s provided',
			__CLASS__,
			TypeUtils::getName($value)
		);

		parent::prepend($value);

		return $this;
	}
}

?>