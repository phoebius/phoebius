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
class IntegerPropertyType extends PrimitivePropertyType
{
	/**
	 * @var integer|null
	 */
	private $size;

	function __construct($size = null, $defaultValue = null, $isNullable = false)
	{
		if (!is_null($size)) {
			Assert::isPositiveInteger($size);
		}

		$this->size = $size;

		parent::__construct($defaultValue, $isNullable);
	}

	/**
	 * @return string
	 */
	function getImplClass()
	{
		return 'Integer';
	}

	/**
	 * @return integer|null
	 */
	function getSize()
	{
		return $this->size;
	}

	/**
	 * @return array
	 */
	function getDbColumns()
	{
		return array (
			DBType::create(DBType::INTEGER)
				->setSize($this->size)
				->setIsNullable($this->isNullable())
		);
	}
}

?>