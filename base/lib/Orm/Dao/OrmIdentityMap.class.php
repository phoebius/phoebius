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
 * @ingroup Dao
 */
class OrmIdentityMap
{
	/**
	 * @var array of {@link IdentifiableOrmEntity}
	 */
	private $identityMap = array();

	/**
	 * @var OrmProperty
	 */
	private $identifier;

	/**
	 * @var string
	 */
	private $identifierSetter;

	/**
	 * @var IdentifiableOrmEntity
	 */
	private $stubObject;

	function __construct(ILogicallySchematic $logicalSchema)
	{
		$identifier = $logicalSchema->getIdentifier();

		if (!$identifier) {
			throw new OrmModelIntegrityException('IdentityMapOrmDao is for identifierable entities only');
		}

		if ($identifier->getVisibility()->isNot(OrmPropertyVisibility::FULL)) {
			throw new OrmModelIntegrityException('identifier property should have FULL access level');
		}

		$this->stubObject = $logicalSchema->getNewEntity();

		Assert::isTrue(
			$this->stubObject instanceof IdentifiableOrmEntity,
			'%s should populate entities that extend IdentifiableOrmEntity',
			$logicalSchema->getEntityName()
		);

		$this->identifier = $identifier;
		$this->identifierSetter = $identifier->getSetter();
	}

	/**
	 * @return IdentifiableOrmEntity
	 */
	function getLazyFromIdentityMap($id)
	{
		$scalarId = $this->getScalarId($id);

		if (!isset($this->identityMap[$scalarId])) {
			$entity = clone $this->stubObject;
			$entity->_setId($id);

			$this->identityMap[$scalarId] = $entity;
		}

		return $this->identityMap[$this->getScalarId($scalarId)];
	}

	/**
	 * @return boolean
	 */
	function isInIdentityMap($id)
	{
		$scalarId = $this->getScalarId($id);

		return isset ($this->identityMap[$scalarId]);
	}

	/**
	 * @return OrmIdentityMap
	 */
	function dropFromIdentityMap($id)
	{
		$scalarId = $this->getScalarId($id);

		unset ($this->identityMap[$scalarId]);

		return $this;
	}

	/**
	 * @return OrmIdentityMap
	 */
	function dropIdentityMap()
	{
		$this->identityMap = array();

		return $this;
	}

	/**
	 * @return IdentifiableOrmEntity|null
	 */
	function getFromIdentityMap($id)
	{
		$scalarId = $this->getScalarId($id);

		return
			isset($this->identityMap[$scalarId])
				? $this->identityMap[$scalarId]
				: null;
	}

	/**
	 * @return OrmIdentityMap
	 */
	function addToIdentityMap(IdentifiableOrmEntity $entity)
	{
		$scalarId = $this->getScalarId($entity->getId());

		$this->identityMap[$scalarId] = $entity;

		return $this;
	}

	/**
	 * TODO: remove ability to provide both scalar and object ID representation. Introduce IIdentifiableOrmProperty that accepts only IIdentifierMappable as property type
	 * @return string
	 */
	private function getScalarId($id)
	{
		if (is_scalar($id) || is_null($id)) {
			return $id;
		}
		else {
			Assert::isTrue(
				$id instanceof IIdentifierMappable,
				'identifier object should implement IIdentifierMappable'
			);

			return $id->toScalarId();
		}
	}
}

?>