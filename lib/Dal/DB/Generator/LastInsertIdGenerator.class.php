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

final class LastInsertIdGenerator implements IIDGenerator
{
	/**
	 * @var DB
	 */
	private $db;

	function __construct(DB $db)
	{
		Assert::isTrue(
			method_exists($db, 'getLastInsertId'),
			'DB should provide getLastInsertId() method to be used within %s',
			__CLASS__
		);

		$this->db = $db;
	}

	function getType()
	{
		return new IDGeneratorType(IDGeneratorType::POST);
	}

	function generate(IdentifiableOrmEntity $entity)
	{
		$id = $this->db->getLastInsertId();

		return $id;
	}
}

?>