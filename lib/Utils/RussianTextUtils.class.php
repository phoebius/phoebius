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
 * Helper utilities for russian texts
 * @ingroup Utils
 */
class RussianTextUtils extends StaticClass
{
	/**
	 * Choose russion word declension based on numeric.
	 * Example for $expressions: array("ответ", "ответа", "ответов")
	 * @return string
	 */
	static function getDeclension($number, array $expressions)
	{
		Assert::isNumeric($number);

		if (count($expressions) < 3) {
			$expressions[2] = $expressions[1];
		}

		$count = $number % 100;
		if ($count >= 5 && $count <= 20) {
			$result = $expressions[2];
		}
		else {
			$count = $count % 10;
			if ( $count == 1 ) {
				$result = $expressions[0];
			}
			elseif ( $count >= 2 && $count <= 4 ) {
				$result = $expressions[1];
			}
			else {
				$result = $expressions[2];
			}
		}
		return $result;
	}
}

?>