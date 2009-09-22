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