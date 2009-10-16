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
 * @ingroup OrmExpression
 */
final class EntityProperty
{
	/**
	 * @var EntityQuery
	 */
	private $entityQuery;

	/**
	 * @var OrmProperty
	 */
	private $property;

	/**
	 * @return EntityExpression
	 */
	static function create(EntityQuery $entityQuery, OrmProperty $property)
	{
		return new self ($entityQuery, $property);
	}

	function __construct(EntityQuery $entityQuery, OrmProperty $property)
	{
		$this->entityQuery = $entityQuery;
		$this->property = $property;
	}

	/**
	 * @return EntityQuery
	 */
	function getEntityQuery()
	{
		return $this->entityQuery;
	}

	/**
	 * @return OrmProperty
	 */
	function getProperty()
	{
		Return $this->property;
	}

	/**
	 * @return array
	 */
	function getSqlColumns()
	{
		$yield = array();

		foreach ($this->property->getDBFields() as $key) {
			$yield[] = new SqlColumn(
				$key,
				$this->entityQuery->getAlias()
			);
		}

		return $yield;
	}
}

?>