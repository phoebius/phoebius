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
 * Represents a database pool
 * @ingroup Dal_DB
 */
final class DBPool extends Pool
{
	/**
	 * Default database
	 * @var DB
	 */
	private $default;

	/**
	 * @var array of named DB
	 */
	private $handles = array();

	/**
	 * Returns the instance of DBPool
	 * @return DBPool
	 */
	static function getInstance()
	{
		return LazySingleton::instance(__CLASS__);
	}

	/**
	 * Adds the database identified by name
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
	 * Gets the database identified by name
	 * @param string $name name to identify database handle
	 * @return DB
	 */
	static function get($name, $connectIfDisconnected = false)
	{
		return self::getInstance()->getDB($name, $connectIfDisconnected);
	}

	/**
	 * Gets the default database
	 * @param string $name name to identify database handle
	 * @return DB
	 */
	static function getDefault()
	{
		return self::getInstance()->getDefaultDB();
	}

	/**
	 * Gets the default database
	 * @return DB
	 */
	function getDefaultDB()
	{
		Assert::isTrue(
			$this->default instanceof DB,
			'no items inside, so default item is not specified'
		);

		return $this->default;
	}

	/**
	 * Adds the database identified by name
	 * @param string $name name to identify database handle
	 * @param DB $db database handle itself
	 * @param boolean $isDefault specifies wheter to set this handle as default
	 * @return DBPool
	 */
	function addDB($name, DB $db, $isDefault = false)
	{
		$this->handles[$name] = $db;

		if (!$this->default || $isDefault) {
			$this->default = $db;
		}

		return $this;
	}

	/**
	 * Gets the database identified by name
	 * @return DB
	 */
	function getDB($name, $connectIfDisconnected = false)
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