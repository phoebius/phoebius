<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright (c) 2009 Scand Ltd.
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
 * Represents a box for decimal primitives (float, integer, etc)
 *
 * @ingroup Core_Types
 */
abstract class Decimal extends Numeric
{
	static function cast($value)
	{
		return new self ($value);
	}

	protected function isValidValue($value)
	{
		if (strlen((string)$value) > 0 && $value{0} == '+') {
			$value = substr($value, 1);
			if (empty($value)) {
				$value = 0;
			}
		}

		return
			// http://www.php.net/manual/en/function.is-numeric.php#76094
			((string)(float)$value) === (preg_replace('/(\.0*)$/', '', (string)$value));
	}
}

?>