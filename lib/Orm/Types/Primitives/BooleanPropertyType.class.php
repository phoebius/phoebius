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
 * @ingroup Orm_Types
 */
class BooleanPropertyType extends PrimitivePropertyType
{
	private $trueIdentifiers = array('1', 't', 'true', 1);

	/**
	 * @return mixed
	 */
	function makeValue(array $rawValue, FetchStrategy $fetchStrategy)
	{
		$stringableValue = reset($rawValue);
		return in_array($stringableValue, $this->trueIdentifiers, true);
	}

	/**
	 * @return string
	 */
	function getImplClass()
	{
		return 'Boolean';
	}

	/**
	 * @return array
	 */
	function getDBFields()
	{
		return array (
			DBType::create(DBType::BOOLEAN)
				->setIsNullable($this->isNullable())
		);
	}
}

?>