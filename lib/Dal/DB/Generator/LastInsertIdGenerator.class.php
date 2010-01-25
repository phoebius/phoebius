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
 * Auto-increment based generator. Retrieves the new primary key by fetching the last insert ID
 * the database has generated for a newly inserted tuple.
 *
 * @see mysql_last_insert_id()
 *
 * @ingroup Dal_DB_Geenrator
 */
final class LastInsertIdGenerator implements IIDGenerator
{
	/**
	 * @var DB
	 */
	private $db;

	/**
	 * @param DB $db actual db
	 */
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