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
	 * entity -> tuple
	 * @return array
	 */
	function disassemble(OrmEntity $entity);

	/**
	 * Array->entity mapping (aka entity assembler). Creates a new entity and performs 1:1 mapping
	 * by filling the values specified by the array
	 * @return OrmEntity
	 */
	function assemble(OrmEntity $entity, array $tuple, FetchStrategy $fetchStrategy);

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