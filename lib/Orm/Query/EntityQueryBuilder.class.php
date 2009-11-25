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
 * object to ISqlCastable subjection
 * @internal
 * @ingroup
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
	 * @var EntityQuery
	 */
	private $entityQuery;

	/**
	 * @var array of SelectQuerySource
	 */
	private $joins = array();

	/**
	 * @var array of propertyName=>EntityQueryBuilder
	 */
	private $joined = array();

	/**
	 * @var array of property{Name,Path}=>EntityProperty
	 */
	private $propertyCache = array();

	/**
	 * Sql identifiers list
	 */
	private $registeredIds = array();

	function __construct(EntityQuery $entityQuery, $alias = null)
	{
		Assert::isScalarOrNull($alias);

		$this->entityQuery = $entityQuery;

		$this->table = $entityQuery->getEntity()->getPhysicalSchema()->getTable();
		$this->alias =
			$alias
				? $alias
				: $this->table;

		$this->registeredIds[$alias] = true;

		$this->joins[] = new SelectQuerySource(
			new AliasedSqlValueExpression(
				new SqlIdentifier($entityQuery->getTable()),
				$entityQuery->alias
			)
		);
	}

	function getSelectQuerySources()
	{
		return $this->joins;
	}

	function registerIdentifier($string)
	{
		$this->registeredIds[$string] = true;
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

		if (is_scalar($subject)) {

			if ($this->hasId($subject)) {
				return new SqlIdentifier($subject);
			}

			try {
				return
					$this->subject(
						$this->getEntityProperty($subject)
					);
			}
			catch (Exception $e) {
				// probably, a value, not a property path
			}
		}

		return new SqlValue((string) $subject);
	}

	function addId($id)
	{
		$this->registeredIds[$id] = true;

		return $this;
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
			// a path actually
			if (false !== strpos($property, '.')) {
				$this->propertyCache[$property] = $this->guessEntityProperty($property);
			}
			else {
				$this->propertyCache[$property] =
					new EntityProperty(
						$this->alias,
						$this->entity->getLogicalSchema()->getProperty($property)
					);
			}
		}

		return $this->propertyCache[$property];
	}

	/**
	 * @return EntityProperty
	 */
	private function guessEntityProperty($propertyPath)
	{
		$propertyPathChunks = explode('.', $propertyPath);

		if (sizeof($propertyPathChunks) == 1) {
			return $this->getEntityProperty($propertyPath);
		}

		$propertyName = reset($propertyPath);

		if (isset($this->joined[$propertyName])) {
			$builder = $this->joined[$propertyName];
		}
		else {
			$property = $this->getEntityProperty($propertyName)->getProperty();

			// FIXME accept only AssocProperty

			$builder = $this->joined[$propertyName] =
				new self(
					$property->getType()->getContainer(),
					(APP_SLOT_CONFIGURATION & SLOT_CONFIGURATION_FLAG_DEVELOPMENT) != 0
						? $this->alias . '_' . $propertyName
						: substr(sha1($this->alias), 0, 6) . '_' . $propertyName
				);

			$this->join($property, $builder, end($this->joins));
		}

		return
			$builder->getEntityProperty(
				join('.', array_slice($propertyPath, 1))
			);
	}

	/**
	 * @return void
	 */
	private function join(
			OrmProperty $property,
			EntityQueryBuilder $builder,
			SelectQuerySource $source
		)
	{
		$type = $property->getType();

		Assert::isTrue($type instanceof AssociationPropertyType);

		$joinMethod =
			$type->getAssociationMultiplicity()->is(
				AssociationMultiplicity::EXACTLY_ONE
			)
				? SqlJoinMethod::INNER // exactlyOne association is strict enough
				: SqlJoinMethod::LEFT;

		$joinExpr = Expression::andChain();
		$srcSqlFields = $property->getFields();
		$dstSqlFields = $builder->entityQuery->getEntity()->getLogicalSchema()->getIdentifier()->getFields();

		foreach ($srcSqlFields as $k => $v) {
			$joinExpr->add(
				Expression::eq(
					new SqlColumn($srcSqlFields[$k], $this->alias),
					new SqlColumn($dstSqlFields[$k], $builder->alias)
				)
			);
		}

		$source->join(
			new SqlConditionalJoin(
				$builder->table,
				$builder->alias,
				new SqlJoinMethod($joinMethod),
				$joinExpr
			)
		);
	}
}

?>