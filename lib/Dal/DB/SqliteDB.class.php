<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright (c) 2010 Scand Ltd.
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
 * Represents the Sqlite DAL
 *
 * @ingroup Dal_DB
 */
class SqliteDB extends DB
{
	/**
	 * For sqlite_num_rows()
	 *
	 * @var DBQueryResult
	 */
	private $latestQueryResultId;

	/**
	 * @var array of DBQueryResult
	 */
	private $queryResults = array();

	/**
	 * @var resource|null
	 */
	private $link;

    /**
     * @var string
     */
    private $filename;

	/**
	 * Static alias for the ctor
	 *
	 * @return SqliteDB
	 */
	static function create($filename)
	{
		return new self ($filename);
	}

    function  __construct($filename)
    {
        $this->filename = $filename;
    }

	function connect($force = false)
	{
		if ($this->isConnected() && !$force) {
			return $this;
		}
        
        if ($this->isPersistent()) {
			LoggerPool::log(parent::LOG_VERBOSE, 'obtaining a persistent connection to Sqlite: %s', $this->filename);
            $this->link = sqlite_popen($this->filename);
        }
        else {
			LoggerPool::log(parent::LOG_VERBOSE, 'establishing a new connection to Sqlite: %s', $this->filename);
            $this->link = sqlite_open($this->filename);
        }

        if (!$this->link) {
            $error = sqlite_error_string(sqlite_last_error($this->link));

			LoggerPool::log(parent::LOG_VERBOSE, 'failed to connect: %s', $error);

			throw new DBConnectionException($this, $error);
        }

        return $this;
	}

	function disconnect()
	{
		if ($this->isConnected()) {
			sqlite_close($this->link);
		}

		return $this;
	}

	function getAffectedRowsNumber(DBQueryResult $result)
	{
		Assert::isTrue(
			$this->latestQueryResultId === $result,
			'affected rows number can be obtained only for the latest sent query'
		);

		return sqlite_changes($this->link);
	}

	function getFetchedRowsNumber(DBQueryResult $result)
	{
		Assert::isTrue(
			isset($this->queryResults[spl_object_hash($result)]),
			'unknown DBQueryResult'
		);

		return sqlite_num_rows($result->getResource());
	}

	function getDialect()
	{
		return new SqliteDBDialect();
	}

	function setEncoding($encoding)
	{
		Assert::notImplemented();
	}

	function sendQuery(ISqlQuery $query, $isAsync = false)
	{
		$resource = $this->performQuery($query, $isAsync);

		$this->latestQueryResultId = new DBQueryResult($this, $resource);

		$this->queryResults[spl_object_hash($this->latestQueryResultId)] = true;

		return $this->latestQueryResultId;
	}

	function getRow(ISqlSelectQuery $query)
	{
		$result = $this->performQuery($query, false);

		Assert::isTrue(
			sqlite_num_rows($result) <= 1,
			'query returned too many rows (only one is expected)'
		);

		$row = sqlite_fetch_array($result);

		if (!$row) {
			throw new RowNotFoundException($query);
		}

		return $row;
	}

	function getColumn(ISqlSelectQuery $query)
	{
		$result = $this->performQuery($query, false);

        $array = array();

        while (($row = sqlite_fetch_single($result))) {
            $array[] = $row;
        }

        return $array;
	}

	function getCell(ISqlSelectQuery $query)
	{
		$result = $this->performQuery($query, false);

        $cell = sqlite_fetch_single($result);

        if (!$cell) {
			throw new CellNotFoundException($query);
		}
        
		return $cell;
	}

	function getRows(ISqlSelectQuery $query)
	{
		$result = $this->performQuery($query, false);

        $array = array();

        while (($row = sqlite_fetch_array($result))) {
            $array[] = $row;
        }

        return $array;
	}

	/**
	 * @throws DBQueryException
	 * @param ISqlQUery $query
	 * @param boolean $isAsync
	 * @return resource
	 */
	protected function performQuery(ISqlQuery $query, $isAsync)
	{
		$queryAsString = $query->toDialectString($this->getDialect());

		LoggerPool::log(parent::LOG_VERBOSE, 'sending query: %s', $queryAsString);
		LoggerPool::log(parent::LOG_QUERY, $queryAsString);

		$result = sqlite_query($queryAsString, $this->link);

		if (!$result) {
			$code = sqlite_last_error($this->link);
			$error = sqlite_error_string($code);

			if ($code == 19) {
				LoggerPool::log(parent::LOG_VERBOSE, 'query caused a unique violation #%s: %s', $code, $error);

				throw new UniqueViolationException($query, $error);
			}
			else {
				LoggerPool::log(parent::LOG_VERBOSE, 'query caused an error #%s: %s', $code, $error);

				throw new DBQueryException($query, $error, $code);
			}
		}

		Assert::isTrue(
			is_resource($result) || $result === true
		);

		return $result;
	}

	function isConnected()
	{
		return is_resource($this->link);
	}

	function getGenerator($tableName, $columnName, DBType $type)
	{
		return new LastInsertIdGenerator($this);
	}

	/**
	 * @return int
	 */
	function getLastInsertId()
	{
		return sqlite_last_insert_rowid($this->link);
	}
}

?>