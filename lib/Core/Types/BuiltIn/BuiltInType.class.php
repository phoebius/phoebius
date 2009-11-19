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
abstract class BuiltInType implements IObjectCastable, IOrmPropertyAssignable, IStringCastable
{
	/**
	 * @var scalar
	 */
	private $value;

	/**
	 * @param scalar $value
	 */
	function __construct($value)
	{
		$this->setValue($value);
	}

	/**
	 * @return BuiltInType
	 */
	function setValue($value)
	{
		if ($this->isValidValue($value)) {
			$this->value = $value;
		}
		else {
			throw new TypeCastException($this, $value);
		}

		return $this;
	}

	/**
	 * @return scalar
	 */
	function getValue()
	{
		return $this->value;
	}

	/**
	 * @return string
	 */
	function __toString()
	{
		return  (string) $this->value;
	}

	/**
	 * @return boolean
	 */
	protected function isValidValue($value)
	{
		return is_scalar($value);
	}
}

?>