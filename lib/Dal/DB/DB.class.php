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
 * Default implementation of DAL - database abstraction layer
 * @ingroup Dal
 */
abstract class DB implements IFactory
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
	 * @return DB an object itself
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
	 * @return DB an object itself
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
	 * @return DB an object itself
	 */
	function makePersistent()
	{
		Assert::isFalse($this->isConnected(), 'already connected');

		$this->isPersistent = true;

		return $this;
	}

	/**
	 * Makes a connection to the database to be NOT persistent
	 * @return DB an object itself
	 */
	function makeNotPersistent()
	{
		Assert::isFalse($this->isConnected(), 'already connected');

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
	 * @return DB an object itself
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
	 * @return DB an object itself
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
	 * @return DB an object itself
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
	 * Not yet implemented. Gets the number of queries sent to the database
	 * @return integer
	 */
	function getQueryNumber()
	{
		Assert::notImplemented();
	}

	/**
	 * Not yet implemented. Gets the time spent on processing queries sent to the database
	 * @return float
	 */
	function getQueryTime()
	{
		Assert::notImplemented();
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
	 * Determines whether a database handle is connectied to a remote server
	 * @return boolean
	 */
	abstract function isConnected();

	/**
	 * Connects to the dabase using the data specified inside handle
	 * @throws DBConnectionException
	 * @return DB an object itself
	 */
	abstract function connect($force = false);

	/**
	 * Disconnects the handle from the database
	 * @return DB an object itself
	 */
	abstract function disconnect();

	/**
	 * Returns the number of rows affected by the query with the specified result identifier
	 * @return integer
	 */
	abstract function getAffectedRowsNumber(DBQueryResultId $id);

	/**
	 * Returns the number of rows fetched by the query with the specified result identifier
	 * @return integer
	 */
	abstract function getFetchedRowsNumber(DBQueryResultId $id);

	/**
	 * Returns the SQL dialect that conforms the database handle
	 * @return IDialect
	 */
	abstract function getDialect();

	/**
	 * Passes the query to the database and returns the resulting resource id, without fetching
	 * the result.
	 * @throws UniqueViolationException
	 * @throws DBQueryException
	 * @return DBQueryResultId
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
	 * Returns next_id uid
	 * For oracle-family databases, obtains the id by incrementing a generator
	 * For mssql-family database makes nothing (will use LAST_INSERT_ID() and etc later on IDialect::getGeneratedValue())
	 * MUST BE CALLED before the insert query
	 */
	abstract function preGenerate($tableName, $columnName);

	/**
	 * MUST BE CALLED RIGHT AFTER the insert query
	 * @return scalar
	 */
	abstract function getGeneratedId($tableName, $columnName);
}

?>