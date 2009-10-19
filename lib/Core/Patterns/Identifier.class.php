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
 * Basic implementation of an entity, which has a special "feature" - a possibility to be
 * identified
 * @ingroup Core_Patterns
 */
class Identifier implements IIdentifiable
{
	/**
	 * An identifer of an entity
	 * @param scalar
	 */
	protected $id;

	/**
	 * Creates an instance of {@link Identifier}
	 * @return Identifier
	 */
	static function create()
	{
		return new self;
	}

	/**
	 * Gets the identifier of an entity
	 * @return integer
	 */
	function getId()
	{
		return $this->id;
	}

	/**
	 * Sets the identifier of an entity
	 * @param $id scalar
	 * @return Identifier an object itself
	 */
	function setId($id)
	{
		Assert::isScalar($id);

		$this->id = $id;

		return $this;
	}
}

?>