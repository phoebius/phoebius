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
 * Represents the PostgreSQL DAL
 *
 * @ingroup Dal_DB
 */
class PgSqlDB extends DB
{
	/**
	 * @var PgSqlDialect|null
	 */
	private $dialect;

	/**
	 * @var array
	 */
	private $preparedStatements = array();

	/**
	 * @var resource|null
	 */
	private $link = null;

	/**
	 * @var array of DBQueryResult
	 */
	private $queryResults = array();

	/**
	 * @return PgSqlDB
	 */
	static function create()
	{
		return new self;
	}

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

	function getAffectedRowsNumber(DBQueryResult $result)
	{
		Assert::isTrue(
			isset($this->queryResults[spl_object_hash($result)]),
			'unknown DBQueryResult'
		);

		return pg_affected_rows($result->getResource());
	}

	function getFetchedRowsNumber(DBQueryResult $result)
	{
		Assert::isTrue(
			isset($this->queryResults[spl_object_hash($result)]),
			'unknown DBQueryResult'
		);

		return pg_num_rows($result->getResource());
	}

	function getDialect()
	{
		if (!$this->dialect) {
			$this->dialect = PgSqlDialect::getInstance();
		}

		return $this->dialect;
	}

	function setEncoding($encoding)
	{
		parent::setEncoding($encoding);

		if ($this->isConnected()) {
			$result = pg_set_client_encoding($this->link, $encoding);

			Assert::isFalse(
				$result == -1,
				'invalid encoding `%s` is specified',
				$encoding
			);
		}


		return $this;
	}

	function sendQuery(ISqlQuery $query, $isAsync = false)
	{
		$resource = $this->performQuery(
			$query,
			(APP_SLOT_CONFIGURATION & SLOT_CONFIGURATION_SEVERITY_VERBOSE) == 0
				? $isAsync
				: false
		);

		$result = new DBQueryResult($this, $resource);

		$this->queryResults[spl_object_hash($result)] = true;

		return $result;
	}

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

		// TODO ISqlQuery should generate its own hash by itself
		$queryAsString = $query->toDialectString($this->getDialect());
		$statementId = md5($queryAsString);

		if (!isset($this->preparedStatements[$statementId])) {
			pg_send_prepare($this->link, $statementId, $queryAsString);
			$result = pg_get_result($this->link);

			if (PGSQL_COMMAND_OK != pg_result_status($result, PGSQL_STATUS_LONG))
			{
				$errorCode = pg_result_error_field($result, PGSQL_DIAG_SQLSTATE);
				// We generate a unique statement id for each query so prepared statement
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
	 * @throws DBQueryException
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

	function isConnected()
	{
		return is_resource($this->link);
	}

	/**
	 * @todo replace seq with `RETURNING` syntax
	 */
	function getGenerator($tableName, $columnName, DBType $type)
	{
		$query =
			SelectQuery::create()
				->getExpr(
					SqlFunction::create('nextval')->addArg(
						new ScalarSqlValue(
							$this->getDialect()->getSequenceName($tableName, $columnName)
						)
					)
				);

		return new SequenceGenerator($this, $query);
	}
}

?>