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
 * @ingroup Query
 */
class CreateTableQuery implements ISqlQuery
{
	/**
	 * @var DBTable
	 */
	private $table;

	/**
	 * @var array
	 */
	private $commaSeparatedQueryParts = array();

	/**
	 * @return CreateTableQuery
	 */
	static function create(DBTable $table)
	{
		return new self ($table);
	}

	function __construct(DBTable $table)
	{
		$this->table = $table;
	}

	/**
	 * Casts an object to the SQL dialect string
	 * @return string
	 */
	function toDialectString(IDialect $dialect)
	{
		$queryParts = array();
		$this->commaSeparatedQueryParts = array();

		$queryParts[] = 'CREATE TABLE ';
		$queryParts[] = $dialect->quoteIdentifier($this->table->getName());
		$queryParts[] = '(';

		$this->makeColumns($dialect);
		$this->makeConstraints($dialect);

		$queryParts[] = join(',', $this->commaSeparatedQueryParts);

		$queryParts[] = PHP_EOL;
		$queryParts[] = ');';

		return join('', $queryParts);
	}

	/**
	 * @return array
	 */
	function getCastedParameters(IDialect $dialect)
	{
		return array ();
	}

	/**
	 * @return void
	 */
	private function makeColumns(IDialect $dialect)
	{
		foreach ($this->table->getColumns() as $column) {
			$this->makeColumn($column, $dialect);
		}
	}

	/**
	 * @return void
	 */
	private function makeColumn(DBColumn $column, IDialect $dialect)
	{
		$queryParts = array(PHP_EOL, "\t");

		$queryParts[] = $dialect->quoteIdentifier($column->getName());
		$queryParts[] = ' ';
		$queryParts[] = $column->getType()->toDialectString($dialect);

		if (($defaultValue = $column->getDefaultValue())) {
			$queryParts[] = ' DEFAULT ';
			$queryParts[] = $defaultValue->toDialectString($dialect);
		}

		$this->commaSeparatedQueryParts[] = join('', $queryParts);
	}

	/**
	 * @return void
	 */
	private function makeConstraints(IDialect $dialect)
	{
		foreach ($this->table->getConstraints() as $constraint) {
			$queryParts = array(PHP_EOL, "\t");
			$queryParts[] = $constraint->toDialectString($dialect);
			$this->commaSeparatedQueryParts[] = join('', $queryParts);
		}
	}
}

?>