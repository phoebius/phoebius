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
 * @ingroup App_Web_Routing_Rules
 * @internal
 */
class PathChunk
{
	const PARAMETER_PLACEHOLDER = ':';
	const DEFAULT_VALUE_DELIMITER = '|';
	const GREEDYNESS_OPERATOR = '*';

	/**
	 * @var string
	 */
	private $values = array();

	/**
	 * @var string|null
	 */
	private $name = null;

	/**
	 * @var boolean
	 */
	private $isGreedy = false;

	private $isLast = false;

	/**
	 * @return PathChunk
	 */
	static function import($unparsedChunk)
	{
		$me = new self;

		$unparsedChunk = trim($unparsedChunk, '/');

		// is greedy?
		$postfix = substr($unparsedChunk, -1 * strlen(self::GREEDYNESS_OPERATOR));
		if ($postfix == self::GREEDYNESS_OPERATOR) {
			$me->isGreedy = true;

			$unparsedChunk = substr(
				$unparsedChunk,
				0,
				-strlen(self::GREEDYNESS_OPERATOR)
			);
		}

		// parameter name
		$regex = '/' . self::PARAMETER_PLACEHOLDER . '([\w]+)$/';
		$m = array();
		if (preg_match($regex, $unparsedChunk, $m)) {
			$me->name = $m[1];

			$unparsedChunk = substr(
				$unparsedChunk,
				0,
				-strlen(self::PARAMETER_PLACEHOLDER . $me->name)
			);
		}

		// default value
		if (
				$me->name
				&& !empty($unparsedChunk)
				&& $unparsedChunk{0} == '('
				&& $unparsedChunk{strlen($unparsedChunk) - 1} == ')'
		) {
			$unparsedChunk = substr($unparsedChunk, 1, -1);
			foreach (explode(self::DEFAULT_VALUE_DELIMITER, $unparsedChunk) as $value) {
				$me->values[] = $value;
			}
		}
		else if (!$me->name || ($me->name && !empty($unparsedChunk))) {
			$me->values[] = $unparsedChunk;
		}

		return $me;
	}

	/**
	 * @return boolean
	 */
	function isGreedy()
	{
		return $this->name && $this->isLast && $this->isGreedy;
	}

	/**
	 * @return PathChunk
	 */
	function setLast()
	{
		$this->isLast = true;

		return $this;
	}

	/**
	 * @return boolean
	 */
	function isLast()
	{
		return $this->isLast;
	}

	/**
	 * @return string|null
	 */
	function getName()
	{
		return $this->name;
	}

	/**
	 * @return array
	 */
	function getValues()
	{
		return $this->values;
	}

	/**
	 * @return integer
	 */
	function getValueCount()
	{
		return sizeof($this->values);
	}
}

?>