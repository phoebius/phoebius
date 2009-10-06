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
 * @ingroup ComplexCoreTypes
 */
class Date implements IObjectMappable, IHandled, IBoxed
{
	/**
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
	 * @return Date
	 */
	static function create($value = null)
	{
		return new self($value);
	}

	/**
	 * @return Date
	 */
	static function cast($value)
	{
		try {
			return new self($value);
		}
		catch (ArgumentException $e) {
			throw new TypeCastException(new Type(__CLASS__), $value);
		}
	}

	/**
	 * @return OrmPropertyType
	 */
	static function getHandler(AssociationMultiplicity $multiplicity)
	{
		return new DatePropertyType(
			$multiplicity->is(AssociationMultiplicity::ZERO_OR_ONE)
		);
	}

	/**
	 * @return Date
	 */
	static function today()
	{
		return new self(time());
	}

	/**
	 * Alias for Date::today()
	 * @return Date
	 */
	static function now()
	{
		return new self(time());
	}

	/**
	 * Returns the number of days between two dates. Result can be negative if left > right
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
	 * 0 - if equals
	 * 1 - if left > right
	 * -1 - if left < right
	 * @return int
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
	 * @param $value int (unix timestamp) or string (string representation of a date)
	 */
	function __construct($value = null)
	{
		$this->setValue($value);
	}

	private function setValue($value = null)
	{
		if (!$value) {
			$value = time();
		}

		// not a unix timestamp
		if (!is_int($value) && !is_numeric($value)) {
			$value = strtotime($value);
		}

		$this->resolveAssocProperty($value);
	}

	/**
	 * Returns the number of days between two dates. Result can be negative if left > right
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
	 * @return int
	 */
	function getDaysCountInMonth()
	{
		return (int) date('t', $this->int);
	}

	/**
	 * @return boolean
	 */
	function equals(Date $date)
	{
		return (boolean) (
			   $this->year == $date->year
			&& $this->month == $date->month
			&& $this->day == $date->day
		);
	}

	/**
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
	 * @throws ArgumentException
	 * @return Date an object itself
	 */
	function modify($string)
	{
		$time = strtotime($string, $this->int);

		if (false === $time) {
			throw new ArgumentException('string', 'wrong modification');
		}

		$this->int = $time;
		$this->resolveAssocProperty($this->int);

		return $this;
	}

	/**
	 * @return int
	 */
	function getYear()
	{
		return $this->year;
	}

	/**
	 * @return int
	 */
	function getMonth()
	{
		return $this->month;
	}

	/**
	 * @return int
	 */
	function getDay()
	{
		return $this->day;
	}

	/**
	 * Week number
	 * @return int
	 */
	function getWeek()
	{
		return date('W', $this->int);
	}

	/**
	 * @return WeekDay
	 */
	function getWeekDay()
	{
		return new WeekDay(strftime('%w', $this->int));
	}

	/**
	 * @return int
	 */
	function getStamp()
	{
		return $this->int;
	}

	/**
	 * @return int
	 */
	function getValue()
	{
		return $this->getStamp();
	}

	/**
	 * @return int
	 */
	function toScalar()
	{
		return $this->getStamp();
	}

	/**
	 * @return int
	 */
	function getScalar()
	{
		return $this->getValue();
	}

	/**
	 * @return string
	 */
	function toFormattedString($format = 'd-m-Y')
	{
		return date($format, $this->int);
	}

	/**
	 * Overridden. Imports date represented as unix timestamp value filling in dependant helper
	 * properties
	 * @throws TypeCastException
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

		throw new ArgumentException('int', 'specified string is not valid date');
	}

	/**
	 * clone + modify
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