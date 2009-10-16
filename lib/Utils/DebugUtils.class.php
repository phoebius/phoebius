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
 * Debugging utilities
 * @ingroup Utils
 */
final class DebugUtils extends StaticClass
{
	/**
	 * Same as PHP's sprintf, but supports non-scalar arguments, that are expanded in print_r
	 * manner
	 * @param string $string the string to be processed
	 * @param mixed ... any arguments
	 */
	static function sprintf($string)
	{
		if (func_num_args() > 1) {
			$params = func_get_args();
			foreach ($params as &$param) {
				if (!is_scalar($param)) {
					$param = print_r($param, true);
				}
			}

			$string = call_user_func_array('sprintf', $params);
		}

		return $string;
	}
}

?>