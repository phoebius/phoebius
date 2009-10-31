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
 * Represents the PostgreSQL database handle
 * @ingroup Dal_DB
 */
class PgSqlDB extends DB
{
	/**
	 * @var array
	 */
	private $preparedStatements = array();

	/**
	 * @var resource|null
	 */
	private $link = null;

	/**
	 * @return PgSqlDB
	 */
	static function create()
	{
		return new self;
	}

	/**
	 * Connects to the database using the data specified inside handle
	 * @throws DBConnectionException
	 * @return PgSqlDB an object itself
	 */
	function connect($force = false)
	{
		if ($this->isConnected() && !$force) {
			return $this;
		}

		$connectionParameters = array();

		$connectionParameters['host'] = (string) $this->getHost();
		$connectionParameters['user'] = (string) $this->getUser();

		if ($this->getPassword()) {
			$connectionParameters['password'] = $this->getPassword();
		}

		if ($this->getDBName()) {
			$connectionParameters['dbname'] = $this->getDBName();
		}

		if ($this->getPort()) {
			$connectionParameters['port'] = $this->getPort();
		}

		$connectionString = array();
		foreach ($connectionParameters as $key => $value) {
			$connectionString[] = $key . '=' . $this->getDialect()->quoteValue($value);
		}

		$connectionString = join(' ', $connectionString);

		try {
			if ($this->isPersistent()) {
				$this->link = pg_pconnect($connectionString);
			}
			else {
				$this->link = pg_pconnect(
					$connectionString,
					$force
						? PGSQL_CONNECT_FORCE_NEW
						: null
				);
			}
		}
		catch (ExecutionContextException $e) {
			throw new DBConnectionException(
				$this,
				"can not connect using {$connectionString}: {$e->getMessage()}"
			);
		}

		$this->preparedStatements = array();

		if ($this->getEncoding()) {
			$this->setEncoding($this->getEncoding());
		}

		pg_set_error_verbosity($this->link, PGSQL_ERRORS_TERSE);

		return $this;
	}

	/**
	 * Disconnects the handle from the database
	 * @return PgSqlDB an object itself
	 */
	function disconnect()
	{
		if ($this->isConnected()) {
			try {
				pg_close($this->link);
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
		Assert::isTrue($id->isValid($this));

		return pg_affected_rows($id->getResultId());
	}

	/**
	 * Returns the number of rows fetched by the query with the specified result identifier
	 * @return integer
	 */
	function getFetchedRowsNumber(DBQueryResultId $id)
	{
		Assert::isTrue($id->isValid($this));

		return pg_num_rows($id->getResultId());
	}

	/**
	 * Returns the SQL dialect that conforms the database handle
	 * @return IDialect
	 */
	function getDialect()
	{
		return PgSqlDialect::getInstance();
	}

	/**
	 * Overridden. Sets the encoding of the database
	 * @param string $encoding
	 * @return PgSqlDB
	 */
	function setEncoding($encoding)
	{
		parent::setEncoding($encoding);

		if ($this->isConnected()) {
			$result = pg_set_client_encoding($this->link, $encoding);

			Assert::isFalse($result == -1, "invalid encoding `{$encoding}` is specified");
		}

		return $this;
	}

	/**
	 * Passes the query to the database and returns the resulting resource id, without fetching
	 * the result.
	 * @throws UniqueViolationException
	 * @throws PgSqlQueryException
	 * @return DBQueryResultId
	 */
	function sendQuery(ISqlQuery $query, $isAsync = false)
	{
		try {
			$resource = $this->performQuery(
				$query,
				(APP_SLOT_CONFIGURATION & SLOT_CONFIGURATION_SEVERITY_VERBOSE) == 0
					? $isAsync
					: false
			);
		}
		catch (PgSqlQueryException $e) {
			// If query is async and it fails, we should alert about it as loud as possible
			Assert::isFalse(
				$isAsync,
				'Async query failed with code %s (%s) and message %s. The query itself: %s',
				$e->getSystemMessage()->getErrorCode(),
				$e->getSystemMessage()->getErrorDescription(),
				$e->getMessage(),
				$query->toDialectString($this->getDialect())
			);

			throw $e;
		}

		return new DBQueryResultId($this, $resource);
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
			pg_num_rows($result) <= 1,
			'query returned too many rows (only one is expected)'
		);

		$row = pg_fetch_assoc($result);
		pg_free_result($result);

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

			while (($row = pg_fetch_row($result))) {
				$array[] = reset($row);
			}

			pg_free_result($result);

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
			$row = pg_fetch_row($result);
			$cell = reset($row);

			pg_free_result($result);

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

			while (($row = pg_fetch_assoc($result))) {
				$array[] = $row;
			}

			pg_free_result($result);

			return $array;
		}
		else {
			return array();
		}
	}

