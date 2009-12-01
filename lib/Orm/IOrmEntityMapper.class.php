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
 * Contract for ORM-related entity workflow.
 *
 * @ingroup Orm
 */
interface IOrmEntityMapper
{
	/**
	 * entity->tuple mapper.
	 *
	 * Disassembles entity to the set of primitive values according the property types.
	 *
	 * @param OrmEntity $entity entity to disasseble
	 *
	 * @return array
	 */
	function disassemble(OrmEntity $entity);

	/**
	 * tuple->entity mapper.
	 *
	 * Fills the entity by mapping the set of primitive values to property objects according
	 * to their OrmPropertyType classes.
	 *
	 * @param OrmEntity $entity entity to fill
	 * @param array $tuple set of primitive values
	 * @param FetchStrategy $fetchStrategy current fetch strategy to use
	 *
	 * @return OrmEntity
	 */
	function assemble(OrmEntity $entity, array $tuple, FetchStrategy $fetchStrategy);

	/**
	 * Gets the optimized entity mapper
	 *
	 * @return IOrmEntityBatchMapper
	 */
	function getBatchMapper();
}

?>