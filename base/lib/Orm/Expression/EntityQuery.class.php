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
 * $entitySetQuery =
 * 	EntityQuery::create(MyEntity::orm())
 * 		->where(
 *			'time',
 *			Expression::between(
 *				Date::now()->spawn('-1 day'),
 *				Date::now()->spawn('+1 day')
 *			)
 * 		);
 *
 * LINQ to OrmEntity
 * TODO:
 *  * aggregation functions
 *  * HAVING clause
 * @ingroup OrmExpression
 */
final class EntityQuery implements ISqlSelectQuery, IDalExpression
{
	/**
	 * @var array of Property{name,path} => EntityQuery
	 */
	private $entityPropertyCache = array();

	/**
	 * @var IQueried
	 */
	private $entity;

	/**
	 * @var string|null
	 */
	private $alias;

	/**
	 * @var string|null
	 */
	private $table;

	/**
	 * @var array of propertyName => EntityQuery
	 */
	private $joined = array();

	/**
	 * @var array of {@link IEntityPropertyExpression}
	 */
	private $expressionChain = array();

	/**
	 * @var array
	 */
	private $orderBy = array();

	/**
	 * @var array of ISqlValueExpression
	 */
	private $groupByExpressions = array();

	/**
	 * @var IDalExpression
	 */
	private $having;

	/**
	 * @var integer
	 */
	private $limit = 0;

	/**
	 * @var offset
	 */
	private $offset = 0;

	/**
	 * @var boolean
	 */
	private $distinct;

	/**
	 * @return EntityQuery
	 */
	static function create(IQueried $entity, $alias = null)
	{
		return new self ($entity, $alias);
	}

	function __construct(IQueried $entity, $alias = null)
	{
		$this->entity = $entity;
		$this->table = $entity->getPhysicalSchema()->getDBTableName();
		$this->alias =
			$alias
				? $alias
				: $this->table;

		$this->expressionChain = new EntityPropertyExpressionChain();
	}

	/**
	 * @return string
	 */
	function getAlias()
	{
		Return $this->alias;
	}

	/**
	 * @return EntityQuery
	 */
	function setDistinct()
	{
		$this->distinct = true;

		return $this;
	}

	/**
	 * Drops ORDERBY list and adds an order expression
	 * @return EntityQuery an object itself
	 */
	function orderBy($property, SqlOrderDirection $direction = null)
	{
		$this->dropOrderBy()->andOrderBy($property, $direction);

		return $this;
	}

	/**
	 * Adds an order expression
	 * @return EntityQuery an object itself
	 */
	function andOrderBy($property, SqlOrderDirection $direction = null)
	{
		foreach ($this->guessEntityProperty($property)->getSqlColumns() as $column) {
			$this->orderBy[] =
				new SqlOrderExpression(
					$column,
					$direction
				);
		}

		return $this;
	}

	/**
	 * Drops the set of order expressions
	 * @return EntityQuery an object itself
	 */
	function dropOrderBy()
	{
		$this->orderBy = array();

		return $this;
	}

	/**
	 * Drops grouping schema and adds a grouping element
	 * @return EntityQuery an object itself
	 */
	function groupBy($property)
	{
		$this->dropGroupBy()->andGroupBy($property);

		return $this;
	}

	/**
	 * Adds a grouping element
	 * @return EntityQuery an object itself
	 */
	function andGroupBy($property)
	{
		$this->groupByExpressions =
			array_merge(
				$this->groupByExpressions,
				$this->guessEntityProperty($property)->getSqlColumns()
			);

		return $this;
	}

	/**
	 * Drops a grouping list
	 * @return EntityQuery an object itself
	 */
	function dropGroupBy()
	{
		$this->groupByExpressions = array();

		return $this;
	}

	/**
	 * Sets a limit for row selection
	 * @param integer $limit positive integer
	 * @return EntityQuery an object itself
	 */
	function setLimit($limit)
	{
		Assert::isPositiveInteger($limit);

		$this->limit = $limit;

		return $this;
	}

