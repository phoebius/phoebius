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
 * Type-safe collection
 *
 * @ingroup Core_Patterns
 */
abstract class TypedValueList extends ValueList
{
	private $type;

	/**
	 * @param $type name of a type
	 * @param $array initial values to be imported to the value list
	 */
	function __construct($type, array $array = array())
	{
		$this->type =
			is_object($type)
				? get_class($type)
				: $type;

		parent::__construct($array);
	}

	function appendValue($value)
	{
		Assert::isTrue(
			($value instanceof $this->type),
			'wrong type passed to %s, expected %s but %s found',
			get_class($this),
			$this->type,
			gettype($value)
		);

		parent::appendValue($value);

		return $this;
	}

	function prependValue($value)
	{
		Assert::isTrue(
			($value instanceof $this->type),
			'wrong type passed to %s, expected %s but %s found',
			get_class($this),
			$this->type,
			gettype($value)
		);

		parent::prependValue($value);

		return $this;
	}
}

?>