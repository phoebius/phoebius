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
 * Represents a date and a time
 *
 * @see Date for representing date only
 *
 * @ingroup Core_Types_Complex
 */
final class Timestamp extends Date
{
	/**
	 * @var Time
	 */
	private $time;

	/**
	 * Static constructor of a Timestamp object
	 *
	 * @param int $input unix timestamp or textual representation of the date
	 *
	 * @return Timestamp
	 */
	static function create($input)
	{
		return new self ($input);
	}

	static function cast($value)
	{
		return new self ($value);
	}

	static function getOrmPropertyType(AssociationMultiplicity $multiplicity)
	{
		$type = new DBType(
			DBType::DATETIME,
			/* is nullable */$multiplicity->isNullable(),
			/* size */null,
			/* precision */null,
			/* scale */null,
			/* is generated */false
		);

		return $type->getOrmPropertyType();
	}

	/**
	 * Creates a Timestamp object that represents current application date and time
	 *
	 * @return Timestamp
	 */
	static function now()
	{
		return new self (time());
	}

	/**
	 * Gets the hours of the Timestamp object
	 *
	 * @return int
	 */
	function getHour()
	{
		return $this->time->getHour();
	}

	/**
	 * Gets the minutes of the Timestamp object
	 *
	 * @return int
	 */
	function getMinute()
	{
		return $this->time->getMinute();
	}

	/**
	 * Gets the seconds of the Timestamp object
	 */
	function getSecond()
	{
		return $this->time->getSecond();
	}

	/**
	 * Gets the Time object
	 *
	 * @return Time
	 */
	function getTime()
	{
		return $this->time;
	}

	function equals(Date $timestamp)
	{
		return ($this->int == $timestamp->int);
	}

	function toFormattedString($format = 'Y/m/d H:i:s')
	{
		return parent::toFormattedString($format);
	}

	protected function import($int)
	{
		$this->time = new Time($int);

		parent::import($int);
	}
}

?>