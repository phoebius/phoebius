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
 * @ingroup Orm_Map
 */
final class OrmMap implements IOrmEntityMapper
{
	/**
	 * @var array
	 */
	private $batchFetchingStorage = array();

	/**
	 * @var array of {@link FetchStrategy}
	 */
	private $batchFetchingStrategy = array();

	/**
	 * @var array
	 */
	private $batchFetchingEntities = array();

	/**
	 * @var integer
	 */
	private $batchFetchingLevel = 0;

	/**
	 * @var ILogicallySchematic
	 */
	private $logicalSchema;

	function __construct(ILogicallySchematic $logicalSchema)
	{
		$this->logicalSchema = $logicalSchema;
	}

	/**
	 * @return array
	 */
	function getProperties(OrmEntity $entity)
	{
		$yield = array();

		foreach ($this->logicalSchema->getProperties() as $property) {
			if (!$property->getVisibility()->isGettable()) {
				continue;
			}

			$getter = $property->getGetter();
			$yield[$property->getName()] = $entity->$getter();
		}

		return $yield;
	}

	/**
	 * @return OrmEntity
	 */
	function setProperties(OrmEntity $entity, array $properties)
	{
		foreach ($this->logicalSchema->getProperties() as $property) {
			if (!$property->getVisibility()->isSettable()) {
				continue;
			}

			$propertyName = $property->getName();

			if (isset($properties[$propertyName])) {
				$setter = $property->getSetter();
				$entity->$setter($properties[$propertyName]);
			}
		}

		return $entity;
	}

	/**
	 * @return array
	 */
	function getRawValues(OrmEntity $entity)
	{
		$rawValues = array();
		foreach ($this->logicalSchema->getProperties() as $property) {
			if (!$property->getVisibility()->isGettable()) {
				continue;
			}

			$getter = $property->getGetter();
			$value = $entity->$getter();

			if (is_null($value)) {
				if ($this->logicalSchema->getIdentifier() === $property) {
					continue;
				}

				if ($property->getType()->isNullable()) {
					continue;
				}
			}

			$rawValues[$property->getName()] = $property->getType()->disassemble($value);
		}

		return $rawValues;
	}

	/**
	 * @return OrmEntity
	 */
	function setRawValues(OrmEntity $entity, array $rawValues, FetchStrategy $fetchStrategy)
	{
		foreach ($this->logicalSchema->getProperties() as $property) {
			if (!$property->getVisibility()->isSettable()) {
				continue;
			}

			if (isset ($rawValues[$property->getName()])) {
				$rawValue = $rawValues[$property->getName()];

				Assert::isTrue(
					is_array($rawValue)
					&& array_keys($rawValue) === array_keys($property->getType()->getSqlTypes()),
					'wrong raw value %s for property %s',
					$rawValue,
					$property->getName()
				);

				// FIXME: batch fetching mode fails, see sheduling mechanism
				if (false && $this->batchFetchingLevel > 0) {
					$this->scheduleBatchFetch(
						$entity,
						$property,
						new DBValueArray($rawValue),
						$fetchStrategy
					);
				}
				else {
					$setter = $property->getSetter();
					$entity->$setter(
						$property->getType()->assemble(
							new DBValueArray($rawValue),
							$fetchStrategy
						)
					);
				}
			}
		}

		return $entity;
	}

	/**
	 * @return integer batch fetching id
	 */
	function beginBatchFetchingMode()
	{
		$id = ++$this->batchFetchingLevel;

		$this->batchFetchingStorage[$id] = array();
		$this->batchFetchingStrategy[$id] = array();
		$this->batchFetchingEntities[$id] = array();

		return $id;
	}

	/**
	 * @return void
	 */
	private function scheduleBatchFetch(
			OrmEntity $entity,
			OrmProperty $property,
			array $rawValue,
			FetchStrategy $fetchStrategy
		)
	{
		Assert::notImplemented();

		$name = $property->getName();

		// FIXME: save an order of added entity according to the rawValue
		// propertyType::makeValueSet() calls array_unique so we cannot resolve
		// the actual entity the resolved value should be setted to
		$this->batchFetchingEntities[$this->batchFetchingLevel][$name][] = $entity;
		$this->batchFetchingStorage[$this->batchFetchingLevel][$name][] = $rawValue;
		$this->batchFetchingStrategy[$this->batchFetchingLevel][$name] = $fetchStrategy;
	}

	/**
	 * @return void
	 */
	function commitBatchFetchingMode($batchFetchingId)
	{
		Assert::isTrue(
			isset($this->batchFetchingStorage[$batchFetchingId]),
			'unknown batch fetching id specified: %s',
			$batchFetchingId
		);

		foreach ($this->batchFetchingStorage[$batchFetchingId] as $name => $rawValueSet) {

			$property = $this->logicalSchema->getProperty($name);
			$setter = $property->getSetter();

			$fetchStrategy = $this->batchFetchingStrategy[$batchFetchingId][$name];
			$entities = $this->batchFetchingEntities[$batchFetchingId][$name];

			$valueSet = $property->getType()->makeValueSet($rawValueSet, $fetchStrategy);

			foreach ($entities as $idx => $entity) {
				$entity->$setter($valueSet[$idx]);
			}
		}

		unset($this->batchFetchingStorage[$this->batchFetchingLevel]);
		unset($this->batchFetchingStrategy[$this->batchFetchingLevel]);
		unset($this->batchFetchingEntities[$this->batchFetchingLevel]);

		--$this->batchFetchingLevel;
	}
}

?>