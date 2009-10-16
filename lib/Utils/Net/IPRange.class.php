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
 * @ingroup Net
 */
final class IPRange
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
	 * @return IPRange
	 */
	static function create(IP $ip1, IP $ip2)
	{
		return new self($ip1, $ip2);
	}

	function __construct(IP $ip1, IP $ip2)
	{
		if ($ip1->getLongIp() > $ip2->getLongIp()) {
			$t = $ip1;
			$ip1 = $ip2;
			$ip2 = $t;
			unset($t);
		}

		$this->findRange($ip1, $ip2);
	}

	/**
	 * @return IP
	 */
	function getRangeStart()
	{
		return $this->from;
	}

	/**
	 * @return IP
	 */
	function getRangeEnd()
	{
		return $this->to;
	}

	/**
	 * @return boolean
	 */
	function contains(IP $ip)
	{
		$mask =
			($this->to->equalTo($this->from)
				? $this->to->getLongIp()
				: 0xffffffff << (32 - $this->to->getLongIp()));
		return
			($ip->getLongIp() & $mask)
			== ($this->from->getLongIp() & $mask);
	}

	/**
	 * @return string
	 */
	function getRange()
	{
		return $this->from->toString() . '/' . $this->to->toString();
	}

	/**
	 * @return string
	 */
	function toString()
	{
		return $this->getRange();
	}

	private function findRange(IP $ip1, IP $ip2)
	{
		list ($a, $b, $c, $d) = explode('.', $ip1->toString());
		list ($e, $f, $g, $h) = explode('.', $ip2->toString());

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