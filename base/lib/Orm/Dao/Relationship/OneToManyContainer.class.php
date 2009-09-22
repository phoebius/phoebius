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
 * @ingroup RelationshipDao
 */
abstract class OneToManyContainer extends Container
{
	function __construct(OrmEntity $parent, OrmMap $children, $partialFetch = false)
	{
		parent::__construct($parent, $children, $partialFetch);

		$worker =
			$partialFetch
				? 'OneToManyPartialWorker'
				: 'OneToManyFullWorker';

		$this->setWorker(new $worker($parent, $children, $this->getFKFieldName()));
	}

	/**
	 * Overridden. Now uses silly algorithm of searching the column
	 * @return string
	 */
	protected function getFKFieldName()
	{
		foreach (
				$this->childrenMap->getLogicalSchema()->getPropertySet()
				as $propertyPrefix => $property
		) {
			if ($property instanceof AssociationPropertyType) {
				if ($property->getContainerMap() === $this->parent->map()) {
					$fields = $this->childrenMap->getPropertySqlFields($propertyPrefix);
					return reset($fields);
				}
			}
		}

		Assert::isUnreachable();
	}
}

?>