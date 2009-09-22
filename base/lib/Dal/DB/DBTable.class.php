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
 * @ingroup DB
 */
class DBTable
{
	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var array of {@link DBColumn}
	 */
	private $columns = array();

	/**
	 * @var array of {@link DBConstraint}
	 */
	private $constraints = array();

	private $preQueries = array();
	private $postQueries = array();

	/**
	 * @return DBTable
	 */
	static function create()
	{
		return new self;
	}

	function __clone()
	{
		$columns = $this->columns;
		$constraints = $this->constraints;

		$this->dropColumns()->addColumns($columns);
		$this->dropConstraints()->addConstraints($constraints);
	}

	function __sleep()
	{
		return array (
			'name', 'columns', 'constraints'
		);
	}

	/**
	 * @return string
	 */
	function getName()
	{
		return $this->name;
	}

	/**
	 * @return DBTable
	 */
	function setName($name)
	{
		Assert::isScalar($name);

		$this->name = $name;

		return $this;
	}

	/**
	 * @throws DuplicationException
	 * @return DBTable
	 */
	function addColumn(DBColumn $column)
	{
		$name = $column->getName();

		if (isset($this->columns[$name])) {
			throw new DuplicationException('column', $name);
		}

		$this->columns[$name] = $column;

		return $this;
	}

	/**
	 * @throws DuplicationException
	 * @return DBTable
	 */
	function addColumns(array $columns)
	{
		foreach ($columns as $column) {
			$this->addColumn($column);
		}

		return $this;
	}

	/**
	 * @return array of {@link DBColumn}
	 */
	function getColumns()
	{
		return $this->columns;
	}

	/**
	 * @return DBColumn
	 */
	function getColumn($name)
	{
		Assert::isScalar($name);

		if (!isset($this->columns[$name])) {
			throw new ArgumentException('name', 'column not found');
		}

		return $this->columns[$name];
	}

	/**
	 * @return DBTable
	 */
	function dropColumns()
	{
		$this->columns = array();
		$this->constraints = array();

		return $this;
	}

	/**
	 * @throws DuplicationException
	 * @return DBTable
	 */
	function addConstraint(DBConstraint $constraint)
	{
//		foreach ($constraint->getColumns() as $column) {
//			if (!isset($this->columns[$column->getName()])) {
//				$this->addColumn($column);
//			}
//		}

		$name = $constraint->getName();

		if ($name) {
			if (isset($this->constraints[$name])) {
				throw new DuplicationException('constraint', $name);
			}
		}
		else {
			$name = 'constraint_' . $this->name . '_' . (sizeof($this->constraints) + 1);
			$constraint->setName($name);
		}

		$this->constraints[$name] = $constraint;

		return $this;
	}

	/**
	 * @throws DuplicationException
	 * @return DBTable
	 */
	function addConstraints(array $constraints)
	{
		foreach ($constraints as $constraint) {
			$this->addConstraint($constraint);
		}

		return $this;
	}

	/**
	 * @return array of {@link DBConstraint}
	 */
	function getConstraints()
	{
		return $this->constraints;
	}

	/**
	 * @return DBTable
	 */
	function dropConstraints()
	{
		$this->constraints = array();

		return $this;
	}

	/**
	 * @return array of {@link IQuery}
	 */
	function toQueries(IDialect $dialect)
	{
		return $dialect->getTableQuerySet($this);
	}
}

?>