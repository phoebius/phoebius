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
 * FIXME: rename has{Size,Precision,Scale,Timezone} to canHave{Size,PrecisionScale,Timezone}
 * @see NHibernate/Dialect/TypeNames.cs NHibernate.Dialect.TypeNames
 * @see NHibernate/Dialect/PostgreSQLDialect.cs NHibernate.Dialect.PostgreSQLDialect
 * @see http://msdn.microsoft.com/en-us/library/system.data.dbtype.aspx
 * @ingroup Dal_DB
 */
class DBType extends Enumeration implements ISqlCastable
{
	const SMALL_INTEGER   = 0x030101;
	const INTEGER         = 0x030102;
	const BIG_INTEGER     = 0x030103;

	/**
	 * Arbitrary Precision number
	 * Postgresql manual: 8.1.2. Arbitrary Precision Numbers
	 */
	const NUMERIC         = 0x000604;

	/**
	 * floating point number
	 * Postgresql manual: 8.1.3. Floating-Point Types
	 */
	const FLOAT           = 0x000205;

	const STRING          = 0x000106;
	const BOOLEAN         = 0x000007;

	const DATE            = 0x000008;
	const TIME            = 0x000009;
	const DATETIME        = 0x00080A;

	const INTERVAL        = 0x00000B;

	const BINARY          = 0x00000C;

	private $size;
	private $precision;
	private $scale;
	private $hasTimezone = false;
	private $isUnsigned = false;
	private $isGenerated = false;
	private $isNullable = false;

	/**
	 * @return DBType
	 */
	static function create($id)
	{
		return new self ($id);
	}

	/**
	 * @return boolean
	 */
	function hasSize()
	{
		return ($this->getValue() & DBTypeFlag::HAS_SIZE) > 0;
	}

	/**
	 * @return boolean
	 */
	function hasPrecision()
	{
		return ($this->getValue() & DBTypeFlag::HAS_PRECISION) > 0;
	}

	/**
	 * @return boolean
	 */
	function hasScale()
	{
		return ($this->getValue() & DBTypeFlag::HAS_SCALE) > 0;
	}

	/**
	 * @return boolean
	 */
	function hasTimezone()
	{
		return ($this->getValue() & DBTypeFlag::HAS_TIMEZONE) > 0;
	}

	/**
	 * @return boolean
	 */
	function canBeUnsigned()
	{
		return ($this->getValue() & DBTypeFlag::CAN_BE_UNSIGNED) > 0;
	}

	/**
	 * @return boolean
	 */
	function canBeGenerated()
	{
		return ($this->getValue() & DBTypeFlag::CAN_BE_GENERATED) > 0;
	}

	/**
	 * @return int|null
	 */
	function getSize()
	{
		Assert::isTrue($this->hasSize());

		return $this->size;
	}

	/**
	 * @return int|null
	 */
	function getPrecision()
	{
		Assert::isTrue($this->hasPrecision());

		return $this->precision;
	}

	/**
	 * @return int|null
	 */
	function getScale()
	{
		Assert::isTrue($this->hasScale());

		return $this->scale;
	}

	/**
	 * @return boolean|null
	 */
	function isUnsigned()
	{
		return $this->isUnsigned;
	}

	/**
	 * @return boolean|null
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
		Assert::isTrue($this->hasSize());

		if (!is_null($size)) {
			Assert::isPositiveInteger($size);
		}

		$this->size = (int) $size;

		return $this;
	}

	/**
	 * @return DBType
	 */
	function setPrecision($precision = null)
	{
		Assert::isTrue($this->hasPrecision());

		if (!is_null($precision)) {
			Assert::isPositiveInteger($precision);
		}

		$this->precision = (int) $precision;

		return $this;
	}

	/**
	 * @return DBType
	 */
	function setScale($scale = null)
	{
		Assert::isTrue($this->hasScale());

		if (!is_null($scale)) {
			Assert::isPositiveInteger($scale);
		}

		$this->scale = (int) $scale;

		return $this;
	}

	/**
	 * @return DBType
	 */
	function withTimezeone()
	{
		Assert::isTrue($this->hasTimezone());

		$this->hasTimezone = true;

		return $this;
	}

	/**
	 * @return DBType
	 */
	function withoutTimezone()
	{
		Assert::isTrue($this->hasTimezone());

		$this->hasTimezone = false;

		return $this;
	}

	/**
	 * @return DBType
	 */
	function setUnsigned($flag)
	{
		Assert::isTrue($this->canBeUnsigned());
		Assert::isBoolean($flag);

		$this->isUnsigned = $flag;

		return $this;
	}

	/**
	 * @return DBType
	 */
	function makeUnsigned()
	{
		Assert::isTrue($this->canBeUnsigned());

		$this->isUnsigned = true;

		return $this;
	}

	/**
	 * @return DBType
	 */
	function makeNotUnsigned()
	{
		Assert::isTrue($this->canBeUnsigned());

		$this->isUnsigned = false;

		return $this;
	}

	/**
	 * @return DBType
	 */
	function setGenerated($flag)
	{
		Assert::isTrue($this->canBeGenerated());
		Assert::isBoolean($flag);

		$this->isGenerated = $flag;

		return $this;
	}

	/**
	 * @return DBType
	 */
	function makeGenerated()
	{
		Assert::isTrue($this->canBeGenerated());

		$this->isGenerated = true;

		return $this;
	}

	/**
	 * @return DBType
	 */
	function makeNotGenerated()
	{
		Assert::isTrue($this->canBeGenerated());

		$this->isGenerated = false;

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
	 * @return DBType
	 */
	function setIsNotNullable($flag)
	{
		Assert::isBoolean($flag);

		$this->isNullable = !$flag;

		return $this;
	}

	/**
	 * @return boolean
	 */
	function isNullable()
	{
		return $this->isNullable;
	}

	/**
	 * @return boolean
	 */
	function isNotNullable()
	{
		return !$this->isNullable;
	}

	/**
	 * @return DBType
	 */
	function makeNullable()
	{
		$this->isNullable = true;

		return $this;
	}

	/**
	 * @return DBType
	 */
	function makeNotNullable()
	{
		$this->isNullable = false;

		return $this;
	}

	/**
	 * FIXME: reimplement, create DBType=>database translation tables inside IDialect
	 * Casts an object to the SQL dialect string
	 * @return string
	 */
	function toDialectString(IDialect $dialect)
	{
		return $dialect->getTypeRepresentation($this);

		$postfix = '';

		if ($this->isGenerated && $dialect->getDBDriver()->is(DBDriver::MYSQL)) {
			$postfix .= ' AUTO_INCREMENT';
		}

		if (!$this->isNullable) {
			$postfix .= ' NOT NULL';
		}

		return $this->getId() . $postfix;
	}
}

?>