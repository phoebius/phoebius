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
 * Represents an ORDER BY direction
 * @ingroup Sql
 */
final class SqlOrderDirection extends Enumeration implements ISqlCastable
{
	const NONE = '';
	const ASC = 'ASC';
	const DESC = 'DESC';

	/**
	 * Creates an instance of {@link SqlOrderDirection}
	 * @return SqlOrderDirection
	 */
	static function create($id)
	{
		return new self($id);
	}

	/**
	 * @return SqlOrderDirection
	 */
	static function asc()
	{
		return new self(self::ASC);
	}

	/**
	 * @return SqlOrderDirection
	 */
	static function desc()
	{
		return new self(self::DESC);
	}

	/**
	 * @return SqlOrderDirection
	 */
	static function none()
	{
		return new self(self::NONE);
	}

	/**
	 * Casts an object to the SQL dialect string
	 * @return string
	 */
	function toDialectString(IDialect $dialect)
	{
		return $this->getValue();
	}
}

?>