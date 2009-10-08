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
 * @ingroup PrimitiveOrmTypes
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
}

?>