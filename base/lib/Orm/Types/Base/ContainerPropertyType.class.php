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
abstract class ContainerPropertyType extends OrmPropertyType
{
	/**
	 * @var IQueried
	 */
	private $container;

	/**
	 * @var IQueried
	 */
	private $encapsulant;

	function __construct(
			IQueried $container,
			IQueried $encapsulant
		)
	{
		$this->container = $container;
		$this->encapsulant = $encapsulant;
	}

	/**
	 * @return array
	 */
	function getDbColumns()
	{
		return array();
	}

	/**
	 * @return IQueried
	 */
	function getEncapsulant()
	{
		return $this->encapsulant;
	}

	/**
	 * @return IQueried
	 */
	function getContainer()
	{
		return $this->container;
	}

	/**
	 * @return mixed
	 */
	function makeValue(array $rawValue, FetchStrategy $fetchStrategy)
	{
		Assert::isUnreachable('%s cannot be used for transparent property', __METHOD__);
	}

	/**
	 * @return array
	 */
	function makeValueSet(array $rawValueSet, FetchStrategy $fetchStrategy)
	{
		Assert::isUnreachable('%s cannot be used for transparent property', __METHOD__);
	}

	/**
	 * @return array
	 */
	function makeRawValue($value)
	{
		Assert::isUnreachable('%s cannot be used for transparent property', __METHOD__);
	}

	/**
	 * @return boolean
	 */
	function isNullable()
	{
		return false;
	}
}

?>