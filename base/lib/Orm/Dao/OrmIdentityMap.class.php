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
	 * @var array of scalarId => id
	 */
	private $idList = array();

	/**
	 * @var OrmProperty
	 */
	private $identifier;

	/**
	 * @var IdentifiableOrmEntity
	 */
	private $stubObject;

	function __construct(ILogicallySchematic $logicalSchema)
	{
		$identifier = $logicalSchema->getIdentifier();

		if (!$identifier) {
			throw new OrmModelIntegrityException(
				'IdentityMapOrmDao is for identifierable entities only'
			);
		}

		if ($identifier->getVisibility()->isNot(OrmPropertyVisibility::FULL)) {
			throw new OrmModelIntegrityException(
				'identifier property should have FULL access level'
			);
		}

		$this->stubObject = $logicalSchema->getNewEntity();

		Assert::isTrue(
			$this->stubObject instanceof IdentifiableOrmEntity,
			'%s should implement IdentifiableOrmEntity',
			$logicalSchema->getEntityName()
		);

		$this->identifier = $identifier;
	}

	/**
	 * @return IdentifiableOrmEntity
	 */
	function getLazy($id)
	{
		$scalarId = $this->getScalarId($id);

		if (!isset($this->identityMap[$scalarId])) {
			$entity = clone $this->stubObject;
			$entity->_setId($id);

			$this->identityMap[$scalarId] = $entity;
			$this->idList[$scalarId] = $id;
		}

		return $this->identityMap[$scalarId];
	}

	/**
	 * @return boolean
	 */
	function has($id)
	{
		$scalarId = $this->getScalarId($id);

		return isset ($this->identityMap[$scalarId]);
	}

	/**
	 * @return OrmIdentityMap
	 */
	function drop($id)
	{
		$scalarId = $this->getScalarId($id);

		unset ($this->identityMap[$scalarId]);
		unset ($this->idList[$scalarId]);

		return $this;
	}

	/**
	 * @return OrmIdentityMap
	 */
	function clean()
	{
		$this->identityMap = array();
		$this->idList = array();

		return $this;
	}

	/**
	 * @return IdentifiableOrmEntity|null
	 */
	function get($id)
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
	function add(IdentifiableOrmEntity $entity)
	{
		Assert::isTrue(get_class($entity) == get_class($this->stubObject));

		$id = $entity->_getId();

		$scalarId = $this->getScalarId($id);

		$this->identityMap[$scalarId] = $entity;
		$this->idList[$scalarId] = $id;

		return $this;
	}

	/**
	 * @return array of id => IdentifiableOrmEntity
	 */
	function getList()
	{
		return $this->identityMap;
	}

	/**
	 *
	 * @return unknown_type
	 */
	function getIds()
	{
		return $this->idList;
	}

	/**
	 * @return OrmIdentityMap
	 */
	function addList(array $list)
	{
		foreach ($list as $object) {
			$this->add($object);
		}

		return $this;
	}

	/**
	 * Actually, this method is not needed because we can use implicit mapping:
	 * $this->identityMap[(string)$id] = $entity;
	 *
	 * That is because comppsite ids implement IIdentifierMappable::__toString();
	 *
	 * But right now this method is used to check this explicitly.
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