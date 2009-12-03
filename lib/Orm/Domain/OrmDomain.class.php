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
 * Represents an auxiliary container of ORM entities' representation
 *
 * @aux
 * @ingroup Orm_Domain
 */
final class OrmDomain
{
	/**
	 * @var string
	 */
	private $dbSchema;

	/**
	 * @var array of OrmClass
	 */
	private $classes = array();

	/**
	 * Gets the name of the database schema presented in DBPool to be used when generating SQL, if set
	 *
	 * @return string|null
	 */
	function getDbSchema()
	{
		return $this->dbSchema;
	}

	/**
	 * Sets the name of the database schema presented in DBPool to be used when generating SQL
	 *
	 * @param stirng $dbSchema
	 *
	 * @return OrmDomain
	 */
	function setDbSchema($dbSchema = null)
	{
		Assert::isScalarOrNull($dbSchema);

		$this->dbSchema = $dbSchema;

		return $this;
	}

	/**
	 * Gets the OrmClass objects defined within the domain
	 *
	 * @return array of OrmClass
	 */
	function getClasses()
	{
		return $this->classes;
	}

	/**
	 * Adds the new OrmClass object to the domain
	 *
	 * @throws OrmModelIntegrityException if the сдфыы with the same name already added
	 *
	 * @return OrmDomain
	 */
	function addClass(OrmClass $class)
	{
		$name = $class->getName();

		if (isset($this->classes[$name])) {
			throw new OrmModelIntegrityException("Class {$class->getName()} already defined");
		}

		$this->classes[$name] = $class;

		return $this;
	}

	/**
	 * Gets the OrmClass object identified by name
	 *
	 * @throws OrmModelIntegrityException if no class identified by found
	 *
	 * @return OrmClass
	 */
	function getClass($name)
	{
		if (!isset($this->classes[$name])) {
			throw new OrmModelIntegrityException("Class {$name} is not defined");
		}

		return $this->classes[$name];
	}

	/**
	 * Determines whether the OrmClass object with the specified name is defined within the OrmDomain
	 *
	 * @return boolean
	 */
	function classExists($name)
	{
		return isset($this->classes[$name]);
	}
}

?>