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
class ObjectPropertyType extends PrimitivePropertyType
{
	/**
	 * @var string
	 */
	private $type;

	/**
	 * @param $type should implement {@link IBoxed}
	 */
	function __construct($type, $defaultValue = null, $isNullable = true)
	{
		if (!class_exists($type)) {
			throw new OrmModelIntegrityException("{$type} not found");
		}

		if (!in_array('IBoxed', class_implements($type))) {
			throw new OrmModelIntegrityException("{$type} should implement IBoxed");
		}

		$this->type = $type;

		parent::__construct(
			!$defaultValue
				? null
				: (
					$defaultValue instanceof $type
						? $defaultValue
						: call_user_func(array($this->type, 'cast'), $defaultValue)
				),
			$isNullable
		);
	}

	/**
	 * @return string
	 */
	function getImplClass()
	{
		return $this->type;
	}

	/**
	 * @return array
	 */
	function getDBFields()
	{
		return array (
			DBType::create(DBType::STRING)
				->setSize(255)
				->setIsNullable($this->isNullable())
		);
	}

	/**
	 * @return mixed
	 */
	function makeValue(array $rawValue, FetchStrategy $fetchStrategy)
	{
		return call_user_func_array(
			array($this->type, 'cast'),
			array(reset($rawValue))
		);
	}

	/**
	 * @return array
	 */
	function makeRawValue($value)
	{
		Assert::isTrue($value instanceof $this->type);

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
			new ScalarSqlValue(
				$value
					? $value->toScalar()
					: null
			)
		);
	}

	/**
	 * @return string
	 */
	function getType()
	{
		return $this->type;
	}

	protected function getCtorArgumentsPhpCode()
	{
		return array(
			'\'' . $this->type . '\'',
			'null',
			$this->isNullable
				? 'true'
				: 'false'
		);
	}
}

?>