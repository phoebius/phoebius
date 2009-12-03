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
 * Simple XOR cipher implementation
 *
 * @ingroup Utils_Cipher
 */
class XorCipherer implements ICipherer
{
	private $key = '';

	/**
	 * @param string $key key to use in ciphering
	 */
	function __construct($key)
	{
		$this->setKey($key);
	}

	function setKey($key)
	{
		Assert::isScalar($key);

		$this->key = $key;

		return $this;
	}

	function getKey()
	{
		return $this->key;
	}

	function encrypt($data)
	{
		$key = $this->key;
		$result = array ();
		for ($i = 0; $i < strlen($data); $i++) {
			$char = substr($data, $i, 1);
			$keychar = substr($key, ( $i % strlen($key) ) - 1, 1);
			$char = chr(ord($char) + ord($keychar));
			$result[] = $char;
		}
		$b64u = base64_encode(join('', $result));
		$b64u = strtr($b64u, '+/', '-_');
		$b64u = rtrim($b64u, '=');
		return $b64u;
	}

	function decrypt($cypher)
	{
		$result = array();
		$key = $this->key;
		$string = strtr($cypher, '-_', '+/');
		$string = base64_decode($string);
		for ($i = 0; $i < strlen($string); $i++) {
			$char = substr($string, $i, 1);
			$keychar = substr($key, ( $i % strlen($key) ) - 1, 1);
			$char = chr(ord($char) - ord($keychar));
			$result[] = $char;
		}
		return join('', $result);
	}
}

?>