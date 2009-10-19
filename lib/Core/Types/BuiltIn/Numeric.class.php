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
 * @ingroup Core_Types_BuiltIn
 */
abstract class Numeric extends Scalar
{
	/**
	 * @return Numeric
	 */
	static function create($value)
	{
		return new self ($value);
	}

	/**
	 * @return Numeric
	 */
	static function cast($value)
	{
		return new self ($value);
	}

	/**
	 * @return BuiltInType
	 */
	function setValue($value)
	{
		if (is_scalar($value)) {
			$value = str_replace(',', '.', $value);
		}

		return parent::setValue($value);
	}

	/**
	 * @return boolean
	 */
	protected function isValidValue($value)
	{
		return
			   parent::isValidValue($value)
			&& is_numeric($value);
	}
}

?>