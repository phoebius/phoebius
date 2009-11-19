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

class SequenceGenerator implements IIDGenerator
{
	/**
	 * @var DB
	 */
	private $db;

	/**
	 * @var ISqlSelectQuery
	 */
	private $query;

	function __construct(DB $db, ISqlSelectQuery $query)
	{
		$this->db = $db;
		$this->query = $query;
	}

	function getType()
	{
		return new IDGeneratorType(IDGeneratorType::PRE);
	}

	function generate(IdentifiableOrmEntity $entity)
	{
		$id = $this->db->getCell($this->query);

		return $id;
	}
}

?>