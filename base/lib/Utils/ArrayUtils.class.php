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
 * Utils for manipulating arrays
 * @ingroup Utils
 */
final class ArrayUtils extends StaticClass
{
	/**
	 * Snaps the element (not the key) off the specified array
	 * @param array $array
	 * @param mixed $element
	 * @return void
	 */
	static function snapOff(array &$array, $element)
	{
		$key = array_search($element, $array, true);
		if (false !== $key) {
			unset($array[$key]);
		}
	}

	/**
	 * Creates an array with values in range from $from to $to.
	 * @var integer the first limit of the range
	 * @var integer the last limit of the range
	 * @var boolean specifies whether to set the keys of a new array as the appropriate values
	 * @return array
	 */
	static function makeRange($from, $to, $keysAsValues = false)
	{
	    Assert::isBoolean($keysAsValues);

		$newArray = array();
		for($i = $from; $i <= $to; $i ++) {
			if ($keysAsValues) {
				$newArray[$i] = $i;
			}
			else {
				$newArray[] = $i;
			}
		}
		return $newArray;
	}

	/**
	 * Gets an md5 hash of an array
	 * @return string
	 */
	static function getMd5(array &$master)
	{
		return md5(serialize($master));
	}

	/**
	 * Gets the sha1 hash of an array
	 * @return string
	 */
	static function getSha1(array &$master)
	{
		return sha1(serialize($master));
	}

	/**
	 * Smart callback function for processing nested array, used by the magic_quotes_qpc switcher
	 * and so on.
	 * @return void
	 */
	static function iterateCallback(array &$incoming, array $processor_funcs = array())
	{
		foreach ($incoming as &$value) {
			if (is_array($value)) {
				self::iterateCallback($value, $processor_funcs);
			}
			else {
				foreach ($processor_funcs as $func_name) {
					$value = call_user_func_array($func_name, array(&$value));
				}
			}
		}
	}
}

?>