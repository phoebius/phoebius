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
final class TimestampPropertyType extends ObjectPropertyType
{
	function __construct($isNullable = false)
	{
		parent::__construct('Timestamp', null, $isNullable);
	}

	/**
	 * @return array
	 */
	function toRawValue($value)
	{
		return array (
			new ScalarSqlValue(
				$value->getStamp()
			)
		);
	}

	/**
	 * @return array
	 */
	function getDBFields()
	{
		return array (
			DBType::create(DBType::INTEGER)
				->setSize(11)
				->setUnsigned(true)
				->setIsNullable($this->isNullable())
		);
	}

	protected function getCtorArgumentsPhpCode()
	{
		return array(
			$this->isNullable()
				? 'true'
				: 'false'
		);
	}
}

?>