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
abstract class PrimitivePropertyType extends OrmPropertyType
{
	/**
	 * @var boolean
	 */
	private $isNullable;

	/**
	 * @var mixed|null
	 */
	private $defaultValue;

	function __construct($defaultValue = null, $isNullable = false)
	{
		Assert::isBoolean($isNullable);

		$this->isNullable = $isNullable;
		$this->defaultValue = $defaultValue;
	}

	/**
	 * @return mixed
	 */
	function makeValue(array $rawValue, FetchStrategy $fetchStrategy)
	{
		return reset($rawValue);
	}

	/**
	 * @return array
	 */
	function makeRawValue($value)
	{
		if (is_null($value)) {
			if ($this->hasDefaultValue()) {
				$value = $this->getDefaultValue();
			}
			else {
				if (!$this->isNullable()) {
					throw new OrmModelIntegrityException('property cannot be null');
				}
			}
		}

		return array(
			new ScalarSqlValue($value)
		);
	}

	/**
	 * @return array
	 */
	function makeValueSet(array $rawValueSet, FetchStrategy $fetchStrategy)
	{
		$values = array();

		foreach ($rawValueSet as $rawValue) {
			$values[] = $this->makeValue($rawValue, $fetchStrategy);
		}

		return $values;
	}

	/**
	 * @return mixed
	 */
	function getDefaultValue()
	{
		return $this->defaultValue;
	}

	/**
	 * @return boolean
	 */
	function hasDefaultValue()
	{
		return !is_null($this->defaultValue);
	}

	/**
	 * @return boolean
	 */
	function isNullable()
	{
		return $this->isNullable;
	}

	protected function getCtorArgumentsPhpCode()
	{
		return array(
			'null',
			$this->isNullable
				? 'true'
				: 'false'
		);
	}
}

?>