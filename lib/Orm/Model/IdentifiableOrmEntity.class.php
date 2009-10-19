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
 * Represents an ORM entity
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
	 * @return mixed
	 */
	abstract function _getId();

	/**
	 * @return IdentifiableOrmEntity
	 */
	abstract function _setId($id);

	/**
	 * Lazy fetching stub
	 * @throws OrmEntityNotFoundException
	 * @return IdentifiableOrmEntity
	 */
	final function fetch()
	{
		if (!$this->inFetchProcess && !$this->fetched && ($id = $this->_getId())) {
			$this->inFetchProcess = true;
			try {
				call_user_func(array($this, 'dao'))->getById($id);
			}
			catch (Exception $e) {
				$this->inFetchProcess = false;
				throw $e;
			}
			$this->inFetchProcess = false;
			$this->fetched = true;
		}

		return $this;
	}

	/**
	 * @return void
	 */
	function __clone()
	{
		$this->_setId(null);
		$this->fetched = false;
	}
}

?>