	/**
	 * Gets the limit for the row selection
	 * @return integer 0 if limit is not set, otherwise a positive integer
	 */
	function getLimit()
	{
		return $this->limit;
	}

	/**
	 * Drops a row selection limit
	 * @return EntityQuery an object itself
	 */
	function dropLimit()
	{
		$this->limit = 0;

		return $this;
	}

	/**
	 * Sets the offset for row selection
	 * @param integer $offset positive integer
	 * @return EntityQuery
	 */
	function setOffset($offset)
	{
		Assert::isPositiveInteger($offset);

		$this->offset = $offset;

		return $this;
	}

	/**
	 * Gets the offset for the row selection
	 * @return integet 0 if offset is not set, otherwise a positive integer
	 */
	function getOffset()
	{
		return $this->offset;
	}

	/**
	 * Drops a row selection offset
	 * @return EntityQuery an object itself
	 */
	function dropOffset()
	{
		$this->offset = 0;

		return $this;
	}

	/**
	 * @return string
	 */
	function getDbContainer()
	{
		return $this->alias;
	}

	/**
	 * @return IQueried
	 */
	function getEntity()
	{
		return $this->entity;
	}

	/**
	 * Alias for EntityQuery::addExpression()
	 * @return EntityQuery
	 */
	function where($property, IExpression $expression)
	{
		$this->addExpression($property, $expression);

		return $this;
	}

	/*
	 * @return EntityQuery
	 */
	function andWhere($property, IExpression $expression)
	{
		$this->resortChain(ExpressionChainPredicate::conditionAnd());

		$this->where($property, $expression);

		return $this;
	}

	/*
	 * @return EntityQuery
	 */
	function orWhere($property, IExpression $expression)
	{
		$this->resortChain(ExpressionChainPredicate::conditionOr());

		$this->where($property, $expression);

		return $this;
	}

	/*
	 * @return EntityQuery
	 */
	function addExpression($property, IExpression $expression)
	{
		$ep = $this->guessEntityProperty($property);

		$ep
			->getEntityQuery()
			->resortChain(ExpressionChainPredicate::conditionAnd())
			->add(
				$this->alias,
				$ep->getProperty(),
				$ep->getProperty()->getType()->getEntityPropertyExpression($expression)
			);
	}

	/**
	 * @return EntityPropertyExpressionChain
	 */
	function getEntityPropertyExpressionChain()
	{
		return $this->expressionChain;
	}

	/**
	 * @return EntityQuery
	 */
	function merge($property, EntityQuery $entityQuery)
	{
		$ep = $this->guessEntityProperty($property);

		if ($entityQuery->alias && $entityQuery->alias != $entityQuery->table) {
			$ep->getEntityQuery()->alias = $entityQuery;
		}

		$this
			->resortChain(ExpressionChainPredicate::conditionAnd())
			->add($entityQuery->expressionChain);

		return $this;
	}

	/**
	 * @return EntityQuery
	 */
	function using($property, $alias = null)
	{
		$ep = $this->guessEntityProperty($property);

		if ($alias) {
			$ep->getEntityQuery()->alias = $alias;
		}

		return $this;
	}

	/**
	 * @return EntityExpressionChain
	 */
	private function resortChain(ExpressionChainPredicate $ecp)
	{
		if ($this->expressionChain->getPredicate()->isNot($ecp)) {
			$this->expressionChain =
				EntityExpressionChain::create($ecp)
					->addEntityExpression($this->expressionChain);
		}

		return $this->expressionChain;
	}

	/**
	 * @return IDalExpression
	 */
	function toDalExpression()
	{
		return $this->expressionChain->toDalExpression();
	}

