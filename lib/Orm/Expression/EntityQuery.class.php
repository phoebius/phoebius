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
 *
 * LINQ to OrmEntity
 *
 * $entitySetQuery =
 * 	EntityQuery::create(MyEntity::orm())
 * 		->where(
 *			Expression::between(
 *				'time',
 *				Date::now()->spawn('-1 day'),
 *				Date::now()->spawn('+1 day')
 *			)
 * 		);
 *
 * TODO:
 *  * aggregation functions
 *  * HAVING clause
 *  * distinct
 *  * projections
 *  * abilty to fetch entity of any other type (not only of type specified in ctor)
 *  * geEntityProperty() should accept aliased in property path
 *  * querying against multi-field properties
 *
 * @ingroup OrmExpression
 */
final class EntityQuery implements ISqlSelectQuery, IExpressionSubjectConverter //, IDalExpression
{
	/**
	 * @var array of Property{name,path} => EntityQuery
	 */
	private $entityPropertyCache = array();

	/**
	 * @var IQueryable
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

	private $expressionObjectStack = array();
	private $expressionSubjects = array();

	/**
	 * @return EntityQuery
	 */
	static function create(IQueryable $entity, $alias = null)
	{
		return new self ($entity, $alias);
	}

	function __construct(IQueryable $entity, $alias = null)
	{
		$this->entity = $entity;
		$this->table = $entity->getPhysicalSchema()->getDBTableName();
		$this->alias =
			$alias
				? $alias
				: $this->table;

		$this->expressionChain = Expression::andChain();
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
	private function setDistinct()
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
		foreach ($this->getEntityProperty($property)->getSqlColumns() as $column) {
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
				$this->getEntityProperty($property)->getSqlColumns()
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
	 * @return IQueryable
	 */
	function getEntity()
	{
		return $this->entity;
	}

	/**
	 * @return EntityQuery
	 */
	function where(IExpression $expression)
	{
		$this->expressionChain->add($expression->toExpression($this));

		return $this;
	}

	/*
	 * @return EntityQuery
	 */
	function andWhere(IExpression $expression)
	{
		$this->resortChain(ExpressionChainLogicalOperator::conditionAnd());

		$this->where($expression);

		return $this;
	}

	/*
	 * @return EntityQuery
	 */
	function orWhere(IExpression $expression)
	{
		$this->resortChain(ExpressionChainLogicalOperator::conditionOr());

		$this->where($expression);

		return $this;
	}

	/**
	 * @return EntityQuery
	 */
	function merge($property, EntityQuery $entityQuery)
	{
		$ep = $this->getEntityProperty($property);

		if ($entityQuery->alias && $entityQuery->alias != $entityQuery->table) {
			$ep->getEntityQuery()->alias = $entityQuery;
		}

		$this
			->resortChain(ExpressionChainLogicalOperator::conditionAnd())
			->add($entityQuery->expressionChain->toExpression($entityQuery));

		return $this;
	}

	/**
	 * FIXME: IExpression->IDalExpression now only supports one-field properties
	 * @see Expression/IExpressionSubjectConverter#convert($subject, $object)
	 */
	function convert($subject, IExpression $object)
	{
		// bogus check
		if ($subject instanceof IExpression) {
			return $subject->convert($this);
		}

		if (is_scalar($subject)) {
			try {
				$subject = $this->getEntityProperty($subject);
			}
			catch (Exception $e) {
				// probably, a value, not a property path
			}
		}

		if ($subject instanceof EntityProperty) {
			$objId = spl_object_hash($object);

			if (isset($this->expressionSubjects[$objId])) {
				$index = array_search($objId, $this->expressionObjectStack);
				$extracted = array_splice($this->expressionObjectStack, $index + 1);
				foreach ($extracted as $_) {
					unset($this->expressionSubjects[$_]);
				}
			}
			else {
				$this->expressionObjectStack[] = $objId;
				$this->expressionSubjects[$objId] = $subject;
			}

			$columns = $subject->getSqlColumns();

			Assert::isTrue(sizeof($columns) == 1, 'single-field properties are only supported');

			return reset($columns);
		}

		if ($subject instanceof OrmProperty) {
			return $this->convert(EntityProperty::create($this, $subject), $object);
		}

		if ($subject instanceof ISqlCastable) {
			return $subject;
		}

		if (is_scalar($subject) || is_null($subject)) {
			return new ScalarSqlValue($subject);
		}

		// else -- a property value
		$epHashId = reset($this->expressionObjectStack);
		return reset(
			$this->expressionSubjects[$epHashId]->getProperty()->getType()->makeRawValue($subject)
		);
	}

	/**
	 * @return ExpressionChain
	 */
	function getExpressionChain()
	{
		return $this->expressionChain;
	}

	/**
	 * @return EntityQuery
	 */
	function using($property, $alias = null)
	{
		$ep = $this->getEntityProperty($property);

		if ($alias) {
			$ep->getEntityQuery()->alias = $alias;
		}

		return $this;
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
		// - distinct
		// fields
		// - projections
		// WHERE
		// GROUP BY
		// - HAVING
		// ORDER
		// LIMIT
		// OFFSET

		$selectQuery = new SelectQuery;

		$selectQuery->from($this->table, $this->alias != $this->table ? $this->alias : null);

		foreach ($this->entity->getPhysicalSchema()->getDBFields() as $field) {
			$selectQuery->get($field, null, $this->alias);
		}

		$this->fill($selectQuery);

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
	 * @return EntityProperty
	 */
	function getEntityProperty($property)
	{
		if (is_scalar($property)) {
			if (isset($this->entityPropertyCache[$property])) {
				return $this->entityPropertyCache[$property];
			}

			if (false !== strpos($property, '.')) {
				$ep = $this->guessEntityProperty($property);
				$this->entityPropertyCache[$property] = $ep;

				return $ep;
			}

			$property = $this->entity->getLogicalSchema()->getProperty($property);
		}

		Assert::isTrue(
			$property instanceof OrmProperty
			&& $this->entity->getLogicalSchema()->getProperty(
				$property->getName()
			),
			'unknwown property'
		);

		$name = $property->getName();
		$this->entityPropertyCache[$name] = new EntityProperty($this, $property);

		return $this->entityPropertyCache[$name];
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
	 * @return EntityExpressionChain
	 */
	private function resortChain(ExpressionChainLogicalOperator $ecp)
	{
		if ($this->expressionChain->getLogicalOperator()->isNot($ecp)) {
			$this->expressionChain =
				new ExpressionChain(
					$ecp,
					$this->expressionChain->getChain()
				);
		}

		return $this->expressionChain;
	}

	/**
	 * @return void
	 */
	private function fill(SelectQuery $selectQuery)
	{
		foreach ($this->joined as $propertyName => $entityQuery) {
			$property = $this->getEntityProperty($propertyName)->getProperty();
			$type = $property->getType();

			Assert::isTrue($type instanceof AssociationPropertyType);

			$joinMethod =
				$type->getAssociationMultiplicity()->is(
					AssociationMultiplicity::EXACTLY_ONE
				)
					? SqlJoinMethod::INNER // exactlyOne association is strict enough
					: SqlJoinMethod::LEFT;

			$selectQuery->join(
				new SqlConditionalJoin(
					$entityQuery->table,
					$entityQuery->alias,
					new SqlJoinMethod($joinMethod),
					Expression
						::eq(
							new EntityProperty(
								$this,
								$property
							),
							new EntityProperty(
								$entityQuery,
								$entityQuery->entity->getLogicalSchema()->getIdentifier()
							)
						)
						->toExpression($this)
						->toDalExpression()
				)
			);

			$entityQuery->fill($selectQuery);
		}
	}

	/**
	 * @return EntityProperty
	 */
	private function guessEntityProperty($propertyPath)
	{
		$propertyPath = explode('.', $propertyPath);

		$propertyName = reset($propertyPath);
		$property = $this->getEntityProperty(reset($propertyPath))->getProperty();

		Assert::isTrue(
			$property->getType() instanceof AssociationPropertyType,
			'%s::%s property should be of AssociationPropertyType',
			$this->entity->getLogicalSchema()->getEntityName(),
			$propertyName
		);

		if (isset($this->joined[$propertyName])) {
			$query = $this->joined[$propertyName];
		}
		else {
			$query = $this->joined[$propertyName]
				= new EntityQuery(
						$property->getType()->getContainer(),
						(APP_SLOT_CONFIGURATION & SLOT_CONFIGURATION_FLAG_DEVELOPMENT) != 0
							? $this->alias . '_' . $propertyName
							: substr(sha1($this->alias), 0, 6) . '_' . $propertyName
					);
		}

		return $query->getEntityProperty(join('.', array_slice($propertyPath, 1)));
	}
}

?>