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
 * Aggregated by:
 *  - DBTable
 *  - DBConstraint
 *  - DBIndex (currently unimplemented)
 * @ingroup Dal_DB_Schema
 */
class DBColumn
{
	/**
	 * @var ISqlValueExpression
	 */
	private $defaultValue;

	/**
	 * @var string
	 */
	private $name;
	/**
	 * @var ISqlType
	 */
	private $type;

	function __construct($name, ISqlType $type)
	{
		Assert::isScalar($name);

		$this->name = $name;
		$this->type = $type;
	}

	/**
	 * @return string
	 */
	function getName()
	{
		return $this->name;
	}

	/**
	 * @return ISqlType
	 */
	function getType()
	{
		return $this->type;
	}

	/**
	 * @return ISqlValueExpression
	 */
	function getDefaultValue()
	{
		return $this->defaultValue;
	}

	/**
	 * @return ISqlValueExpression
	 */
	function setDefaultValue(ISqlValueExpression $defaultValue = null)
	{
		$this->defaultValue = $defaultValue;

		return $this;
	}
}

?>