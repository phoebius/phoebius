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

	// for raw implementation
	const OBJECT = 'object';

	private $hasSize = array(
		self::VARCHAR, self::CHAR, self::BINARY
	);

	private $hasPrecision = array(
		self::CURRENCY, self::DECIMAL, self::FLOAT
	);

	private $hasScale = array(
		self::CURRENCY, self::DECIMAL
	);

	private $canBeGenerated = array(
		self::UINT16, self::UINT32, self::UINT64
	);

	private $isNullable = false;
	private $isGenerated = false;

	private $size;
	private $precision;
	private $scale;

	function __construct(
			$id,
			$isNullable = false,
			$size = null,
			$precision = null,
			$scale = null,
			$isGenerated = false
		)
	{
		$this->setIsNullable($isNullable);

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
			$this->setGenerated($isGenerated);
		}

		parent::__construct($id);
	}

	/**
	 * @return OrmPropertyType
	 */
	function getPropertyType()
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
			case self::OBJECT: {
				Assert::isUnreachable('DBType::OBJECT should not be used directly');
			}
			default: {
				return new PrimitivePropertyType($this);
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
		return
			$this->canHaveSize()
				? $this->size
				: null;
	}

	/**
	 * @return int|null
	 */
	function getPrecision()
	{
		return
			$this->canHavePrecision()
				? $this->precision
				: null;
	}

	/**
	 * @return int|null
	 */
	function getScale()
	{
		return
			$this->canHaveScale()
				? $this->scale
				: null;
	}

	/**
	 * @return boolean
	 */
	function isGenerated()
	{
		return
			$this->canBeGenerated() && $this->isGenerated
				? true
				: false;
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
}

?>