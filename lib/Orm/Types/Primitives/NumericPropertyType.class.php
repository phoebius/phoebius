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
class NumericPropertyType extends FloatPropertyType
{
	/**
	 * @var integer|null
	 */
	private $scale;

	function __construct($precision = null, $scale = null, $defaultValue = null, $isNullable = false)
	{
		if (!is_null($precision)) {
			Assert::isPositiveInteger($precision);

			if (!is_null($scale)) {
				Assert::isPositiveInteger($scale);
			}
		}

		if ($this->precision) {
			$this->scale = $scale;
		}

		parent::__construct($precision, $defaultValue, $isNullable);
	}

	/**
	 * @return string
	 */
	function getImplClass()
	{
		return 'Numeric';
	}

	/**
	 * @return array
	 */
	function getDBFields()
	{
		return array (
			DBType::create(DBType::NUMERIC)
				->setPrecision($this->getPrecision())
				->setScale($this->scale)
				->setIsNullable($this->isNullable())
		);
	}

	protected function getCtorArgumentsPhpCode()
	{
		return array(
			is_null($this->precision)
				? 'null'
				: $this->precision,
			is_null($this->scale)
				? 'null'
				: $this->scale,
			'null',
			$this->isNullable()
				? 'true'
				: 'false'
		);
	}
}

?>