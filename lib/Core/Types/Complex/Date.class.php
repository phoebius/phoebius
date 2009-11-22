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
 * Represents a date
 *
 * @see Timestamp for date+time wrapper
 *
 * @ingroup Core_Types_Complex
 */
class Date implements IBoxable, IOrmPropertyAssignable
{
	/**
	 * unix timestamp representation
	 *
	 * @var int
	 */
	protected $int;

	/**
	 * @var int
	 */
	private $year;

	/**
	 * @var int
	 */
	private $month;

	/**
	 * @var int
	 */
	private $day;

	/**
	 * Static constructor of a Date object
	 *
	 * @param int $input unix timestamp or textual representation of the date
	 *
	 * @return Date
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
			DBType::DATE,
			/* is nullable */$multiplicity->isNullable(),
			/* size */null,
			/* precision */null,
			/* scale */null,
			/* is generated */false
		);

		return $type->getOrmPropertyType();
	}

	/**
	 * Creates a Date object that represents current application date
	 *
	 * Alias for Date::now()
	 *
	 * @return Date
	 */
	static function today()
	{
		return new self(time());
	}

	/**
	 * Creates a Date object that represents current application date
	 *
	 * @return Date
	 */
	static function now()
	{
		return new self(time());
	}

	/**
	 * Gets the number of days between two dates. Result can be negative if left > right
	 *
	 * @return int
	 */
	static function dayDifference(Date $left, Date $right)
	{
		return (
			gregoriantojd($right->getMonth(), $right->getDay(), $right->getYear())
			- gregoriantojd($left->getMonth(), $left->getDay(), $left->getYear())
		);
	}

	/**
	 * Compares to dates.
	 *
	 * Returns:
	 * - 0 if dates are equal
	 * - 1 if left Date > right Date
	 * - -1 if left Date < right Date
	 *
	 * @return int 0 if dates are eq; "1" if left Date > right Date; "-1" if left Date < right Date
	 */
	static function compare(Date $left, Date $right)
	{
		if ($left->equals($right)) {
			return CompareResult::EQUALS;
		}
		else {
			return (
				$left->int > $right->int
					? CompareResult::GREATER_THAN
					: CompareResult::LESS_THAN
			);
		}
	}

	/**
	 * @param mixed $input unix timestamp or textual representation of the date
	 */
	function __construct($input)
	{
		// not a unix timestamp
		if (!is_int($input) && !is_numeric($input)) {
			$input = strtotime($input);
		}

		$this->import($input);
	}

	/**
	 * Gets the number of days between current Date objcet and the specified Date.
	 *
	 * If Date is not specified, current Date is taken (see Date::now()).
	 *
	 * Result can be negative if current Date object > the specified Date.
	 *
	 * @return int
	 */
	function getPassedDays(Date $to = null)
	{
		return self::dayDifference(
			$this,
			$to
				? $to
				: Timestamp::now()
		);
	}

	/**
	 * Gets the Date representing the first day of the current week.
	 *
	 * Current week is the week which covers the current Date object.
	 *
	 * By default, the first day of week is presented by WeekDay::monday(). but can be set
	 * explicitly.
	 *
	 * @param WeekDay $weekStart the first day of week; WeekDay::monday() is default
	 *
	 * @return Date
	 */
	function getFirstDayOfWeek(WeekDay $weekStart = null)
	{
		if (!$weekStart) {
			$weekStart = WeekDay::monday();
		}

		return $this->spawn(
			'-' . ((7 + $this->getWeekDay() - $weekStart->getValue()) % 7).' days'
		);
	}

	/**
	 * Gets the Date representing the last day of the current week.
	 *
	 * Current week is the week which covers the current Date object.
	 *
	 * By default, the first day of week is presented by WeekDay::monday(). but can be set
	 * explicitly.
	 *
	 * @param WeekDay $weekStart the first day of week; WeekDay::monday() is default
	 *
	 * @return Date
	 */
	function getLastDayOfWeek(WeekDay $weekStart = null)
	{
		if (!$weekStart) {
			$weekStart = WeekDay::monday();
		}

		return $this->spawn(
			'+' . ((13 - $this->getWeekDay() + $weekStart->getValue()) % 7).' days'
		);
	}

	/**
	 * Gets the number of days in a current month.
	 *
	 * @return int
	 */
	function getDaysCountInMonth()
	{
		return (int) date('t', $this->int);
	}

	/**
	 * Determines whether the dates are equal or not.
	 *
	 * @return boolean
	 */
	function equals(Date $date)
	{
		return (
			   $this->year == $date->year
			&& $this->month == $date->month
			&& $this->day == $date->day
		);
	}

	/**
	 * Gets the unix timestamp representing the beginning of the current day.
	 *
	 * @return int
	 */
	function getDayStartStamp()
	{
		return
			mktime(
				0, 0, 0,
				$this->month,
				$this->day,
				$this->year
			);
	}

	/**
	 * Gets the unix timestamp representing the end of the current day
	 *
	 * @return int
	 */
	function getDayEndStamp()
	{
		return
			mktime(
				23, 59, 59,
				$this->month,
				$this->day,
				$this->year
			);
	}

	/**
	 * Modifies the current day with even any english textual representation.
	 *
	 * Modification is applied relative to the current Date.
	 *
	 * @see strtotime() reference to learn more about allowed modification
	 *
	 * @param string $modification textual representaion of modification to be applied to the Date
	 *
	 * @throws ArgumentException if modification faield
	 *
	 * @return Date itself
	 */
	function modify($string)
	{
		$time = strtotime($string, $this->int);

		if (false === $time) {
			throw new ArgumentException('string', 'wrong modification');
		}

		$this->int = $time;
		$this->import($this->int);

		return $this;
	}

	/**
	 * Gets the Date's year
	 *
	 * @return int
	 */
	function getYear()
	{
		return $this->year;
	}

	/**
	 * Gets the Date's month number
	 *
	 * @return int
	 */
	function getMonth()
	{
		return $this->month;
	}

	/**
	 * Gets the Date's day of the month
	 *
	 * @return int
	 */
	function getDay()
	{
		return $this->day;
	}

	/**
	 * Gets the Date's week number
	 *
	 * @return int
	 */
	function getWeek()
	{
		return date('W', $this->int);
	}

	/**
	 * Gets the WeekDay represented by the current date
	 *
	 * @return WeekDay
	 */
	function getWeekDay()
	{
		return new WeekDay(strftime('%w', $this->int));
	}

	/**
	 * Gets the unix representation of the Date
	 *
	 * @return int
	 */
	function getStamp()
	{
		return $this->int;
	}

	function getValue()
	{
		return $this->toFormattedString();
	}

	/**
	 * Gets the textual representation of the current Date.
	 *
	 * Object transformation codes are taken from PHP's date() codes.
	 *
	 * @see date() manual
	 *
	 * @return string
	 */
	function toFormattedString($format = 'Y/m/d')
	{
		return date($format, $this->int);
	}

	function __toString()
	{
		return $this->toFormattedString();
	}

	/**
	 * Imports date represented as unix timestamp value filling in dependant helper fields
	 *
	 * @throws TypeCastException if unix timestamp is wrong
	 * @return void
	 */
	protected function import($int)
	{
		if ($int) {
			list($this->year, $this->month, $this->day) = explode('.', date('Y.m.d', $int));

			if (checkdate($this->month, $this->day, $this->year)) {
				$this->int = $this->getDayStartStamp();

				return;
			}
		}

		throw new TypeCastException(__CLASS__, 'specified string is not a valid date');
	}

	/**
	 * Clones the current Date and optionally modifies the resulting Date.
	 *
	 * @param string $modification modification to be applied to the resulting object
	 *
	 * @see Date::modify()
	 *
	 * @return Date
	 */
	function spawn($modification = null)
	{
		// avoid useless value checks int ctor
		$child = clone $this;

		if ($modification) {
			return $child->modify($modification);
		}

		return $child;
	}
}

?>