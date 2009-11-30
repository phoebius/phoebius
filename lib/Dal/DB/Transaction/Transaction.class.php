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
 * Reprsents a transaction run in the database
 * @ingroup Dal_DB_Transaction
 */
class Transaction
{
	/**
	 * @var array
	 */
	private $savepoints = array();

	/**
	 * @var AccessMode
	 */
	private $mode;

	/**
	 * @var IsolationLevel
	 */
	private $isolationLevel;

	/**
	 * @var DB
	 */
	protected $db;

	/**
	 * @var boolean
	 */
	protected $isStarted = false;

	/**
	 * @var boolean
	 */
	protected $isCommited = false;

	/**
	 * @var boolean
	 */
	protected $isRolledBack = false;

	function __construct(DB $db)
	{
		$this->db = $db;
	}

	/**
	 * Gets the database handle which hols the transaction itself
	 * @return DB
	 */
	function getDB()
	{
		return $this->db;
	}

	/**
	 * Begins a transaction
	 * @return void
	 */
	function begin()
	{
		$query = array();

		$query[] = 'START TRANSACTION';

		if ($this->isolationLevel) {
			$query[] = ' '.$this->isolationLevel->toDialectString($this->db->getDialect());
		}

		if ($this->mode) {
			$query[] = ' '.$this->mode->toDialectString($this->db->getDialect());
		}

		$this->db->sendQuery(new RawSqlQuery(join(' ', $query)));

		$this->isStarted = true;
		$this->isCommited = false;
		$this->isRolledBack = false;
	}

	/**
	 * Commits a transaction
	 * @return void
	 */
	function commit()
	{
		Assert::isTrue(
			$this->isStarted,
			'transaction is not yet started'
		);

		$this->isStarted = false;
		$this->isCommited = true;
		$this->isRolledBack = false;

		$this->db->sendQuery(new RawSqlQuery('COMMIT'));
	}

	/**
	 * Rolls back a transaction to the beginning or to the specified savepoint
	 * @param string $savepoint the name of the savepoint
	 * @return void
	 */
	function rollback($savepoint = null)
	{
		Assert::isTrue(
			$this->isStarted,
			'transaction is not yet started'
		);

		$query = array();

		$query[] = 'ROLLBACK';

		if ($savepoint) {
			Assert::isTrue(
				in_array($savepoint, $this->savepoints),
				'unknown savepoint',
				$savepoint
			);

			$query[] = ' TO SAVEPOINT ' . $this->db->quoteIdentifier($savepoint);
		}

		$this->isStarted = false;
		$this->isCommited = false;
		$this->isRolledBack = true;

		$this->db->sendQuery(new RawSqlQuery(join(' ', $query)));
	}

	/**
	 * Creates a new save point inside the transaction so that it could be rolled back to this
	 * step
	 * @param string $savepointthe name of the savepoint
	 * @return Transaction an object itself
	 */
	function save($savepointId)
	{
		$this->savepoints[] = $savepointId;
		$query = 'SAVEPOINT ' . $this->db->quoteIdentifier($savepointId);
		$this->db->sendQuery(new RawSqlQuery($query));

		return $this;
	}

	/**
	 * Determines whether the transaction is already begun
	 * @return boolean
	 */
	function isStarted()
	{
		return $this->isStarted;
	}

	/**
	 * Determines whether the transaction is already commited
	 * @return boolean
	 */
	function isCommited()
	{
		return $this->isCommited;
	}

	/**
	 * Determines whether the transaction is rolled back
	 * @return boolean
	 */
	function isRolledBack()
	{
		return $this->isRolledBack;
	}

	/**
	 * Sets a custom access mode of the transaction
	 * @return Transaction an object itself
	 */
	function setAccessMode(AccessMode $mode)
	{
		$this->mode = $mode;

		return $this;
	}

	/**
	 * Sets a custom isolation level of the transaction
	 * @return Transaction an object itself
	 */
	function setIsolationLevel(IsolationLevel $level)
	{
		$this->isolationLevel = $level;

		return $this;
	}

	/**
	 * The destructor, that checks if the transaction is started. Opened transactions MUST be
	 * avoided, auto-commiting is the great evil
	 */
	function __destruct()
	{
		if ($this->isStarted()) {
			$this->rollback();
		}
	}
}

?>