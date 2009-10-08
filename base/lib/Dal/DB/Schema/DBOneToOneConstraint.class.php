<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright notice
 *
 ************************************************************************************************/

/**
 * Same as DBForeignKeyConstraint but references the primary key only
 * @ingroup DB
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
			. SqlFieldList::create(array_keys($this->columns))->toDialectString($dialect)
			. ')'
			. ' REFERENCES ' . $dialect->quoteIdentifier($this->referencedTable->getName())
			. ' ON DELETE ' .$this->associationBreakAction->toDialectString($dialect)
			. ' ON UPDATE ' . AssociationBreakAction::cascade()->toDialectString($dialect);
	}
}

?>