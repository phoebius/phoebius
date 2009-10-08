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
 * @ingroup Sql
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