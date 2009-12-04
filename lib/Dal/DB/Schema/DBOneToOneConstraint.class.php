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
 * Represents a foreign key constaint that refers to the primary key of another table.
 *
 * @ingroup Dal_DB_Schema
 */
class DBOneToOneConstraint extends DBConstraint
{
	/**
	 * @var SqlFieldArray
	 */
	private $fields;

	/**
	 * @var DBColumn
	 */
	private $referencedTable;

	/**
	 * @var AssociationBreakAction
	 */
	private $associationBreakAction;

	/**
	 * @param array $fields fields of referencing table that reference primary another table
	 * @param DBTable $referencedTable object that represents referenced table
	 * @param AssociationBreakAction $associationBreakAction action which is performed on reference break
	 */
	function __construct(
			array $fields,
			DBTable $referencedTable,
			AssociationBreakAction $associationBreakAction
		)
	{
		foreach ($referencedTable->getConstraints() as $constraint) {
			if ($constraint instanceof DBPrimaryKeyConstraint) {
				$pkFields = $constraint->getFields();

				Assert::isTrue(
					sizeof($pkFields) == sizeof($fields),
					'foreign key (%s) should have the same number of columns as %s`s table primary key (%s)',
					join(', ', $fields),
					$referencedTable->getName(),
					join(', ', $pkFields)
				);

				$this->fields = new SqlFieldArray($fields);

				$this->referencedTable = $referencedTable;
				$this->associationBreakAction = $associationBreakAction;

				return;
			}
		}

		Assert::isUnreachable(
			'referenced table `%s` MUST contain DBPrimaryKeyConstraint',
			$referencedTable->getName()
		);
	}

	function getIndexableFields()
	{
		return $this->fields;
	}

	function toDialectString(IDialect $dialect)
	{
		return
			  'FOREIGN KEY ('
			. $this->fields->toDialectString($dialect)
			. ')'
			. ' REFERENCES ' . $dialect->quoteIdentifier($this->referencedTable->getName())
			. ' ON DELETE ' .$this->associationBreakAction->toDialectString($dialect)
			. ' ON UPDATE ' . AssociationBreakAction::cascade()->toDialectString($dialect);
	}
}

?>