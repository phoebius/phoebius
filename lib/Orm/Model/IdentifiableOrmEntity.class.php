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
 * Represents an entity that is related to ORM and is stored in the database.
 * @ingroup Orm_Model
 */
abstract class IdentifiableOrmEntity extends OrmEntity implements IDaoRelated
{
	/**
	 * @var boolean
	 */
	private $fetched = false;

	/**
	 * @var boolean
	 */
	private $inFetchProcess = false;

	/**
	 * Gets the entity id, no matter how the identifier propery is named.
	 * @return mixed
	 */
	abstract function _getId();

	/**
	 * Ыets the entity id, no matter how the identifier propery is named.
	 * @return IdentifiableOrmEntity itself
	 */
	abstract function _setId($id);

	/**
	 * Assembles the entity properies (if they are not yet obtained from the storage) using
	 * the set ID.
	 * @throws OrmEntityNotFoundException if entity by the id is not presented in the DB
	 * @return IdentifiableOrmEntity
	 */
	final function fetch()
	{
		if (!$this->inFetchProcess && !$this->fetched && ($id = $this->_getId())) {

			$this->inFetchProcess = true;
			try {
				call_user_func(array(get_class($this), 'dao'))->getEntityById($id);
				$this->inFetchProcess = false;
			}
			catch (Exception $e) {
				$this->inFetchProcess = false;
				throw $e;
			}

			$this->fetched = true;
		}

		return $this;
	}

	final function setFetched()
	{
		$this->fetched = true;

		return $this;
	}

	/**
	 * Determines whether entity is fetched
	 * @return boolean
	 */
	final function isFetched()
	{
		return $this->fetched;
	}

	/**
	 * Completely drops the entity from the database
	 *
	 * @return void
	 */
	function drop()
	{
		if (($id = $this->_getId())) {
			$dao = call_user_func(array(get_class($this), 'dao'));
			$dao->dropById($id);
			$this->_setId(null);
		}
	}

	/**
	 * Saves the entity. If the ID is presented then DAO tries to update the existing one
	 *
	 * @return OrmEntity itself
	 */
	function save()
	{
		$dao = call_user_func(array(get_class($this), 'dao'));
		$dao->saveEntity($this);

		return $this;
	}

	function __clone()
	{
		$this->_setId(null);
		$this->fetched = false;
	}
}

?>