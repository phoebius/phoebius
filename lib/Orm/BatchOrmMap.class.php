<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright (c) 2009 Scand Ltd.
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
 * Represents a base implementation of ORM-related entity mapper
 *
 * @ingroup Orm
 */
final class BatchOrmMap extends OrmMap implements IOrmEntityBatchMapper
{
	/**
	 * @var ILogicallySchematic
	 */
	private $logicalSchema;

	private $strategy;
	private $objects = array();
	private $properties = array();

	/**
	 * @param ILogicallySchematic $logicalSchema all we need to know is logical structure of ORM entity
	 */
	function __construct(ILogicallySchematic $logicalSchema)
	{
		$this->logicalSchema = $logicalSchema;

		parent::__construct($this->logicalSchema);
	}

	function assemble(OrmEntity $entity, array $tuple, FetchStrategy $fetchStrategy)
	{
		$this->strategy = $fetchStrategy;
		$this->objects[] = $entity;

		foreach ($this->logicalSchema->getProperties() as $property) {
			if (!$property->getVisibility()->isSettable()) {
				continue;
			}

			$propertyTuple = $this->getPropertyTuple($property, $tuple);

			if (!isset($this->properties[$property->getName()])) {
				$this->properties[$property->getName()] = array();
			}

			$this->properties[$property->getName()][] = $propertyTuple;
		}

		return $entity;
	}

	function getBatchMapper()
	{
		return new self ($this->logicalSchema);
	}

	function finish()
	{
		foreach ($this->properties as $name => $tuples) {
			$property = $this->logicalSchema->getProperty($name);
			$setter = $property->getSetter();

			$assembled = $property->getType()->assebmleSet($tuples, $this->strategy);

			foreach ($assembled as $k => $value) {
				$this->objects[$k]->{$setter}($value);
			}

		}
	}
}

?>