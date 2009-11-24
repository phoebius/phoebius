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
 * Same as DBForeignKeyConstraint but references the primary key only
 * @ingroup Dal_DB_Schema
 */
class DBOneToOneConstraint extends DBConstraint
{
	/**
	 * @var array
	 */
	private $columns;

	/**
	 * @var DBColumn
	 */
	private $referencedTable;

	/**
	 * @var AssociationBreakAction
	 */
	private $associationBreakAction;

	/**
	 * @param array $columns array of DBColumn
	 * @return DBOneToOneConstraint
	 */
	static function create(
			array $columns,
			DBTable $referencedTable,
			AssociationBreakAction $associationBreakAction
		)
	{
		return new self ($columns, $referencedTable, $associationBreakAction);
	}

	function __construct(
			array $columns,
			DBTable $referencedTable,
			AssociationBreakAction $associationBreakAction
		)
	{
		foreach ($referencedTable->getConstraints() as $constraint) {
			if ($constraint instanceof DBPrimaryKeyConstraint) {
				$pkColumns = $constraint->getColumns();

				if (sizeof($pkColumns) == sizeof($columns)) {
					foreach ($columns as $column) {
						$this->columns[$column->getName()] = $column;
					}

					$this->referencedTable = $referencedTable;
					$this->associationBreakAction = $associationBreakAction;

					return;
				}
			}
		}

		throw new ArgumentException('columns', 'columns does not match the primary key');
	}

	/**
	 * Returns the affected columns, if any
	 * @return array of {@link DBColumn}
	 */
	function getIndexedColumns()
	{
		return $this->columns;
	}

	/**
	 * @return string
	 */
	function toDialectString(IDialect $dialect)
	{
		return
			  'FOREIGN KEY ('
			. SqlFieldArray::create(array_keys($this->columns))->toDialectString($dialect)
			. ')'
			. ' REFERENCES ' . $dialect->quoteIdentifier($this->referencedTable->getName())
			. ' ON DELETE ' .$this->associationBreakAction->toDialectString($dialect)
			. ' ON UPDATE ' . AssociationBreakAction::cascade()->toDialectString($dialect);
	}
}

?>