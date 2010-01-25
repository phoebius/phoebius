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
 * Represents a fundamental type abstract box
 *
 * @ingroup Core_Types
 */
abstract class Primitive implements IBoxable, IOrmPropertyAssignable
{
	/**
	 * @var scalar
	 */
	private $value;

	/**
	 * @throws TypeCastException
	 * @param scalar $value value to be wrapped
	 */
	function __construct($value)
	{
		$this->setValue($value);
	}

	/**
	 * Sets the internal value
	 *
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

	function getValue()
	{
		return $this->value;
	}

	function __toString()
	{
		return (string) $this->value;
	}

	/**
	 * Checks whether the primitive value is suitable for wrapping with this class
	 *
	 * @return boolean
	 */
	protected function isValidValue($value)
	{
		return is_scalar($value);
	}
}

?>