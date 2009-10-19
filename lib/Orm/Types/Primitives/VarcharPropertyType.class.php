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
class VarcharPropertyType extends PrimitivePropertyType
{
	/**
	 * @var integer|null
	 */
	private $length;

	function __construct($length = null, $defaultValue = null, $isNullable = false)
	{
		if (!is_null($length)) {
			Assert::isPositiveInteger($length);
		}

		$this->length = $length;

		parent::__construct($defaultValue, $isNullable);
	}

	/**
	 * @return string
	 */
	function getImplClass()
	{
		return 'String';
	}

	/**
	 * @return array
	 */
	function getDBFields()
	{
		return array (
			DBType::create(DBType::STRING)
				->setSize($this->length)
				->setIsNullable($this->isNullable())
		);
	}

	protected function getCtorArgumentsPhpCode()
	{
		return array(
			is_null($this->length)
				? 'null'
				: $this->length,
			'null',
			$this->isNullable()
				? 'true'
				: 'false'
		);
	}
}

?>