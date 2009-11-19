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
 * Simple database abstraction layer.
 *
 * @ingroup Dal_DB
 */
abstract class DB
{
	/**
	 * @var string|null
	 */
	private $user;

	/**
	 * @var string|null
	 */
	private $password;

	/**
	 * @var string|null
	 */
	private $host;

	/**
	 * @var string|null
	 */
	private $dbname;

	/**
	 * @var DBDriver
	 */
	private $driver;

	/**
	 * @var string|null
	 */
	private $encoding;

	/**
	 * @var boolean
	 */
	private $isPersistent = false;

	/**
	 * @var integer|null
	 */
	private $port;

	/**
	 * @var Transaction|null
	 */
	private $transaction;

	/**
	 * Gets the port of the database, or null if not set
	 * @return integer|null
	 */
	function getPort()
	{
		return $this->port;
	}

	/**
	 * Sets the port on which the database resides
	 * @param integer $port
	 * @return DB itself
	 */
	function setPort($port)
	{
		Assert::isNumeric($port);

		$this->port = $port;

		return $this;
	}

	/**
	 * Returns the encoding of the database, or null if not yet set
	 * @return string|null
	 */
	function getEncoding()
	{
		return $this->encoding;
	}

	/**
	 * Overridden. Sets the encoding of the database
	 * @param string $encoding
	 * @return DB itself
	 */
	function setEncoding($encoding)
	{
		Assert::isScalar($encoding);

		$this->encoding = $encoding;

		return $this;
	}

	/**
	 * Determines whether the connection to the database is persistent or not
	 * @return boolean
	 */
	function isPersistent()
	{
		return $this->isPersistent;
	}

	/**
	 * Makes a connection to the database to be persistent
	 * @return DB itself
	 */
	function makePersistent()
	{
		Assert::isFalse(
			$this->isConnected(),
			'already connected - cannot switch to persistent conn'
		);

		$this->isPersistent = true;

		return $this;
	}

	/**
	 * Makes a connection to the database to be NOT persistent
	 * @return DB itself
	 */
	function makeNotPersistent()
	{
		Assert::isFalse(
			$this->isConnected(),
			'already connected - cannot switch to non-persistent conn'
		);

		$this->isPersistent = false;

		return $this;
	}

	/**
	 * Gets the host where the database resides
	 * @return string
	 */
	function getHost()
	{
		return $this->host;
	}

	/**
	 * Sets the host where the database resides
	 * @param string $host
	 * @return DB itself
	 */
	function setHost($host)
	{
		Assert::isScalar($host);

		$this->host = $host;

		return $this;
	}

	/**
	 * Gets the name of the database
	 * @return string
	 */
	function getDBName()
	{
		return $this->dbname;
	}

	/**
	 * Sets the name of the database
	 * @param string $name
	 * @return DB itself
	 */
	function setDBName($name)
	{
		Assert::isScalar($name);

		$this->dbname = $name;

		return $this;
	}

	/**
	 * Gets the password  required when authorizing to the database
	 * @return string
	 */
	function getPassword()
	{
		return $this->password;
	}

	/**
	 * Sets the password required when authorizing to the database
	 * @return DB itself
	 */
	function setPassword($password)
	{
		$this->password = $password;

		return $this;
	}

	/**
	 * Gets the username required when authorizing to the database
	 * @return string
	 */
	function getUser()
	{
		return $this->user;
	}

	/**
	 * Sets the username required when authorizing to the database
	 * @param string $user
	 * @return DB
	 */
	function setUser($user)
	{
		Assert::isScalar($user);

		$this->user = $user;

		return $this;
	}

	/**
	 * Acquires a transaction and returns it
	 * @return Transaction
	 */
	function getTransaction()
	{
		Assert::isTrue(
			   (is_null($this->transaction))
			|| (
					   ($this->transaction instanceof Transaction)
					&& (!$this->transaction->isStarted())
			),
			'already in transaction'
		);

		$this->transaction = new Transaction($this);

		return $this->transaction;
	}

	/**
	 * Whether a database handle is connected
	 * @return boolean
	 */
	abstract function isConnected();

	/**
	 * Connects to the dabase using the data specified inside handle
	 * @throws DBConnectionException
	 * @return DB itself
	 */
	abstract function connect($force = false);

	/**
	 * Disconnects the handle from the database
	 * @return void
	 */
	abstract function disconnect();

	/**
	 * Returns the number of rows affected by the query sent via sendQuery()
	 * @see DB::sendQuery()
	 * @return integer
	 */
	abstract function getAffectedRowsNumber(DBQueryResult $result);

	/**
	 * Returns the number of rows fetched by the query sent via sendQuery()
	 * @see DB::sendQuery()
	 * @return integer
	 */
	abstract function getFetchedRowsNumber(DBQueryResult $result);

	/**
	 * Returns a SQL dialect that conforms the database
	 * @return IDialect
	 */
	abstract function getDialect();

	/**
	 * Passes the query to the database and returns the query result, without fetching
	 * the result.
	 * @throws DBQueryException
	 * @see DB::getAffectedRowsNumber()
	 * @see DB::getFetchedRowsNumber()
	 * @return DBQueryResult
	 */
	abstract function sendQuery(ISqlQuery $query, $isAsync = false);

	/**
	 * Passes the query to the database and fetches a single-row result as an array representing
	 * a row
	 * @throws RowNotFoundException
	 * @return array
	 */
	abstract function getRow(ISqlSelectQuery $query);

	/**
	 * Passes the query to the database and fetches the set of resulting rows. If nothing to
	 * fetch, empty array is returned
	 * @return array
	 */
	abstract function getRows(ISqlSelectQuery $query);

	/**
	 * Passes the query to the database and fetches the first field of each row from a set of rows.
	 * Returns the array representing the set of column values, or empty array if no rows found.
	 * @return array
	 */
	abstract function getColumn(ISqlSelectQuery $query);

	/**
	 * Passes the query to the database and fetches the first field from a single-row result
	 * @throws CellNotFoundException
	 * @return scalar
	 */
	abstract function getCell(ISqlSelectQuery $query);

	/**
	 * Gets the column value generator
	 * @return IIDGenerator
	 */
	abstract function getGenerator($tableName, $columnName, DBType $type);
}

?>