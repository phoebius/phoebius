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
 * Base types that are mandatory to be implemented by the database drivers. This types are fundamental
 * for ORM entities.
 *
 * @ingroup Dal_DB
 */
final class DBType extends Enumeration implements ISqlType
{
	// primitive
	const BOOLEAN = 'boolean';

	// integers
	const INT16 = 'int16';
	const INT32 = 'int32';
	const INT64 = 'int64';

	// unsigned integers
	const UINT16 = 'uint16';
	const UINT32 = 'uint32';
	const UINT64 = 'uint64';

	// floating-point
	const CURRENCY = 'currency';
	const DECIMAL = 'decimal';
	const FLOAT = 'float';

	// string
	const BINARY = 'binary';
	const CHAR = 'char';
	const VARCHAR = 'varchar';

	// date and time
	const DATE = 'date';
	const TIME = 'time';
	const DATETIME = 'datetime';

	private static $hasSize = array(
		self::VARCHAR, self::CHAR, self::BINARY
	);

	private static $hasPrecision = array(
		self::CURRENCY, self::DECIMAL, self::FLOAT
	);

	private static $hasScale = array(
		self::CURRENCY, self::DECIMAL
	);

	private static $canBeGenerated = array(
		self::UINT16, self::UINT32, self::UINT64
	);

	private $isNullable = false;
	private $isGenerated = false;

	private $size;
	private $precision;
	private $scale;

	/**
	 * @return DBType
	 */
	static function create(
			$id,
			$nullable = false,
			$size = null,
			$precision = null,
			$scale = null,
			$generated = false
		)
	{
		return new self ($id, $nullable, $size, $precision, $scale, $generated);
	}

	/**
	 * @return boolean
	 */
	static function hasMember($id)
	{
		try {
			new self ($id);

			return true;
		}
		catch (Exception $e) {
			return false;
		}
	}

	function __construct(
			$id,
			$nullable = false,
			$size = null,
			$precision = null,
			$scale = null,
			$generated = false
		)
	{
		parent::__construct($id);

		$this->setIsNullable($nullable);

		if ($this->canHaveSize()) {
			$this->setSize($size);
		}
		else if ($this->canHavePrecision()) {
			$this->setPrecision($precision);

			if ($this->canHaveScale()) {
				$this->setScale($scale);
			}
		}

		if ($this->canBeGenerated()) {
			$this->setGenerated($generated);
		}
	}

	/**
	 * @return OrmPropertyType
	 */
	function getOrmPropertyType()
	{
		switch ($this->getValue()) {
			case self::DATE: {
				return new BoxablePropertyType('Date', $this);
			}
			case self::TIME: {
				return new BoxablePropertyType('Time', $this);
			}
			case self::DATETIME: {
				return new BoxablePropertyType('Timestamp', $this);
			}
			default: { // booleans' special case is handled internally
				return new FundamentalPropertyType($this);
			}
		}
	}

	function canHaveSize()
	{
		return in_array($this->getValue(), self::$hasSize);
	}

	function canHavePrecision()
	{
		return in_array($this->getValue(), self::$hasPrecision);
	}

	function canHaveScale()
	{
		return in_array($this->getValue(), self::$hasScale);
	}

	function canBeGenerated()
	{
		return in_array($this->getValue(), self::$canBeGenerated);
	}

	/**
	 * @return int|null
	 */
	function getSize()
	{
		return $this->size;
	}

	/**
	 * @return int|null
	 */
	function getPrecision()
	{
		return $this->precision;
	}

	/**
	 * @return int|null
	 */
	function getScale()
	{
		return $this->scale;
	}

	/**
	 * @return boolean
	 */
	function isGenerated()
	{
		return $this->isGenerated;
	}

	/**
	 * @return DBType
	 */
	function setSize($size = null)
	{
		if (!$this->canHaveSize()) {
			$size = null;
		}

		if (!is_null($size)) {
			$size = abs((int) $size);
		}

		$this->size = $size;

		return $this;
	}

	/**
	 * @return DBType
	 */
	function setPrecision($precision = null)
	{
		if (!$this->canHavePrecision()) {
			$this->precision = null;
		}

		if (!is_null($precision)) {
			$size = abs((int) $precision);
		}

		$this->precision = $precision;

		return $this;
	}

	/**
	 * @return DBType
	 */
	function setScale($scale = null)
	{
		if (!$this->canHaveScale()) {
			$scale = null;
		}

		if (!is_null($scale)) {
			$size = abs((int) $scale);
		}

		$this->scale = $scale;

		return $this;
	}

	/**
	 * @return DBType
	 */
	function setGenerated($flag)
	{
		if (!$this->canBeGenerated()) {
			$flag = false;
		}

		Assert::isBoolean($flag);

		$this->isGenerated = $flag;

		return $this;
	}

	/**
	 * @return DBType
	 */
	function setIsNullable($flag)
	{
		Assert::isBoolean($flag);

		$this->isNullable = $flag;

		return $this;
	}

	/**
	 * @return boolean
	 */
	function isNullable()
	{
		return $this->isNullable;
	}

	function toDialectString(IDialect $dialect)
	{
		return $dialect->getTypeRepresentation($this);
	}

	function toPhpCodeCall()
	{
		return
			'new DBType('.
				join(', ', array(
					'DBType::' . $this->getId(),
					$this->isNullable ? 'true' : 'false',
					null != $this->size ? $this->size : 'null',
					null != $this->precision ? $this->precision : 'null',
					null != $this->scale ? $this->scale : 'null',
					$this->isGenerated ? 'true' : 'false',
				))
			. ')';
	}
}

?>