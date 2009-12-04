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
 * Represents a type that encapsulates primitive native type, that is stored in a single
 * database column
 *
 * @ingroup Orm_Types
 */
abstract class PrimitivePropertyType extends OrmPropertyType
{
	/**
	 * @var ISqlType
	 */
	private $type;

	/**
	 * @param ISqlType $type
	 */
	function __construct(ISqlType $type)
	{
		$this->type = $type;
	}

	function getImplClass()
	{
		return null;
	}

	function assemble(array $tuple, FetchStrategy $fetchStrategy)
	{
		Assert::isTrue(count($tuple) == 1);

		return reset($tuple);
	}

	function disassemble($value)
	{
		return array(new SqlValue($value));
	}

	function getSqlTypes()
	{
		return
			array(
				$this->type
			);
	}

	function getColumnCount()
	{
		return 1;
	}
}

?>