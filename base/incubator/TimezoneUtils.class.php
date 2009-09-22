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
 * Helper utilities for timezones
 * @ingroup Utils
 */
final class TimezoneUtils extends StaticClass
{
	static function getOffsetList()
	{
		return self::$timezones;
	}

	static function offset2timezone($offset)
	{
		list($offset,$isdst) = explode(",",$offset);
		list($hOffset,$mOffset) = explode(':',$offset);
		$gmtOffset = $hOffset * 3600;
		$gmtOffset += $mOffset * 60; // add skipped minutes
		$t = new DateTime("now",new DateTimeZone('Europe/London'));
		if (!$t->format("I") && $isdst)
		{
			$isdst = false;
		}
		$timezone = timezone_name_from_abbr(null,$gmtOffset,$isdst);
		return $timezone;
	}

	static function timezone2offset($timezone_identifier,$include_dst = true)
	{
		$data = self::getTimezoneData($timezone_identifier);
		$m = $data['offset'] / 60;
		$h = floor($m / 60);
		$m = $m % 60;
		if ($h || $m)
		{
			$include_plus = true;
		}
		else
		{
			$include_plus = false;
		}
		$h = str_pad($h,2,'0',STR_PAD_LEFT);
		$m = str_pad($m,2,'0',STR_PAD_LEFT);
		if ($include_plus)
		{
			$h = '+'.$h;
		}
		$isdst = $data['dst'] ? "1" : "0";
		$result = sprintf($include_dst ? "%s:%s,%s" : "%s:%s",$h,$m,$isdst);
		return $result;
	}

	private static function getTimezoneData($timezone_identifier)
	{
		// I'm very shame of that code but now i cannot create something better due
		// php hasn't a smart API (even after they have
		// rewrote datetime functions).
		// Here we should find the offset  for the provided timezone and determine
		// whether the timezone has the DST offset ever.
		// We cannot use date("I") due it depends on current time (if the current time
		// is in winter, we 'll skip the zone)
		// so the best safe (but not fast) practice is to scan all tz transitions
		$timezone = new DateTimeZone($timezone_identifier);
		$current_date = new DateTime("now",$timezone);
		$current_ts = strtotime($current_date->format(DATE_W3C));
		$data = array (
			"offset" => 0,
			"dst" => false
		);

		$transitions = $timezone->getTransitions();
		foreach($transitions as &$transition)
		{
			$tt = new DateTime($transition['time'], $timezone);
			$ts = strtotime($tt->format(DATE_W3C));
			if ($ts < $current_ts)
			{
				continue;
			}

			if (!$transition['isdst'])
			{
				$transition = next($transitions);
			}
			$data['dst'] = $transition['isdst'];
			$data['offset'] = self::makeOffset($transition);

			break;
		}
		return $data;
	}

	private static function makeOffset($transition)
	{
		$offset = $transition['offset'];
		if ($transition['isdst'])
		{
			$offset -= 3600;
		}
		return $offset;
	}

	static function getOffsetListWithDst()
	{
		return self::$dstTimezones;
	}

	private static $timezones = array
	(
		"-12:00",
		"-11:00",
		"-10:00",
		"-09:00",
		"-08:00",
		"-07:00",
		"-07:00",
		"-06:00",
		"-06:00",
		"-05:00",
		"-05:00",
		"-04:00",
		"-04:00",
		"-03:30",
		"-03:00",
		"-03:00",
		"-02:00",
		"-01:00",
		"-01:00",
		"00:00",
		"00:00",
		"+01:00",
		"+01:00",
		"+02:00",
		"+02:00",
		"+03:00",
		"+03:00",
		"+03:30",
		"+04:00",
		"+04:00",
		"+04:30",
		"+05:00",
		"+05:00",
		"+05:30",
		"+05:45",
		"+06:00",
		"+06:00",
		"+06:30",
		"+07:00",
		"+07:00",
		"+08:00",
		"+08:00",
		"+09:00",
		"+09:00",
		"+09:30",
		"+09:30",
		"+10:00",
		"+10:00",
		"+11:00",
		"+12:00",
		"+12:00",
		"+13:00"
	);

	private static $dstTimezones = array
	(
		"-12:00,0",
		"-11:00,0",
		"-10:00,0",
		"-09:00,1",
		"-08:00,1",
		"-07:00,0",
		"-07:00,1",
		"-06:00,0",
		"-06:00,1",
		"-05:00,0",
		"-05:00,1",
		"-04:00,1",
		"-04:00,0",
		"-03:30,1",
		"-03:00,1",
		"-03:00,0",
		"-02:00,1",
		"-01:00,1",
		"-01:00,0",
		"00:00,0",
		"00:00,1",
		"+01:00,1",
		"+01:00,0",
		"+02:00,1",
		"+02:00,0",
		"+03:00,1",
		"+03:00,0",
		"+03:30,0",
		"+04:00,0",
		"+04:00,1",
		"+04:30,0",
		"+05:00,1",
		"+05:00,0",
		"+05:30,0",
		"+05:45,0",
		"+06:00,0",
		"+06:00,1",
		"+06:30,0",
		"+07:00,1",
		"+07:00,0",
		"+08:00,0",
		"+08:00,1",
		"+09:00,1",
		"+09:00,0",
		"+09:30,0",
		"+09:30,1",
		"+10:00,0",
		"+10:00,1",
		"+11:00,0",
		"+12:00,1",
		"+12:00,0",
		"+13:00,0"
	);
}
?>