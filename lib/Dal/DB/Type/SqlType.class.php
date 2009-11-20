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

class PrimitiveSqlType implements ISqlType
{
	/**
	 * @var DBType
	 */
	private $dbType;

	/**
	 * @var ISqlValueExpression|null
	 */
	private $defaultValue;

	function __construct(DBType $dbType, ISqlValueExpression $defaultValue = null)
	{
		$this->dbType = $dbType;
		$this->defaultValue = $defaultValue;
	}

	/**
	 * @return ISqlValueExpression|null
	 */
	function getDefaultValue()
	{
		return $this->defaultValue;
	}

	/**
	 * @return PrimitiveSqlType
	 */
	function setDefaultValue(ISqlValueExpression $defaultValue = null)
	{
		$this->defaultValue = $defaultValue;

		return $this;
	}

	function toDialectString(IDialect $dialect)
	{
		return $dialect->getTypeRepresenation($this->dbType);
	}
}

?>