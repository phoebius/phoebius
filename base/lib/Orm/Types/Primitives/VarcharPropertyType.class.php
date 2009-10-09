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