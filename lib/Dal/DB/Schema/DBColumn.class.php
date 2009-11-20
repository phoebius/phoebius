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
	 * @var string
	 */
	private $name;
	/**
	 * @var ISqlType
	 */
	private $type;

	function __construct($name, ISqlType $type)
	{
		$this->setName($name);
		$this->setType($type);
	}

	/**
	 * @return string
	 */
	function getName()
	{
		return $this->name;
	}

	/**
	 * @return DBColumn
	 */
	function setName($name)
	{
		Assert::isScalar($name);

		$this->name = $name;

		return $this;
	}

	/**
	 * @return ISqlType
	 */
	function getType()
	{
		return $this->type;
	}

	/**
	 * @return DBColumn
	 */
	function setType(ISqlType $type)
	{
		$this->type = $type;

		return $this;
	}
}

?>