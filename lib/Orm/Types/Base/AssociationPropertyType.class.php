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
 * 0:1, 1:1 relation implementation
 * @ingroup Orm_Types
 */
class AssociationPropertyType extends OrmPropertyType
{
	/**
	 * @var IQueryable
	 */
	private $container;

	/**
	 * @var OrmPropertyType
	 */
	private $identifierType;

	/**
	 * @var AssociationMultiplicity
	 */
	private $multiplicity;

	/**
	 * @var AssociationBreakAction
	 */
	private $action;

	/**
	 * All we need to know is the associative entity (the container), because the association
	 * is build between this one property and the identifier of the container
	 * @throws OrmModelIntegrityException
	 */
	function __construct(
			IQueryable $container,
			AssociationMultiplicity $multiplicity,
			AssociationBreakAction $action
		)
	{
		$identifier = $container->getLogicalSchema()->getIdentifier();

		if (!$identifier) {
			throw new OrmModelIntegrityException(
				'Cannot associate to an entity %s without id',
				$container->getLogicalSchema()->getEntityName()
			);
		}

		$this->container = $container;

		Assert::isTrue(
			TypeUtils::isChild(
				$identifier->getType(),
				'IOrmPropertyReferencable'
			)
		);

		$this->identifierType = call_user_func(
			array(
				get_class($identifier->getType()),
				'getRefHandler',
			),
			$multiplicity
		);
		$this->multiplicity = $multiplicity;
		$this->action = $action;
	}

	/**
	 * @return string
	 */
	function getImplClass()
	{
		return $this->container->getLogicalSchema()->getEntityName();
	}

	/**
	 * @return AssociationMultiplicity
	 */
	function getAssociationMultiplicity()
	{
		return $this->multiplicity;
	}

	/**
	 * @return AssociationBreakAction
	 */
	function getAssociationBreakAction()
	{
		return $this->action;
	}

	/**
	 * @return IQueryable
	 */
	function getContainer()
	{
		return $this->container;
	}

	/**
	 * @return array
	 */
	function getDBFields()
	{
		return $this->identifierType->getDBFields();
	}

	/**
	 * @return mixed
	 */
	function makeValue(array $rawValue, FetchStrategy $fetchStrategy)
	{
		try {
			$id = $this->identifierType->makeValue($rawValue, $fetchStrategy);
		}
		catch (OrmModelIntegrityException $e) {
			$id = null;
		}

		if (is_null($id)) {
			if (!$this->isNullable()) {
				throw new OrmModelIntegrityException('cannot be null');
			}

			return null;
		}

		$dao = $this->container->getDao();

		if ($fetchStrategy->is(FetchStrategy::LAZY)) {
			$entity = $dao->getLazyById($id);
		}
		else {
			$entity = $dao->getById($id);
		}

		return $entity;
	}

	/**
	 * @return array
	 */
	function makeValueSet(array $rawValueSet, FetchStrategy $fetchStrategy)
	{
		$ids = array();
		foreach ($rawValueSet as $rawValue) {
			try {
				$ids[] = $this->identifierType->makeValue($rawValue, $fetchStrategy);
			}
			catch (OrmModelIntegrityException $e) {
				if (!$this->isNullable()) {
					throw new OrmModelIntegrityException('cannot be null');
				}

				$ids[] = null;
			}
		}

		if (empty($ids)) {
			return array();
		}

		$dao = $this->container->getDao();
		$entities = array();

		if ($fetchStrategy->is(FetchStrategy::LAZY)) {
			foreach ($ids as $id) {
				$entities[] = $id
					? $dao->getLazyById($id)
					: null;
			}
		}
		else {
			$toFetch = array();

			foreach ($ids as $id) {
				if (!is_null($id)) {
					$toFetch[] = $id;
				}
			}

			$entities = $dao->getByIds($toFetch);
		}

		return $entities;
	}

	/**
	 * @return array
	 */
	function makeRawValue($value)
	{
		if (is_null($value)) {
			if (!$this->isNullable()) {
				throw new OrmModelIntegrityException('cannot be null');
			}
		}

		return
			$this->identifierType->makeRawValue(
				is_null($value)
					? null
					: (
						$value instanceof IdentifiableOrmEntity
							? $value->_getId()
							: $value
					)
			);
	}

	/**
	 * @return boolean
	 */
	function isNullable()
	{
		return $this->multiplicity->is(AssociationMultiplicity::ZERO_OR_ONE);
	}

	protected function getCtorArgumentsPhpCode()
	{
		return array(
			$this->container->getLogicalSchema()->getEntityName() . '::orm()',
			'new AssociationMultiplicity(AssociationMultiplicity::' . $this->multiplicity->getId() . ')',
			'new AssociationBreakAction(AssociationBreakAction::' . $this->action->getId() . ')'
		);
	}
}

?>