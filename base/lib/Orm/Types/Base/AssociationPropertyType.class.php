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
 * 0:1, 1:1 relation implementation
 * @ingroup BaseOrmTypes
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
			Type::create($identifier->getType())->isDescendantOf(new Type('IReferenced'))
			// TODO: clean up
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
						$value instanceof IIdentifiable
							? $value->getId()
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
}

?>