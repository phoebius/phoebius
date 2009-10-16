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
final class TimestampPropertyType extends ObjectPropertyType
{
	function __construct($isNullable = false)
	{
		parent::__construct('Timestamp', null, $isNullable);
	}

	/**
	 * @return array
	 */
	function toRawValue($value)
	{
		return array (
			new ScalarSqlValue(
				$value->getStamp()
			)
		);
	}

	/**
	 * @return array
	 */
	function getDBFields()
	{
		return array (
			DBType::create(DBType::INTEGER)
				->setSize(11)
				->setUnsigned(true)
				->setIsNullable($this->isNullable())
		);
	}

	protected function getCtorArgumentsPhpCode()
	{
		return array(
			$this->isNullable()
				? 'true'
				: 'false'
		);
	}
}

?>