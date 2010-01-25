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
 * Represents a box for numeric primitives
 *
 * @ingroup Core_Types
 */
abstract class Numeric extends Scalar
{
	static function cast($value)
	{
		return new self ($value);
	}

	function setValue($value)
	{
		if (is_string($value)) {
			$value = str_replace(',', '.', $value);
			$value = str_replace(' ', '', $value);
		}

		parent::setValue($value);

		return $this;
	}

	protected function isValidValue($value)
	{
		return
			   parent::isValidValue($value)
			&& is_numeric($value);
	}
}

?>