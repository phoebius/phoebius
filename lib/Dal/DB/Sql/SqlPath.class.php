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
 * @ingroup Dal_DB_Sql
 */
class SqlPath implements ISqlCastable
{
	private $path = array();

	function __construct(array $path)
	{
		$this->setPath($path);
	}

	/**
	 * @return SqlPath
	 */
	function addPathChunk($pathChunk)
	{
		Assert::isScalar($pathChunk);

		$this->path[] = $pathChunk;

		return $this;
	}

	/**
	 * @return SqlPath
	 */
	function setPath(array $path)
	{
		if (empty($path)) {
			throw new ArgumentException('path', 'cannot be empty');
		}

		$this->path = array();

		foreach ($path as $pathChunk) {
			$this->addPathChunk($pathChunk);
		}

		return $this;
	}

	/**
	 * Casts an object to the SQL dialect string
	 * @return string
	 */
	function toDialectString(IDialect $dialect)
	{
		$processedPathChunks = array();

		foreach ($this->path as $pathChunk) {
			$processedPathChunks[] = $dialect->quoteIdentifier($pathChunk);
		}

		return join('.', $processedPathChunks);
	}
}

?>