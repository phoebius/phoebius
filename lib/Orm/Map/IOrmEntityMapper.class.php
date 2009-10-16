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
 * @ingroup OrmMap
 */
interface IOrmEntityMapper
{
	/**
	 * @return array
	 */
	function getProperties(OrmEntity $entity);

	/**
	 * @return OrmEntity
	 */
	function setProperties(OrmEntity $entity, array $properties);

	/**
	 * @return array
	 */
	function getRawValues(OrmEntity $entity);

	/**
	 * Array->entity mapping (aka entity assembler). Creates a new entity and performs 1:1 mapping
	 * by filling the values specified by the array
	 * @return OrmEntity
	 */
	function setRawValues(OrmEntity $entity, array $rawValues, FetchStrategy $fetchStrategy);

	/**
	 * @return integer batch fetching id
	 */
	function beginBatchFetchingMode();

	/**
	 * @return void
	 */
	function commitBatchFetchingMode($batchFetchingId);
}

?>