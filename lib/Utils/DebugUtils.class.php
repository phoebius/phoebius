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