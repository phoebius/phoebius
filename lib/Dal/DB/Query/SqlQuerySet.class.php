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
 * A query set
 *
 * @ingroup Dal_DB_Query
 */
class SqlQuerySet implements ISqlQuery
{
	/**
	 * @var array of ISqlQuery
	 */
	private $queries = array();

	/**
	 * @param array $queries set of ISqlQuery
	 */
	function __construct(array $queries = array())
	{
		$this->addQueries($queries);
	}
	
	function addQuery(ISqlQuery $query)
	{
		$this->queries[] = $query;
		
		return $this;
	}
	
	function addQueries(array $queries)
	{
		foreach ($queries as $query) {
			$this->addQuery($query);
		}
		
		return $this;
	}
	
	function getQueries()
	{
		return $this->queries;
	}
	
	function merge(SqlQuerySet $set) 
	{
		$this->queries = array_merge($this->queries, $set->queries);
		
		return $this;
	}

	function toDialectString(IDialect $dialect)
	{
		$sql = array();
		
		foreach ($this->queries as $query) {
			$sql[] = $query->toDialectString($dialect);
		}
		
		return join(
			StringUtils::DELIM_STANDART . StringUtils::DELIM_STANDART, 
			$sql
		);
	}

	function getPlaceholderValues(IDialect $dialect)
	{
		return array ();
	}
}

?>