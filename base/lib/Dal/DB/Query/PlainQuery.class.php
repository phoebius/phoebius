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
 * @ingroup PlainQuery
 */
class PlainQuery implements ISqlQuery
{
	/**
	 * @var string
	 */
	private $query;

	/**
	 * @var array of {@link ISqlCastable}
	 */
	private $placeholderValues = array();

	/**
	 * @param string $query
	 * @return PlainQuery
	 */
	static function create($query, array $placeholderValues = array())
	{
		return new self ($query, $placeholderValues);
	}

	/**
	 * @param string $query
	 */
	function __construct($query, array $placeholderValues = array())
	{
		$this->setQuery($query);
		$this->setPlaceholderValues($placeholderValues);
	}

	/**
	 * @return string
	 */
	function getQueryAsText()
	{
		return $this->query;
	}

	/**
	 * @param string $query
	 * @return PlainQuery
	 */
	function setQuery($query)
	{
		Assert::isScalar($query);

		$this->query = $query;

		return $this;
	}

	/**
	 * @return PlainQuery
	 */
	function setPlaceholderValues(array $placeholderValues)
	{
		$this->placeholderValues = array();

		foreach ($placeholderValues as $placeholderValue) {
			$this->addPlaceholderValue($placeholderValue);
		}

		return $this;
	}

	/**
	 * @return PlainQuery
	 */
	function addPlaceholderValue(ISqlCastable $placeholderValue)
	{
		$this->placeholderValues[] = $placeholderValue;

		return $this;
	}

	/**
	 * @return PlainQuery
	 */
	function dropPlaceholderValues()
	{
		$this->placeholderValues = array();

		return $this;
	}

	/**
	 * @see ISqlCastable::toDialectString()
	 *
	 * @param IDialect $dialect
	 * @return string
	 */
	function toDialectString(IDialect $dialect)
	{
		if (empty($this->placeholderValues)) {
			return $this->query;
		}
		else {
			$args = array(
				$this->query
			);

			foreach ($this->placeholderValues as $placeholderValue) {
				$args[] = $placeholderValue->toDialectString($dialect);
			}

			return call_user_func_array('sprintf', $args);
		}
	}

	/**
	 * @see ISqlQuery::getCastedParameters()
	 *
	 * @param IDialect $dialect
	 * @return array
	 */
	function getCastedParameters(IDialect $dialect)
	{
		return array ();
	}
}

?>