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
 * EntityQuery->SelectQuery cast helper.
 *
 * Casts various objects to ISqlCastable objects, collecting joins according to found associations
 * @aux
 * @ingroup Orm_Query_Builder
 */
final class EntityQueryBuilder implements ISubjectivity
{
	/**
	 * @var string
	 */
	private $table;

	/**
	 * @var string|null
	 */
	private $alias;

	/**
	 * @var IQueryable
	 */
	private $entity;

	/**
	 * @var array of SelectQuerySource
	 */
	private $joins = array();

	/**
	 * @var array of propertyName=>EntityQueryBuilder
	 */
	private $joined = array();

	/**
	 * @var array of propertyPath=>EntityProperty
	 */
	private $propertyCache = array();

	/**
	 * Fixed sql identifiers list
	 * @var array
	 */
	private $registeredIds = array();

	/**
	 * @param IQueryable $entity entity to use as base when guessing path to queried properties
	 * @param string $alias optional table label
	 */
	function __construct(IQueryable $entity, $alias = null)
	{
		Assert::isScalarOrNull($alias);

		$this->entity = $entity;

		$this->table = $entity->getPhysicalSchema()->getTable();
		$this->alias =
			$alias
				? $alias
				: $this->table;

		$this->registeredIds[$this->table] = true;
		if ($alias) {
			$this->registeredIds[$alias] = true;
		}

		$this->joins[] = new SelectQuerySource(
			new AliasedSqlValueExpression(
				new SqlIdentifier($this->table),
				$alias
			)
		);
	}

	/**
	 * Gets the database table alias
	 * @return string
	 */
	function getAlias()
	{
		return $this->alias;
	}

	/**
	 * Gets the table name the entity reside
	 * @return string
	 */
	function getTable()
	{
		return $this->table;
	}

	/**
	 * Gets the entity used as base when guessing path to queried properties
	 * @return IQueryable
	 */
	function getEntity()
	{
		return $this->entity;
	}

	/**
	 * Gets the list of sources for selection that needed to be looked up when executing
	 * a selection (according to the linked path to a properties)
	 * @return array of SelectQuerySource
	 */
	function getSelectQuerySources()
	{
		$yield = $this->joins;

		foreach ($this->joined as $eqb) {
			$yield = array_merge($yield, $eqb->getSelectQuerySources());
		}

		return $yield;
	}

	/**
	 * Forces the string to be used as SQL identifier, not a value
	 * @param string $string
	 * @return EntityQueryBuilder itself
	 */
	function registerIdentifier($string)
	{
		if ($string) {
			$this->registeredIds[$string] = true;
		}

		return $this;
	}

	function subject($subject, ISubjective $object = null)
	{
		if ($subject instanceof ISubjective) { // bogus check
			return $subject->toSubjected($this);
		}

		if ($subject instanceof ISqlCastable) {
			return $subject;
		}

		if ($subject instanceof OrmProperty) {
			return $this->subject(new EntityProperty($this, $subject));
		}

		if ($subject instanceof EntityProperty) {
			return $subject->getSqlColumn();
		}

		if ($subject instanceof IBoxable) {
			return new SqlValue($subject->getValue());
		}

		if ($subject instanceof EntityPropertyValue) {
			// value cast thru explicit specification of PropertyType to use
		}

		if ($subject instanceof IdentifiableOrmEntity) {
			return $this->subject($subject->_getId());
		}

		if (is_scalar($subject)) {
			try {
				return
					$this->subject(
						$this->getEntityProperty($subject)
					);
			}
			catch (ArgumentException $e) {
				// probably, a value, not a property path
			}

			if ($this->hasId($subject)) {
				return new SqlIdentifier($subject);
			}
		}
		else if (!is_null($subject)) {
			Assert::isUnreachable(
				'do not know how to subject %s',
				TypeUtils::getName($subject)
			);
		}

		return new SqlValue($subject);
	}

	private function hasId($subject)
	{
		if (isset($this->registeredIds[$subject])) {
			return true;
		}

		foreach ($this->joined as $joined) {
			if ($joined->hasId($subject)) {
				$this->registeredIds[$subject] = true;

				return true;
			}
		}

		return false;
	}

	/**
	 * @return EntityProperty
	 */
	private function getEntityProperty($property)
	{
		if (!isset($this->propertyCache[$property])) {
			$this->propertyCache[$property] = 
				$this->entity
					->getLogicalSchema()
					->getEntityProperty(new EntityPropertyPath($property, $this));
		}

		return $this->propertyCache[$property];
	}
	
	/**
	 * @return EntityQueryBuilder
	 */
	function joinEncapsulant($encapsulant)
	{
		Assert::isScalar($encapsulant);
		
		if (!isset($this->joined[$encapsulant])) {
			$property = $this->entity->getLogicalSchema()->getProperty($encapsulant);
			$type = $property->getType();
			
			Assert::isTrue($type instanceof AssociationPropertyType);
			
			$builder = $this->joined[$encapsulant] =
				new self (
					$type->getContainer(),
					$this->alias . '_' . $encapsulant
				);

			$builder->joins = array();
			$this->join($property, $type, $builder, end($this->joins));
		}

		return $this->joined[$encapsulant];
	}

	/**
	 * @return void
	 */
	private function join(
			OrmProperty $property,
			AssociationPropertyType $type,
			EntityQueryBuilder $builder,
			SelectQuerySource $source
		)
	{
		$joinMethod =
			$type->getAssociationMultiplicity()->is(
				AssociationMultiplicity::EXACTLY_ONE
			)
				? SqlJoinMethod::INNER // exactlyOne association is strict enough
				: SqlJoinMethod::LEFT;

		$condition = Expression::andChain();
		$srcSqlFields = $property->getFields();
		$dstSqlFields = $builder->entity->getLogicalSchema()->getIdentifier()->getFields();

		foreach ($srcSqlFields as $k => $v) {
			$condition->add(
				Expression::eq(
					new SqlColumn($srcSqlFields[$k], $this->alias),
					new SqlColumn($dstSqlFields[$k], $builder->alias)
				)
			);
		}

		$source->join(
			new SqlConditionalJoin(
				new SelectQuerySource(
					new AliasedSqlValueExpression(
						new SqlIdentifier($builder->table),
						$builder->alias
					)
				),
				new SqlJoinMethod($joinMethod),
				$condition
			)
		);
	}
}

?>