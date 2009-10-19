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
 * Represents a database handle pool
 * @ingroup Dal_DB
 */
final class DBPool extends Pool
{
	/**
	 * Database handle set as default
	 * @var DB
	 */
	private $defaultDBHandle;

	/**
	 * @var array of DB handles identified by names
	 */
	private $handles = array();

	/**
	 * Returns the instance of the singleton
	 * @return DBPool
	 */
	static function getInstance()
	{
		return LazySingleton::instance(__CLASS__);
	}

	/**
	 * Gets the database handle set as default
	 * @return DB
	 */
	static function getDefault()
	{
		return self::getInstance()->getDefaultHandle();
	}

	/**
	 * Gets the database handle identified by name
	 * @param string $name name to identify database handle
	 * @return DB
	 */
	static function get($name)
	{
		return self::getInstance()->getHandle($name);
	}

	/**
	 * Adds the database handle and identifies it by a name so it can be fetched later
	 * @param string $name name to identify database handle
	 * @param DB $db database handle itself
	 * @param boolean $isDefault specifies wheter to set this handle as default
	 * @return DBPool
	 */
	static function add($name, DB $db, $isDefault = false)
	{
		return self::getInstance()->addDB($name, $db, $isDefault);
	}

	/**
	 * Gets the database handle set as default
	 * @return DB
	 */
	function getDefaultHandle()
	{
		Assert::isTrue(
			$this->defaultDBHandle instanceof DB,
			'no items inside, so default item is not specified'
		);

		return $this->defaultDBHandle;
	}

	/**
	 * Adds the database handle and identifies it by a name so it can be fetched later
	 * @param string $name name to identify database handle
	 * @param DB $db database handle itself
	 * @param boolean $isDefault specifies wheter to set this handle as default
	 * @return DBPool
	 */
	function addDB($name, DB $db, $isDefault = false)
	{
		$this->handles[$name] = $db;

		if (!$this->defaultDBHandle || $isDefault) {
			$this->defaultDBHandle = $db;
		}

		return $this;
	}

	/**
	 * Gets the database handle identified by name
	 * @return DB
	 */
	function getHandle($name, $connectIfDisconnected = false)
	{
		if (!isset($this->handles[$name])) {
			throw new ArgumentException('name', 'db not found');
		}

		$db = $this->handles[$name];
		if ($connectIfDisconnected && !$db->isConnected()) {
			$db->connect();
		}

		return $db;
	}
}

?>