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
class FloatPropertyType extends PrimitivePropertyType
{
	/**
	 * @var integer|null
	 */
	private $precision;

	function __construct($precision = null, $defaultValue = null, $isNullable = false)
	{
		if (!is_null($precision)) {
			Assert::isPositiveInteger($precision);
		}

		$this->precision = $precision;

		parent::__construct($defaultValue, $isNullable);
	}

	/**
	 * @return string
	 */
	function getImplClass()
	{
		return 'Float';
	}

	/**
	 * @return integer|null
	 */
	function getPrecision()
	{
		return $this->precision;
	}

	/**
	 * @return array
	 */
	function getDBFields()
	{
		return array (
			DBType::create(DBType::FLOAT)
				->setPrecision($this->precision)
				->setIsNullable($this->isNullable())
		);
	}

	protected function getCtorArgumentsPhpCode()
	{
		return array(
			is_null($this->precision)
				? 'null'
				: $this->precision,
			'null',
			$this->isNullable()
				? 'true'
				: 'false'
		);
	}
}

?>