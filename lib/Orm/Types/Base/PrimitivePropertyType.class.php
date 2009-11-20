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
final class PrimitivePropertyType extends OrmPropertyType
{
	/**
	 * @var DBType
	 */
	private $type;

	function __construct(DBType $type)
	{
		$this->type = $type;
	}

	function getImplClass()
	{
		return null;
	}

	function assemble(DBValueArray $values, FetchStrategy $fetchStrategy)
	{
		Assert::isTrue($values->count() == 1);

		return $values->getFirst();
	}

	function disassemble($value)
	{
		if (is_null($value)) {
			if (!$this->isNullable()) {
				throw new OrmModelIntegrityException('property cannot be null');
			}
		}

		return new SqlValueArray(
			array(new ScalarSqlValue($value))
		);
	}

	function isNullable()
	{
		return $this->type->isNullable();
	}

	function getSqlTypes()
	{
		return new SqlTypeArray(
			array(
				$this->type
			)
		);
	}

	function getColumnCount()
	{
		return 1;
	}

	protected function getCtorArgumentsPhpCode()
	{
		return
			'new DBType('. join(', ', array(
				'DBType::' . $this->type->getId(),
				$this->isNullable() ? 'true' : 'false',
				$this->type->getSize(),
				$this->type->getPrecision(),
				$this->type->getScale(),
				$this->type->isGenerated() ? 'true' : 'false',
			)) . ')';
	}
}

?>