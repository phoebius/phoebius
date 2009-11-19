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
	 * @var string
	 */
	private $type;

	/**
	 * @var mixed
	 */
	private $value;

	function __construct($type, $value, $message = 'type cast failed')
	{
		$this->type =
			is_object($type)
				? get_class($type)
				: $type;
		$this->value = $value;

		parent::__construct('value', $this->type, $message);
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