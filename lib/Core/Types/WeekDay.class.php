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
 * strftime-compatible enumeration
 * @ingroup CoreTypes
 */
final class WeekDay extends Enumeration
{
	const MONDAY = 1;
	const TUESDAY = 2;
	const WEDNESDAY = 3;
	const THURSDAY = 4;
	const FRIDAY = 5;
	const SATURDAY = 6;
	const SUNDAY = 0;

	/**
	 * @return WeekDay
	 */
	static function create($id)
	{
		return new self($id);
	}

	/**
	 * @return WeekDay
	 */
	static function monday()
	{
		return new self(self::MONDAY);
	}

	/**
	 * @return WeekDay
	 */
	static function tuesday()
	{
		return new self(self::TUESDAY);
	}

	/**
	 * @return WeekDay
	 */
	static function wednesday()
	{
		return new self(self::WEDNESDAY);
	}

	/**
	 * @return WeekDay
	 */
	static function thursday()
	{
		return new self(self::THURSDAY);
	}

	/**
	 * @return WeekDay
	 */
	static function friday()
	{
		return new self(self::FRIDAY);
	}

	/**
	 * @return WeekDay
	 */
	static function saturday()
	{
		return new self(self::SATURDAY);
	}

	/**
	 * @return WeekDay
	 */
	static function sunday()
	{
		return new self(self::SUNDAY);
	}

	/**
	 * @param array of {@link WeekDay} constants
	 * @return boolean
	 */
	function isWeekEnd(
			array $weekendDays = array(
				self::SATURDAY,
				self::SUNDAY
			)
		)
	{
		foreach ($weekendDays as $weekEndDay) {
			if ($this->isEqual($weekEndDay)) {
				return true;
			}
		}

		return false;
	}
}

?>