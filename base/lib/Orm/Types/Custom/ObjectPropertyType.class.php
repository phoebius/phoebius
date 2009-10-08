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
 * @ingroup CustomOrmTypes
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
}

?>