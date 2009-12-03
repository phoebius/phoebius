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
 * Identifier property type generator invoker (to use for generated ORM-related entity properties)
 *
 * @ingroup Core_Types
 */
interface IOrmEntityIdGenerator
{
	/**
	 * Obtaines the new ID generator
	 *
	 * @return IIDGenerator
	 */
	function getIdGenerator(IdentifiableOrmEntity $entity);
}

?>