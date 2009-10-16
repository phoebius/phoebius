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
 * @ingroup Cipher
 */
interface ICipherer
{
	/**
	 * @param string $key
	 * @return ICipherer an object itself
	 */
	function setKey($key);

	/**
	 * @return string
	 */
	function getKey();

	/**
	 * @param string $data
	 * @return string
	 */
	function encrypt($data);

	/**
	 * @param string $cypher
	 * @return string
	 */
	function decrypt($cypher);
}

?>