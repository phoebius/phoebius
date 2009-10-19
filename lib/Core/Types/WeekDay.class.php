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
 * strftime-compatible enumeration
 * @ingroup Core_Types
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