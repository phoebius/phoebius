<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright notice
 *
 ************************************************************************************************/

/**
 * Represents an ORM entity
 * @ingroup OrmModel
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