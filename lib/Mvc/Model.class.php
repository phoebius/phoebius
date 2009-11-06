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
 * @ingroup Mvc
 */
class Model extends Collection
{
	/**
	 * @return Model
	 */
	static function from(array $array)
	{
		$me = new self;
		$me->fill($array);

		return $me;
	}

	/**
	 * @return Model
	 */
	function spawn(array $copy = null)
	{
		if (null === $copy) {
			return clone $this;
		}
		else {
			$me = new self;
			foreach ($copy as $var) {
				$me[$var] = $this[$var];
			}

			return $me;
		}
	}
}

?>