	/**
	 * @throws PgSqlQueryException
	 * @return string
	 */
	private function prepareQuery(ISqlQuery $query, $isAsync)
	{
		Assert::isBoolean($isAsync);

		$queryAsString = $query->toDialectString($this->getDialect());
		$statementId = md5($queryAsString);

		if (!isset($this->preparedStatements[$statementId])) {
			pg_send_prepare($this->link, $statementId, $queryAsString);
			$result = pg_get_result($this->link);

			if (PGSQL_COMMAND_OK != pg_result_status($result, PGSQL_STATUS_LONG))
			{
				$errorCode = pg_result_error_field($result, PGSQL_DIAG_SQLSTATE);
				// We generate a uniq statement id for each query so prepared statement
				// duplication occcurs here only within the persistent connection
				// (because prepared statements are shared between different clients
				// that use the same connection)
				if (PgSqlError::DUPLICATE_PREPARED_STATEMENT == $errorCode) {
					Assert::isTrue(
						$this->isPersistent(),
						'invalid generation of statementId: duplication of statementId'
					);
				}
				else {
					$errorMessage = pg_result_error_field($result, PGSQL_DIAG_MESSAGE_PRIMARY);
					throw new PgSqlQueryException($query, $errorMessage, $errorCode);
				}
			}

			$this->preparedStatements[$statementId] = true;
		}

		return $statementId;
	}

	/**
	 * @throws PgSqlQueryException
	 * @throws UniqueViolationException
	 * @param ISqlQUery $query
	 * @param boolean $isAsync
	 * @return resource
	 */
	protected function performQuery(ISqlQuery $query, $isAsync)
	{
		Assert::isBoolean($isAsync);

		$statementId = $this->prepareQuery($query, $isAsync);
		$parameters = $query->getCastedParameters($this->getDialect());

		$executeResult = pg_send_execute($this->link, $statementId, $parameters);
		if (!$isAsync || !$executeResult) {
			$result = pg_get_result($this->link);
			$resultStatus = pg_result_status($result, PGSQL_STATUS_LONG);
			if (
					in_array($resultStatus, array (
							PGSQL_EMPTY_QUERY, PGSQL_BAD_RESPONSE,
							PGSQL_NONFATAL_ERROR, PGSQL_FATAL_ERROR
						)
					)
			) {
				$errorCode = pg_result_error_field($result, PGSQL_DIAG_SQLSTATE);
				$errorMessage = pg_result_error_field($result, PGSQL_DIAG_MESSAGE_PRIMARY);

				if (PgSqlError::UNIQUE_VIOLATION == $errorCode) {
					Assert::isFalse($query instanceof ISqlSelectQuery);

					throw new UniqueViolationException($query, $errorMessage);
				}
				else {
					throw new PgSqlQueryException($query, $errorMessage, $errorCode);
				}
			}
		}

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

	private $generatedIds = array();

	/**
	 * @return int|null
	 */
	function preGenerate($tableName, $columnName)
	{
		$this->generatedIds[$tableName.$columnName] = $this->getCell(
			SelectQuery::create()
				->getExpr(
					SqlFunction::create('nextval')->addArg(
						new ScalarSqlValue(
							PgSqlDialect::getInstance()->getSequenceName($tableName, $columnName)
						)
					)
				)
		);

		return $this->generatedIds[$tableName.$columnName];
	}

	/**
	 * @return scalar
	 */
	function getGeneratedId($tableName, $columnName)
	{
		return $this->generatedIds[$tableName.$columnName];
	}
}

?>