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
	 * @var IQueryable
	 */
	private $container;

	/**
	 * @var IQueryable
	 */
	private $encapsulant;

	function __construct(
			IQueryable $container,
			IQueryable $encapsulant
		)
	{
		$this->container = $container;
		$this->encapsulant = $encapsulant;
	}

	/**
	 * @return array
	 */
	function getDBFields()
	{
		return array();
	}

	/**
	 * @return IQueryable
	 */
	function getEncapsulant()
	{
		return $this->encapsulant;
	}

	/**
	 * @return IQueryable
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