	/**
	 * @return SelectQuery
	 */
	function toSelectQuery()
	{
		// FROM
		// fields
		// WHERE
		// GROUP BY
		// - HAVING
		// ORDER
		// LIMIT
		// OFFSET

		$selectQuery = new SelectQuery;

		$selectQuery->from($this->table, $this->alias);

		$this->fillJoins($selectQuery);

		$selectQuery->setExpression($this->toDalExpression());

		foreach ($this->groupByExpressions as $groupBy) {
			$selectQuery->andGroupBy($groupBy);
		}

		foreach ($this->orderBy as $orderBy) {
			$selectQuery->andOrderBy($orderBy);
		}

		$selectQuery->setLimit($this->limit);
		$selectQuery->setOffset($this->offset);

		return $selectQuery;
	}

	/**
	 * Casts an object to the SQL dialect string
	 * @return string
	 */
	function toDialectString(IDialect $dialect)
	{
		return $this->toSelectQuery()->toDialectString($dialect);
	}

	/**
	 * @return array
	 */
	function getCastedParameters(IDialect $dialect)
	{
		return array();
	}

	/**
	 * @return array
	 */
	function getList()
	{
		return $this->entity->getDao()->getListByQuery($this->toSelectQuery());
	}

	/**
	 * @return void
	 */
	private function fill(SelectQuery $selectQuery)
	{
		foreach ($this->entity->getPhysicalSchema()->getDBColumns() as $field) {
			$selectQuery->get($field, $this->alias);
		}

		foreach ($this->joined as $entityQuery) {
			$property = $entityQuery->getProperty();
			$type = $property->getType();

			Assert::isTrue($type instanceof AssociationPropertyType);

			$joinMethod =
				$type->getAssociationMultiplicity()->is(
					AssociationMultiplicity::exactlyOne()
				)
					? SqlJoinMethod::INNER // exactlyOne association is strict enough
					: SqlJoinMethod::LEFT;

			$selectQuery->join(
				new SqlConditionalJoin(
					$entityQuery->table,
					$entityQuery->table == $entityQuery->alias
						? $entityQuery->alias
						: null,
					new SqlJoinMethod($joinMethod),
					$type->getEntityPropertyExpression(
						$this->alias,
						$property,
						Expresssion::eq(
							new EntityProperty(
								$entityQuery,
								$entityQuery->entity->getLogicalSchema()->getIdentifier()
							)
						)
					)
				)
			);

			$entityQuery->fill($selectQuery);
		}
	}

	/**
	 * @return EntityProperty
	 */
	private function resolveAssocProperty($property)
	{
		$propertyPath = explode('.', $property);

		$propertyName = reset($propertyPath);
		$property = $this->guessEntityProperty(reset($propertyPath))->getProperty();

		Assert::isTrue(
			$property->getType() instanceof AssociationPropertyType,
			'%s::%s property should be of AssociationPropertyType',
			$this->entity->getLogicalSchema()->getName(),
			$propertyName
		);

		$query =
			isset($this->joined[$propertyName])
				? $this->joined[$propertyName]
				: new EntityQuery(
					$property->getType()->getContainer(),
					$this->alias . '_' . $propertyName
				);

		if (sizeof($propertyPath) > 1) {
			return $query->resolveAssocProperty(join('.', array_slice($propertyPath, 1)));
		}
		else {
			return new EntityProperty($query, $property);
		}
	}

	/**
	 * @return EntityProperty
	 */
	private function guessEntityProperty($property)
	{
		if (is_scalar($property)) {
			if (isset($this->entityPropertyCache[$property])) {
				return $this->entityPropertyCache[$property];
			}

			if (false !== strpos('.', $property)) {
				$ep = $this->resolveAssocProperty($property);
				$this->entityPropertyCache[$property] = $ep;

				return $ep;
			}

			try {
				$property = $this->entity->getLogicalSchema()->getProperty($property);
			}
			catch (OrmModelIntegrityException $e){
				Assert::isUnreachable(
					'unknown property %s::%s for %s',
					$this->entity->getLogicalSchema()->getEntityName(),
					$property,
					__CLASS__
				);
			}
		}

		Assert::isTrue(
			$property instanceof OrmProperty,
			'unknwown property'
		);

		$name = $property->getName();
		$this->entityPropertyCache[$name] = new EntityProperty($this, $property);

		return $this->entityPropertyCache[$name];
	}
}

?>