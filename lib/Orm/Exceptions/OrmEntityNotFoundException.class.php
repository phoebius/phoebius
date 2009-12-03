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
 * Thrown when entity not found by the criteria
 *
 * @ingroup Orm_Exceptions
 */
class OrmEntityNotFoundException extends ObjectNotFoundException
{
	/**
	 * @var IQueryable
	 */
	private $entity;

	/**
	 * @param IQueryable $entity looked up entity
	 * @param string $message
	 */
	function __construct(IQueryable $entity, $message)
	{
		$this->entity = $entity;

		parent::__construct($message);
	}
}

?>