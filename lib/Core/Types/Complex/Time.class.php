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
 * Represents a time
 *
 * @ingroup Core_Types_Complex
 */
final class Time implements IBoxable, IOrmPropertyAssignable
{
	private $hour = '00';
	private $minute = '00';
	private $second = '00';

	/**
	 * @return Time
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
			DBType::TIME,
			/* is nullable */$multiplicity->isNullable(),
			/* size */null,
			/* precision */null,
			/* scale */null,
			/* is generated */false
		);

		return $type->getOrmPropertyType();
	}

	/**
	 * Creates a Time object that represents current application time
	 *
	 * @return Time
	 */
	static function now()
	{
		return new self (time());
	}

	/**
	 * @param mixed $input unix timestamp or textual representation of the time
	 */
	function __construct($input)
	{
		$this->setTimeString($input);
	}

	private function setTimeString($input)
	{
		// HH:MM:SS, HHMMSS
		if (strlen((string)$input) > strlen('000000') && TypeUtils::isInteger($input)) {
			// unix timestamp
			list ($this->hour, $this->minute, $this->second) = explode(':', date('H:i:s', $input));
		}
		else {
			if (preg_match('/[^\d]/', $input)) {
				$chunks = preg_split('/[^\d]+/', $input);
			}
			else {
				Assert::notImplemented('"HHMMSS" syntax not implemented in the Time parser');
			}

			$setters = array('hour', 'minute', 'second');

			foreach ($chunks as $k => $v)
			{
				$this->{'set'.$setters[$k]}($v);
			}
		}
	}

	/**
	 * Gets the hours of the Time object
	 *
	 * @return int
	 */
	function getHour()
	{
		return (int) $this->hour;
	}

	/**
	 * Sets the hours of the Time object
	 *
	 * @param int $hour hours to set
	 *
	 * @return Time itself
	 */
	function setHour($hour)
	{
		$hour = (int) $hour;

		if ((0 > $hour) || ($hour > 23)) {
			throw new ArgumentException('hour');
		}

		$this->hour = sprintf('%02d', $hour);

		return $this;
	}

	/**
	 * Gets the minutes of the Time object
	 *
	 * @return int
	 */
	function getMinute()
	{
		return (int) $this->minute;
	}

	/**
	 * Sets the minutes of the Time objet
	 *
	 * @param int $minute minutes to set
	 *
	 * @return Time itself
	 */
	function setMinute($minute)
	{
		$minute = (int) $minute;

		if ((0 > $minute) || ($minute > 59)) {
			throw new ArgumentException('minute');
		}

		$this->minute = sprintf('%02d', $minute);

		return $this;
	}

	/**
	 * Gets the seconds of the Time object
	 *
	 * @return int
	 */
	function getSecond()
	{
		return (int) $this->second;
	}

	/**
	 * Sets the seconds of the time object
	 *
	 * @param int $second seconds to set
	 *
	 * @return Time
	 */
	function setSecond($second)
	{
		$second = (int) $second;

		if ((0 > $second) || ($second > 59)) {
			throw new ArgumentException('second');
		}

		$this->second = sprintf('%02d', $second);

		return $this;
	}

	/**
	 * @return string
	 */
	function toHourMinuteTimeString($delimiter = ':')
	{
		return join($delimiter, array($this->hour, $this->minute));
	}

	/**
	 * @return string
	 */
	function toFormattedString($delimiter = ':')
	{
		return join($delimiter, array($this->hour, $this->minute, $this->second));
	}

	function __toString()
	{
		return $this->toFormattedString();
	}

	function getValue()
	{
		return $this->toFormattedString();
	}

	/**
	 * Represents the Time object in a whole number of minutes
	 *
	 * @return int
	 */
	function toMinutes()
	{
		return
			($this->hour * 60)
			+ $this->minute
			+ round($this->second / 100, 0);
	}

	/**
	 * Represents the Time object in a whole number of seconds
	 *
	 * @return int
	 */
	function toSeconds()
	{
		return
			($this->hour * 3600)
			+ ($this->minute * 60)
			+ $this->second;
	}
}

?>