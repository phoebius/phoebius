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
class BooleanPropertyType extends PrimitivePropertyType
{
	private $trueIdentifiers = array('1', 't', 'true', 1);

	/**
	 * @return mixed
	 */
	function makeValue(array $rawValue, FetchStrategy $fetchStrategy)
	{
		$stringableValue = reset($rawValue);
		return in_array($stringableValue, $this->trueIdentifiers, true);
	}

	/**
	 * @return string
	 */
	function getImplClass()
	{
		return 'Boolean';
	}

	/**
	 * @return array
	 */
	function getDBFields()
	{
		return array (
			DBType::create(DBType::BOOLEAN)
				->setIsNullable($this->isNullable())
		);
	}
}

?>