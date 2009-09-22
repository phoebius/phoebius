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
 * @ingroup Dal
 */
class MySqlDB extends DB
{
	/**
	 * For mysql_affected_rows
	 * @var DBQueryResultId
	 */
	private $latestQueryResultId;

	/**
	 * @var resource|null
	 */
	private $link = null;

	/**
	 * @var MySqlDialect
	 */
	private $myDialect;

	/**
	 * @return MySqlDB
	 */
	static function create()
	{
		return new self;
	}

	/**
	 * Connects to the database using the data specified inside handle
	 * @throws DBConnectionException
	 * @return MySqlDB an object itself
	 */
	function connect($force = false)
	{
		if ($this->isConnected() && !$force) {
			return $this;
		}


		$connectionArguments = array(
			$this->getHost(),
			$this->getUser(),
			$this->getPassword(),
			true,
			MYSQL_CLIENT_IGNORE_SPACE
		);

		$link = call_user_func_array(
			$this->isPersistent()
				? 'mysql_pconnect'
				: 'mysql_connect',
			$connectionArguments
		);

		if ($link) {
			$this->link = $link;
		}
		else {
			throw new DBConnectionException($this, mysql_error());
		}

		if (($dbname = $this->getDBName())) {
			if (!mysql_select_db($dbname, $this->link)) {
				throw new DBConnectionException($this, mysql_error($this));
			}
		}

		if ($this->getEncoding()) {
			$this->setEncoding($this->getEncoding());
		}

		return $this;
	}

	/**
	 * Disconnects the handle from the database
	 * @return MySqlDB an object itself
	 */
	function disconnect()
	{
		if ($this->isConnected()) {
			try {
				mysql_close($this->link);
			}
			catch (ExecutionContextException $e) {
				// nothing here
			}

			$this->link = null;
		}

		return $this;
	}

	/**
	 * Returns the number of rows affected by the query with the specified result identifier
	 * @return integer
	 */
	function getAffectedRowsNumber(DBQueryResultId $id)
	{
		Assert::isTrue(
			$this->latestQueryResultId === $id,
			'MySql can get the number of affected rows ONLY for the latest query send to the server'
		);

		return mysql_affected_rows($this->link);
	}

	/**
	 * Returns the number of rows fetched by the query with the specified result identifier
	 * @return integer
	 */
	function getFetchedRowsNumber(DBQueryResultId $id)
	{
		Assert::isTrue($id->isValid($this));

		return mysql_num_rows($id->getResultId());
	}

	/**
	 * Returns the SQL dialect that conforms the database handle
	 * @return IDialect
	 */
	function getDialect()
	{
		if (!$this->myDialect) {
			$this->myDialect = new MySqlDialect($this);
		}

		return $this->myDialect;
	}

	/**
	 * Overridden. Sets the encoding of the database
	 * @param string $encoding
	 * @return MySqlDB
	 */
	function setEncoding($encoding)
	{
		parent::setEncoding($encoding);

		if ($this->isConnected()) {
			$result = mysql_set_charset($encoding, $this->link);

			Assert::isTrue(
				$result,
				'invalid encoding "%s" is specified',
				$encoding
			);
		}

		return $this;
	}

	/**
	 * Passes the query to the database and returns the resulting resource id, without fetching
	 * the result.
	 * @throws UniqueViolationException
	 * @throws DBQueryException
	 * @return DBQueryResultId
	 */
	function sendQuery(ISqlQuery $query, $isAsync = false)
	{
		return $this->latestQueryResultId = new DBQueryResultId($this, $this->performQuery($query, $isAsync));
	}

	/**
	 * Passes the query to the database and fetches a single-row result as an array representing
	 * a row
	 * @throws RowNotFoundException
	 * @return array
	 */
	function getRow(ISqlSelectQuery $query)
	{
		$result = $this->performQuery($query, false);

		Assert::isTrue(
			mysql_num_rows($result) <= 1,
			'query returned too many rows (only one is expected)'
		);

		$row = mysql_fetch_assoc($result);
		mysql_free_result($result);

		if (!is_array($row)) {
			throw new RowNotFoundException($query);
		}

		return $row;
	}

	/**
	 * Passes the query to the database and fetches the first field of each row from a set of rows.
	 * Returns the array representing the set of column values, or empty array if no rows found.
	 * @return array
	 */
	function getColumn(ISqlSelectQuery $query)
	{
		$result = $this->performQuery($query, false);

		if ($result) {
			$array = array();

			while (($row = mysql_fetch_row($result))) {
				$array[] = reset($row);
			}

			mysql_free_result($result);

			return $array;
		}
		else {
			return array();
		}
	}

	/**
	 * Passes the query to the database and fetches the first field from a single-row result
	 * @throws CellNotFoundException
	 * @return scalar
	 */
	function getCell(ISqlSelectQuery $query)
	{
		$result = $this->performQuery($query, false);

		if ($result) {
			$row = mysql_fetch_row($result);
			$cell = reset($row);

			mysql_free_result($result);

			return $cell;
		}
		else {
			throw new CellNotFoundException($query);
		}
	}

	/**
	 * Passes the query to the database and fetches the set of resulting rows. If nothing to
	 * fetch, empty array is returned
	 * @return array
	 */
	function getRows(ISqlSelectQuery $query)
	{
		$result = $this->performQuery($query, false);

		if ($result) {
			$array = array();

			while (($row = mysql_fetch_assoc($result))) {
				$array[] = $row;
			}

			mysql_free_result($result);

			return $array;
		}
		else {
			return array();
		}
	}

	/**
	 * @throws DBQueryException
	 * @throws UniqueViolationException
	 * @param ISqlQUery $query
	 * @param boolean $isAsync
	 * @return resource
	 */
	protected function performQuery(ISqlQuery $query, $isAsync)
	{
		$result = mysql_query(
			$query->toDialectString($this->getDialect()),
			$this->link
		);

		if (!$result) {
			$code = mysql_errno($this->link);

			if ($code == 1062) {
				throw new UniqueViolationException($query, mysql_error($this->link));
			}
			else {
				throw new DBQueryException($query, mysql_error($this->link), $code);
			}
		}

		Assert::isTrue(
			is_resource($result) || $result === true
		);

		return $result;
	}

	/**
	 * Determines whether a database handle is connectied to a remote server
	 * @return boolean
	 */
	function isConnected()
	{
		return is_resource($this->link);
	}

	private $insertIdHook;

	/**
	 * @return int|null
	 */
	function preGenerate($tableName, $columnName)
	{
		$this->insertIdHook = $tableName.$columnName;

		return null;
	}

	/**
	 * @return scalar
	 */
	function getGeneratedId($tableName, $columnName)
	{
		Assert::isTrue(
			$this->insertIdHook == $tableName.$columnName,
			'wrong ID generation order: can achieve an ID for %s only',
			$this->insertIdHook
		);

		return mysql_insert_id($this->link);
	}

	/**
	 * For MySqlDialect
	 * @return resource
	 */
	function getLink()
	{
		return $this->link;
	}
}

?>