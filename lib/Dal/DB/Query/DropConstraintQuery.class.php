<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright (c) 2011 Scand Ltd.
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
 * Represents a query for altering tables for dropping constraints
 *
 * @ingroup Dal_DB_Query
 */
final class DropConstraintQuery implements ISqlQuery
{
	/**
	 * @var DBConstraint
	 */
	private $constraint;

	function __construct(DBConstraint $constraint)
	{
		$this->constraint = $constraint;
	}

	function toDialectString(IDialect $dialect)
	{
		$table = $dialect->quoteIdentifier($this->constraint->getTable()->getName());
		$ct = $dialect->quoteIdentifier($this->constraint->getName());
		
		if ($dialect->getDBDriver()->is(DBDriver::MYSQL)) {
			if ($this->constraint instanceof DBUniqueConstraint) {
				return 
					'ALTER TABLE ' . $table
					. ' DROP INDEX ' . $ct;
			}
			else if ($this->constraint instanceof DBPrimaryKeyConstraint) {
				return 
					'ALTER TABLE ' . $table
					. ' DROP PRIMARY KEY';
			}
			else if (
					$this->constraint instanceof DBOneToOneConstraint
					|| $this->constraint instanceof DBForeignKeyConstraint
				) {
				return
					'ALTER TABLE ' . $table
					. ' DROP FOREIGN KEY ' . $ct;
			}
			
			Assert::isUnreachable(
				'Do not know how to remove constraint %s from MySQL',
				get_class($this->constraint)
			);
		}
		
		return
			'ALTER TABLE ' . $table
			. ' DROP CONSTRAINT ' . $ct
			. ' CASCADE;';
	}

	function getPlaceholderValues(IDialect $dialect)
	{
		return array();
	}
}

?>