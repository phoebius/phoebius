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
	 * Readonly
	 *
	 * @var SqlTypeArray
	 */
	private static $sqlTypes;

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

		self::$sqlTypes = new SqlTypeArray;
	}

	final function getSqlTypes()
	{
		return self::$sqlTypes;
	}

	final function getColumnCount()
	{
		return 0;
	}

	final function getImplClass()
	{
		return null;
	}

	final function assemble(DBValueArray $values, FetchStrategy $fetchStrategy)
	{
		Assert::isUnreachable('%s cannot be used for transparent property', __METHOD__);
	}

	final function assebmleSet(array $valueSet, FetchStrategy $fetchStrategy)
	{
		Assert::isUnreachable('%s cannot be used for transparent property', __METHOD__);
	}

	final function disassemble($value)
	{
		Assert::isUnreachable('%s cannot be used for transparent property', __METHOD__);
	}

	final function isNullable()
	{
		return false;
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
}

?>