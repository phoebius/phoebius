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
final class AssociationPropertyType extends OrmPropertyType
{
	/**
	 * @var IQueryable
	 */
	private $container;

	/**
	 * @var OrmPropertyType
	 */
	private $fkType;

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
				'Cannot associate to an entity '.$container->getLogicalSchema()->getEntityName().' without id'
			);
		}

		$this->container = $container;

		Assert::isTrue(
			TypeUtils::isChild(
				$identifier->getType(),
				'IOrmPropertyReferencable'
			)
		);

		$this->fkType = $identifier->getType()->getReferenceType($multiplicity);
		$this->multiplicity = $multiplicity;
		$this->action = $action;
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

	function getImplClass()
	{
		return $this->container->getLogicalSchema()->getEntityName();
	}

	function assemble(array $tuple, FetchStrategy $fetchStrategy)
	{
		try {
			$id = $this->fkType->assemble($tuple, $fetchStrategy);
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

	function assebmleSet(array $tuples, FetchStrategy $fetchStrategy)
	{
		$ids = array();

		foreach ($tuples as $tuple) {
			try {
				$ids[] = $this->fkType->assemble($tuple, $fetchStrategy);
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

			foreach ($ids as $key => $id) {
				if (!is_null($id)) {
					$toFetch[] = $id;
				}
			}

			$entities = $dao->getByIds($toFetch);

			$toReturn = array();
			reset($entities);

			foreach ($ids as $id) {
				if ($id) {
					$toReturn[] = current($entities);

					next ($entities);
				}
				else {
					$toReturn[] = null;
				}
			}

			$entities = $toReturn;
		}

		return $entities;
	}

	function disassemble($value)
	{
		return
			$this->fkType->disassemble(
				is_null($value)
					? null
					: (
						$value instanceof IdentifiableOrmEntity
							? $value->_getId()
							: $value
					)
			);
	}

	function getSqlTypes()
	{
		return $this->fkType->getSqlTypes();
	}

	function getColumnCount()
	{
		return $this->fkType->getColumnCount();
	}

	function toGetter(IMappable $entity, OrmProperty $property)
	{
		$returnValue =
			($implClass = $this->getImplClass())
				? $implClass
				: 'mixed';
		if ($property->getMultiplicity()->isNullable()) {
			$returnValue .= '|null';
		}

		$propertyName = $property->getName();
		$capitalizedPropertyName = ucfirst($propertyName);

		return <<<EOT
	/**
	 * @return {$returnValue}
	 */
	function get{$capitalizedPropertyName}()
	{
//		if (\$this->{$propertyName}) { // thats is what called lazy fetching
//			\$this->{$propertyName}->fetch();
//		}

		return \$this->{$propertyName};
	}
EOT;
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