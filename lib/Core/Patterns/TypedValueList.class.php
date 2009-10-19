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
 * @ingroup Core_Patterns
 */
abstract class TypedValueList extends ValueList
{
	/**
	 * Determines whether the specified value is of valid type supported by the list implementation
	 * @return boolean
	 */
	abstract protected function isValueOfValidType($value);

	/**
	 * @return ValueList
	 */
	function append($value)
	{
		if (!$this->isValueOfValidType($value)) {
			throw new ArgumentException('value', 'not of expected type');
		}

		return parent::append($value);
	}

	/**
	 * @return ValueList
	 */
	function prepend($value)
	{
		if (!$this->isValueOfValidType($value)) {
			throw new ArgumentException('value', 'not of expected type');
		}

		return parent::prepend($value);
	}
}

?>