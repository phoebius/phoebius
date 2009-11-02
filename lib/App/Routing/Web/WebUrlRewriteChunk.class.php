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
 * @ingroup App_Routing_Web
 * @internal
 */
class WebUrlRewriteChunk
{
	const PARAMETER_PLACEHOLDER = ':';
	const DEFAULT_VALUE_DELIMITER = '|';
	const GREEDYNESS_OPERATOR = '*';

	/**
	 * @var string
	 */
	private $predefinedValues = array();

	/**
	 * @var string|null
	 */
	private $name = null;

	/**
	 * @var boolean
	 */
	private $isGreedy = false;

	/**
	 * @return WebUrlRewriteChunk
	 */
	static function create()
	{
		return new self;
	}

	/**
	 * @return WebUrlRewriteChunk
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
				-1 * (strlen(self::GREEDYNESS_OPERATOR))
			);
		}

		// parameter name
		$regex = '/' . self::PARAMETER_PLACEHOLDER . '([\w]+)$/';
		$m = array();
		if (preg_match($regex, $unparsedChunk, $m)) {
			$name = $m[1];
			$me->setName($name);

			$unparsedChunk = substr(
				$unparsedChunk,
				0,
				-1 * (strlen(self::PARAMETER_PLACEHOLDER.$name))
			);
		}

		// default value
		if (!empty($unparsedChunk)) {
			if (
					   $unparsedChunk{0} == '('
					&& $unparsedChunk{strlen($unparsedChunk) - 1} == ')'
			) {
				$unparsedChunk = substr($unparsedChunk, 1, -1);
				foreach (explode(self::DEFAULT_VALUE_DELIMITER, $unparsedChunk) as $defaultValue) {
					$me->addPredefinedValue($defaultValue);
				}
			}
			else {
				 $me->addPredefinedValue($unparsedChunk);
			}
		}
		else {
			if (!$me->getName()) {
				$me->addPredefinedValue('');
			}
		}

		return $me;
	}

	/**
	 * @return WebUrlRewriteChunk
	 */
	function setName($name = null)
	{
		Assert::isScalarOrNull($name);

		$this->name = $name;

		return $this;
	}

	/**
	 * @return WebUrlRewriteChunk
	 */
	function greedy()
	{
		$this->isGreedy = true;

		return $this;
	}

	/**
	 * @return WebUrlRewriteChunk
	 */
	function ungreedy()
	{
		$this->isGreedy = false;

		return $this;
	}

	/**
	 * @return boolean
	 */
	function isGreedy()
	{
		return $this->isGreedy;
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
	function getPredefinedValues()
	{
		return $this->predefinedValues;
	}

	/**
	 * @return integer
	 */
	function getPredefinedValuesCount()
	{
		return sizeof($this->predefinedValues);
	}

	/**
	 * @return WebUrlRewriteChunk
	 */
	function addPredefinedValue($value)
	{
		Assert::isScalar($value);

		$this->predefinedValues[] = $value;

		return $this;
	}

	/**
	 * @return boolean
	 */
	function isMatch($chunk)
	{
		try {
			$this->getMatchedValue($chunk);
			return true;
		}
		catch (ArgumentException $e) {
			return false;
		}
	}

	/**
	 * @throws ArgumentException
	 * @return string
	 */
	function getMatchedValue($chunk)
	{
		Assert::isScalar($chunk);

		$chunk = trim($chunk, '/');

		if (!empty($this->predefinedValues)) {
			foreach ($this->predefinedValues as $value) {
				if ($value == $chunk) {
					return $value;
				}
			}
		}
		else {
			return $chunk;
		}

		throw new ArgumentException('chunk', 'does not match');
	}
}

?>