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
 * Represents a primary key value generator contract.
 *
 * @ingroup Dal_DB_Generator
 */
interface IIDGenerator
{
	/**
	 * Gets the type of the generator. Type of the generator encapsulates the calling logic
	 * of the generator.
	 *
	 * @return IDGeneratorType
	 */
	function getType();

	/**
	 * Gets the new primary key value
	 *
	 * @return mixed a new value itself
	 */
	function generate(IdentifiableOrmEntity $entity);
}

?>