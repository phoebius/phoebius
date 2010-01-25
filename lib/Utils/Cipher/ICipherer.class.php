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
 * Contract for string cipherer
 *
 * @ingroup Utils_Cipher
 */
interface ICipherer
{
	/**
	 * Sets the key to be used when ciphering the string
	 *
	 * @param string $key
	 * @return ICipherer itself
	 */
	function setKey($key);

	/**
	 * Gets the key used in cipher, if set
	 * @return string
	 */
	function getKey();

	/**
	 * Ciphers the data using the key
	 * @param string $data
	 * @return string
	 */
	function encrypt($data);

	/**
	 * Decrypts the cypher using the key
	 * @param string $cypher
	 * @return string
	 */
	function decrypt($cypher);
}

?>