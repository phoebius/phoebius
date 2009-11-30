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
class BoxablePropertyType extends PrimitivePropertyType
{
	/**
	 * @var DBType
	 */
	private $dbType;

	/**
	 * @var string
	 */
	private $boxableType;

	function __construct($boxableType, DBType $dbType)
	{
		Assert::isTrue(
			TypeUtils::isChild($boxableType, 'IBoxable')
		);

		$this->boxableType = $boxableType;
		$this->dbType = $dbType;

		parent::__construct($dbType, $dbType->isNullable());
	}

	function getImplClass()
	{
		return $this->boxableType;
	}

	function assemble(array $tuple, FetchStrategy $fetchStrategy)
	{
		Assert::isTrue(sizeof($tuple) == 1);

		$value = reset($tuple);

		if (is_null($value) && $this->isNullable()) {
			return null;
		}
		else {
			try {
				return call_user_func_array(
					array($this->boxableType, 'cast'),
					array($value)
				);
			}
			catch (TypeCastException $e) {
				Assert::isUnreachable('wrong `%s` cast to %s', $value, $this->boxableType);
			}
		}
	}

	function disassemble($value)
	{
		return new SqlValueExpressionArray(
			array(new SqlValue(
				is_null($value)
					? $value
					: $value->getValue()
			))
		);
	}

	protected function getCtorArgumentsPhpCode()
	{
		return array(
			'\'' . $this->boxableType . '\'',
			$this->dbType->toPhpCodeCall()
		);
	}
}

?>