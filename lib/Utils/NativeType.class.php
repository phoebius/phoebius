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
 * @ingroup Utils
 */
final class NativeType extends Enumeration
{
	const TYPE_STRING = 'string';
	const TYPE_INTEGER = 'integer';
	const TYPE_FLOAT = 'float';
	const TYPE_SCALAR = 'scalar';
	const TYPE_NUMERIC = 'numeric';
	const TYPE_ARRAY = 'array';

	/**
	 * @return array
	 */
	static function getSupportedTypes()
	{
		$me = new self (self::TYPE_ARRAY);
		return $me->getMembers();
	}

	/**
	 * @return boolean
	 */
	function isMatch($variable)
	{
		switch ($this->getValue()) {
			case self::TYPE_SCALAR:
			case self::TYPE_STRING:
				{
					return is_scalar($variable);
				}

			case self::TYPE_INTEGER:
			case self::TYPE_NUMERIC:
				{
					return TypeUtils::isInteger($variable);
				}

			case self::TYPE_FLOAT:
				{
					return is_numeric($variable) && ($variable == (float)$variable);
				}

			case self::TYPE_ARRAY:
				{
					return is_array($variable);
				}
		}
	}
}

?>