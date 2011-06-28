<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright (c) 2009 Scand Ltd.
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
 * Represents a property type that is implemented by IBoxable class and can be stored in a single
 * database column
 *
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

	/**
	 * @param string $boxableType name of an IBoxable class
	 * @param DBType $dbType type to use to store the value
	 */
	function __construct($boxableType, DBType $dbType)
	{
		Assert::isTrue(
			TypeUtils::isInherits($boxableType, 'IBoxable')
		);

		$this->boxableType = $boxableType;
		$this->dbType = $dbType;

		parent::__construct($dbType);
	}

	function getImplClass()
	{
		return $this->boxableType;
	}

	function assemble(array $tuple, FetchStrategy $fetchStrategy)
	{
		Assert::isTrue(sizeof($tuple) == 1);

		$value = reset($tuple);

		if (is_null($value)) {
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
		return
			array(
				new SqlValue(
					is_null($value)
						? $value
						: $value->getValue()
				)
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