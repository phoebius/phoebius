<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright (c) 2009 phoebius.org
 *
 * This program is free software; you can redistribute it and/or modify it under the terms
 * of the GNU Lesser General Public License as published by the Free Software Foundation;
 * either version 3 of the License, or (at your option) any later version.
 *
 * You should have received a copy of the GNU Lesser General Public License along with
 * this program; if not, see <http://www.gnu.org/licenses/>.
 *
 ************************************************************************************************/

/**
 * @ingroup Orm_Types
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