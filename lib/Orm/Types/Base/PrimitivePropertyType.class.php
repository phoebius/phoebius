<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright notice
 *
 ************************************************************************************************/

/**
 * @ingroup BaseOrmTypes
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