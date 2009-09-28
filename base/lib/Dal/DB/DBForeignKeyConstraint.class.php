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
 * TODO: implement DBForeignKeyConstraint
 * Represents an extended version of {@link DBOneToOneConstraint} class, allowing references
 * to any set of columns, not only defined as Primary key.
 * Architectural problems:
 *  - API for fulfilling an object should not be obscure, providing simple interface and good fallover
 *    - addReference(DBColumn $column, DBColumn $referencedColumn)
 *          + pros: strictly-typed API
 *          - cons: cannot add multiple refs at once
 *    - addReferences(array())
 *         key is the name of the column, value is the name of the corresponding referenced column
 *          + pros: API is simple - we can add multiple refs at one method call
 *          - cons: ctor must accept both table and referencedTable to check column names (?)
 *      - addColumn()/addReferencedColumn()
 *  - set of referenced columns should be defined by {@link DBUniqueConstraint} (RDBMS requirement)
 * @ingroup DB
 */
abstract class DBForeignKeyConstraint extends DBConstraint
{
	// not implemented yet, thus marked as abstract
}

?>