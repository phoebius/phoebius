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
final class DatePropertyType extends ObjectPropertyType
{
	function __construct($isNullable = false)
	{
		parent::__construct('Date', null, $isNullable);
	}

	/**
	 * @param Date $logicalValue
	 * @return SqlValueList
	 */
	function makeRawValue($logicalValue)
	{
		return array (
			new ScalarSqlValue(
				$logicalValue->toFormattedString('Y/m/d')
			)
		);
	}

	/**
	 * @return array
	 */
	function getDBFields()
	{
		return array (
			DBType::create(DBType::DATE)
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