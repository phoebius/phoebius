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
 * Represents a range of two IP adresses
 *
 * @ingroup Utils_Net
 */
final class IPRange implements IStringCastable
{
	/**
	 * @var IP
	 */
	private $from;

	/**
	 * @var IP
	 */
	private $to;

	/**
	 * @param IP $ip1
	 * @param IP $ip2
	 */
	function __construct(IP $ip1, IP $ip2)
	{
		if ($ip1->toInt() > $ip2->toInt()) {
			$t = $ip1;
			$ip1 = $ip2;
			$ip2 = $t;
			unset($t);
		}

		$this->findRange($ip1, $ip2);
	}

	/**
	 * Gets the head of the range
	 *
	 * @return IP
	 */
	function getRangeStart()
	{
		return $this->from;
	}

	/**
	 * Gets the tail of the range
	 *
	 * @return IP
	 */
	function getRangeEnd()
	{
		return $this->to;
	}

	/**
	 * Determines whether the specified IP is inside the range
	 *
	 * @return boolean
	 */
	function contains(IP $ip)
	{
		$mask =
			($this->to->equals($this->from)
				? $this->to->toInt()
				: 0xffffffff << (32 - $this->to->toInt()));
		return
			($ip->toInt() & $mask)
			== ($this->from->toInt() & $mask);
	}

	/**
	 * Gets the string representation of the range
	 *
	 * @return string
	 */
	function toString()
	{
		return $this->from . '/' . $this->to;
	}

	function __toString()
	{
		return $this->getRange();
	}

	private function findRange(IP $ip1, IP $ip2)
	{
		list ($a, $b, $c, $d) = explode('.', (string) $ip1);
		list ($e, $f, $g, $h) = explode('.', (string) $ip2);

		if ($a !== $e) {
			$this->from = $ip1;
			$this->to   = $ip2;
		}
		else {
			if ($b !== $f) {
				$this->from = $ip1;
				$this->to   = new IP("$f.$g.$h");
			}
			else {
				if ($c !== $g) {
					$this->from = $ip1;
					$this->to   = new IP("$g.$h");
				}
				else {
					if ($d !== $h) {
						$this->from = $ip1;
						$this->to   = new IP("$h");
					}
					else {
						Assert::isUnreachable();
					} // $d === $h
				} // $c === $g
			} // $b === $f
		} // $a === $e
	}
}

?>