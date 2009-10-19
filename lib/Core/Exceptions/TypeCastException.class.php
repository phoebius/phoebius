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
 * Cast failure
 * @ingroup Core_Exceptions
 */
class TypeCastException extends ArgumentTypeException
{
	/**
	 * @var Type
	 */
	private $type;

	/**
	 * @var mixed
	 */
	private $value;

	function __construct(Type $failedType, $value, $message = 'type cast failed')
	{
		Assert::isTrue($failedType->isDescendantOf(new Type('IObjectMappable')));

		parent::__construct('value', $failedType->getName(), $message);

		$this->type = $failedType;
		$this->value = $value;
	}

	/**
	 * @return Type
	 */
	function getFailedType()
	{
		return $this->type;
	}

	/**
	 * @return mixed
	 */
	function getValue()
	{
		return $this->value;
	}
}

?>