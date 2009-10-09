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
 * FIXME: cut resolving functionality to a base class
 * @ingroup XmlOrmDomainImporter
 */
class XmlOrmDomainBuilder implements IOrmDomainBuilder
{
	const DTD_BASE_PATH = '/share/Orm/Domain/Meta/Xml/abstract.dtd';
	const DTD_ROOT_ELEMENT_NAME = 'domain';

	private $xmlFilename;
	private $dtdFilename;

	/**
	 * @var SimpleXMLElement|null
	 */
	private $xmlElement;

	/**
	 * @var OrmDomain
	 */
	private $ormDomain;

	function __construct(OrmDomain $ormDomain, $xmlFilename, $dtdFilename = null)
	{
		Assert::isScalar($xmlFilename);
		Assert::isScalarOrNull($dtdFilename);

		if (!is_file($xmlFilename)) {
			throw new FileNotFoundException($xmlFilename);
		}

		if ($dtdFilename) {
			if (!is_file($dtdFilename)) {
				throw new FileNotFoundException($dtdFilename);
			}
		}
		else {
			$dtdFilename = PHOEBIUS_BASE_ROOT . self::DTD_BASE_PATH;
		}

		$this->ormDomain = $ormDomain;
		$this->xmlFilename = $xmlFilename;
		$this->dtdFilename = str_replace(
			'\\',
			'/',
			$dtdFilename
		);
	}

	/**
	 * @throws OrmModelException
	 * @return OrmDomain
	 */
	function build()
	{
		try {
			$this->load();
			$this->generateDomain();
		}
		catch (Exception $e) {
			$this->dispose();

//			$this->recorder->putErrorLine($e->getMessage());

			throw $e;
		}

		$this->dispose();

		return $this->ormDomain;
	}

	/**
	 * @return void
	 */
	private function load()
	{
		$xmlContents = file_get_contents($this->xmlFilename);

		try {
//			$this->recorder->putInfoLine('Loading XML definition ' . $this->xmlFilename . '...');
			$this->xmlElement = new SimpleXMLElement(
				$this->fixDtdPath($xmlContents),
				LIBXML_DTDATTR | LIBXML_DTDLOAD | LIBXML_DTDVALID
			);
//			$this->recorder->putMsg(' done.');
		}
		catch (ExecutionContextException $e) {
//			$this->recorder->putError(' failed.');
			$xmlError = libxml_get_last_error();
			throw new OrmModelDefinitionException(
				$xmlError->message . ' in ' . $this->xmlFilename . ':' . $xmlError->line
			);
		}
	}

	/**
	 * @return array
	 */
	private function getChildNodeSet(SimpleXMLElement $node, $childNodeName)
	{
		Assert::isScalar($childNodeName);

		$nodes = (array) $node;

		if (!isset($nodes[$childNodeName])) {
			$nodes = array();
		}
		else if ($nodes[$childNodeName] instanceof SimpleXMLElement) {
			$nodes = array($nodes[$childNodeName]);
		}
		else {
			$nodes = $nodes[$childNodeName];
		}

		return $nodes;
	}

	/**
	 * @return void
	 */
	private function generateDomain()
	{
		$classEntities = array();
		$classProperties = array();
		$classContainers = array();

		if (isset($this->xmlElement['db-schema'])) {
			$this->ormDomain->setDbSchema((string) $this->xmlElement['db-schema']);
		}

		foreach ($this->getChildNodeSet($this->xmlElement->entities, 'entity') as $entity) {
//			$this->recorder->putInfoLine('Creating new entity:');
			$class = $this->generateEntity($entity);

			$this->ormDomain->addClass($class);
//			$this->recorder->putMsgLine($class->getName() . ' added to domain.');

//			$this->recorder->putInfoLine('Resolving ' . $class->getName() . ' identifier...');

			// process an identifier (if specified). However, entity CAN BE identifierless
			if (isset($entity->properties->identifier)) {
				$id = $this->generateIdentifier($entity->properties->identifier);
				// we should generate an identifier (if any) before properties
				// because type juggling depends on the identifier availabilty
				$class->addIdentifier($id);

//				$this->recorder->putMsg(' done.');
			}
			else {
//				$this->recorder->putWarning(' entity is identifierless.');
			}

			// collect props and containers for further processing
			$name = $class->getName();

			$classEntities[$name] = $class;
			$classProperties[$name] = $this->getChildNodeSet($entity->properties, 'property');
			$classContainers[$name] = $this->getChildNodeSet($entity->properties, 'container');

//			$this->recorder->putLine();
		}

		// firsly, we process props as they can have one-to-one associations only
		foreach ($classProperties as $name => $properties) {
			$this->obtainClassProperties($classEntities[$name], $properties);
		}

		// for now we can process containers
//		$this->recorder->putWarningLine('Containers are not generated for now');
		foreach ($classContainers as $name => $containers) {
			$this->obtainClassContainers($classEntities[$name], $containers);
		}
	}

	/**
	 * @return void
	 */
	private function obtainClassProperties(OrmClass $class, array $properties)
	{
		if (!empty($properties)) {
//			$this->recorder->putInfoLine('Obtaining ' . $class->getName() . ' properties:');
			foreach ($properties as $property) {
				$property = $this->generateProperty($property);
				$class->addProperty($property);
			}
//			$this->recorder->putLine();
		}
		else {
//			$this->recorder->putInfoLine('No properties for ' . $class->getName());
		}
	}

	/**
	 * @return void
	 */
	private function obtainClassContainers(OrmClass $class, array $containers)
	{
		if (!empty($containers)) {
//			$this->recorder->putInfoLine('Obtaining ' . $class->getName() . ' containers:');
			foreach ($containers as $container) {
				$container = $this->generateContainer($class, $container);
				$class->addProperty($container);
			}
//			$this->recorder->putLine();
		}
		else {
//			$this->recorder->putInfoLine('No containers for ' . $class->getName());
		}
	}

	/**
	 * @return OrmClass
	 */
	private function generateEntity(SimpleXMLElement $xmlEntity)
	{
		$class = new OrmClass();
		$class->setName((string) $xmlEntity['name']);
		$class->setHasDao('true' == ((string) $xmlEntity['has-dao']));

		if ($class->hasDao()) {
			if (isset($xmlEntity['db-schema'])) {
				$class->setDbSchema((string) $xmlEntity['db-schema']);
			}
			else {
				$class->setDbSchema($this->ormDomain->getDbSchema());
			}

			if (isset($xmlEntity['db-table'])) {
				$class->setDBTableName((string) $xmlEntity['db-table']);
			}
		}

		return $class;
	}

	/**
	 * @return OrmProperty
	 */
	private function generateIdentifier(SimpleXMLElement $xmlIdentifier)
	{
		$type = $this->resolvePropertyType(
			(string) $xmlIdentifier['type'],
			AssociationMultiplicity::zeroOrOne()
		);

		// FIXME: reject associations, accept only mappable types

		$identifier = new OrmProperty(
			(string) $xmlIdentifier['name'],
			$type,
			OrmPropertyVisibility::full(),
			false
		);

		if (isset($xmlIdentifier['db-columns'])) {
			$identifier->setDBColumnNames(
				$this->getDBFields($xmlIdentifier['db-columns'])
			);
		}

		return $identifier;
	}

	/**
	 * @return array
	 */
	private function getDBFields($columnsList)
	{
		$columns = explode(',', $columnsList);
		$yield = array();
		foreach ($columns as $column) {
			$column = trim($column);
			if ($column) {
				$yield[] = $column;
			}
		}

		return $yield;
	}

	private function generateDBFields($propertyName, OrmPropertyType $ormPropertyTpe)
	{
		$fields = array();

		$propertyPrefix = strtolower(
			preg_replace(
				'/([a-z])([A-Z])/',
				'$1_$2',
				$propertyName
			)
		);

		foreach (array_keys($ormPropertyTpe->getDBFields()) as $key) {
			$fields[] = (
				$propertyPrefix
				. (
					(!is_int($key) || $key > 0)
						? '_' . $key
						: ''
				)
			);
		}

		return $fields;
	}

	/**
	 * @return OrmProperty
	 */
	private function generateProperty(SimpleXMLElement $xmlProperty)
	{
//		$this->recorder->putInfoLine('Generating ' . ((string) $xmlProperty['name']) . ' property...');

		$type = $this->resolvePropertyType(
			(string) $xmlProperty['type'],
			new AssociationMultiplicity((string) $xmlProperty['multiplicity'])
		);

		$property = new OrmProperty(
			(string) $xmlProperty['name'],
			isset($xmlProperty['db-columns'])
				? $this->getDBFields($xmlProperty['db-columns'])
				: $this->$this->getDBFields((string) $xmlProperty['name'], $type),
			$type,
			new OrmPropertyVisibility((string) $xmlProperty['visibility']),
			$xmlProperty['unique'] == 'true'
		);

//		$this->recorder->putMsg(' done.');

		return $property;
	}

	/**
	 * @return OrmProperty
	 */
	private function generateContainer(OrmClass $type, SimpleXMLElement $xmlContainer)
	{
		// acceptable types: OneToManyContainerPropertyType, ManyToManyContainerPropertyType

//		$this->recorder->putInfoLine('Generating ' . ((string) $xmlContainer['name']) . ' container...');

		try {
			$referredType = $this->ormDomain->getClass((string)$xmlContainer['type']);
		}
		catch (OrmModelIntegrityException $e) {
			throw new OrmModelIntegrityException('Reference to unknown entity ' . ((string)$xmlContainer['type']));
		}

		try { // one-to-many
			$referredProperty = $referredType->getEntityProperty((string)$xmlContainer['refs']);

			$propertyType = new OneToManyContainerPropertyType(
				$type,
				$referredType,
				$referredProperty
			);
		}
		catch (OrmModelIntegrityException $e) { // many to many

			try {
				$proxy = $this->ormDomain->getClass((string)$xmlContainer['refs']);
			}
			catch (OrmModelIntegrityException $e) {
				$proxy = new OrmClass();
				$proxy->setHasDao(true);
				$proxy->setName((string)$xmlContainer['refs']);
			}

			// FIXME: check whether type and referenced type are identifiable and id property types implement IReferenced
			// FIXME: check whether those properties are already set

			$type_id_name = '___mtm_' . $type->getDBTableName();
			try {
				$type_property = new OrmProperty(
					$type_id_name,
					new AssociationPropertyType(
						$type,
						AssociationMultiplicity::exactlyOne(),
						AssociationBreakAction::cascade()
					)
				);

				$proxy->addProperty(
					$type_property
				);
			}
			catch (OrmModelIntegrityException $e) {
				$type_property = $proxy->getEntityProperty($type_id_name);
			}

			$referredType_id_name = '___mtm_' . $type->getDBTableName();
			try {
				$referredType_property = new OrmProperty(
					$referredType_id_name,
					new AssociationPropertyType(
						$referredType,
						AssociationMultiplicity::exactlyOne(),
						AssociationBreakAction::cascade()
					)
				);

				$proxy->addProperty(
					$referredType_property
				);
			}
			catch (OrmModelIntegrityException $e) {
				$referredType_property = $proxy->getEntityProperty($referredType_id_name);
			}

			$propertyType = new ManyToManyContainerPropertyType(
				$proxy,
				$type_property,
				$referredType_property
			);
		}

//		$this->recorder->putMsg(' done.');

		$property = new OrmProperty(
			(string) $xmlContainer['name'],
			$propertyType,
			new OrmPropertyVisibility(OrmPropertyVisibility::READONLY),
			false
		);

		if (isset($xmlContainer['db-columns'])) {
			$property->setDBColumnNames(
				$this->getDBFields($xmlContainer['db-columns'])
			);
		}

		return $property;
	}

	/**
	 * @return OrmPropertyType
	 */
	private function resolvePropertyType($name, AssociationMultiplicity $multiplicity)
	{
		// resolve order:
		// {$typeName} : IHandled (--> IHandled::getHandler())
		// {$typeName} : IBoxed (--> ObjectPropertyType)
		// {$typeName} : IDaoRelated
		// {$typeName} : IOrmRelated
		// ... not implemented yet. Possibly create an enumeration

		if (class_exists($name, true)) {
			$interfaces = class_implements($name);

			// type resolves handler by itself
			if (in_array('IHandled', $interfaces)) {
				return call_user_func(
					array(
						$name, 'getHandler'
					),
					$multiplicity
				);
			}
			// type can be wrapped automatically
			else if (in_array('IBoxed', $interfaces)) {
				return new ObjectPropertyType(
					$name,
					/* defaultValue */ null,
					/* isNullable */ $multiplicity->is(AssociationMultiplicity::ZERO_OR_ONE)
				);
			}
			// 1:1 entity mapping using the identifier
			else if (
					in_array('IDaoRelated', $interfaces)
					&& call_user_func(array($name, 'orm'))->hasDao()
				) {
				return new AssociationPropertyType(
				// new EntityWrap('EntityName'), EntityWrap implements IQueryable
					call_user_func(
						array($name, 'orm')
					),
					$multiplicity,
					AssociationBreakAction::cascade()
				);
			}
			// entity injection
			else if (in_array('IOrmRelated', $interfaces)) {
				Assert::notImplemented(
					'for now only identifiable DAOized entities can be referenced; %s is daoless or (and) identifierless entity',
					$name
				);
			}
			// nothing for now
			else {
				throw new OrmModelDefinitionException('handle type failure (failed '.$name.' type)');
			}
		}
		else {
			try {
				$class = $this->ormDomain->getClass($name);
			}
			catch (OrmModelIntegrityException $e) {
//				$this->recorder->putError(' failed.');
				throw $e;
			}

			if ($class->hasDao() && $class->getIdentifier()) {
				// map via db foreign key
				return new AssociationPropertyType(
					$class,
					$multiplicity,
					AssociationBreakAction::cascade()
				);
			}
			else {
				// map via injecting the fields
				// currently unsupported. Needed to implement EntityPropertyType
				Assert::notImplemented(
					'for now only identifiable DAOized entities can be referenced; %s is daoless or (and) identifierless entity',
					$name
				);
			}
		}
	}

	/**
	 * @return OrmProperty
	 */
	private function resolveContainerType()
	{
		Assert::notImplemented('containers are not implemented for now');
	}

	/**
	 * @return void
	 */
	private function dispose()
	{
		$this->xmlElement = null;
	}

	/**
	 * sxml requires strict unix-like path, so it should be fixed manually within xml
	 * @return string
	 */
	private function fixDtdPath($xmlContents)
	{
		// case when no !DOCTYPE inside xml
		if (false === strpos($xmlContents, '!DOCTYPE')) {
			$xmlContents = preg_replace(
				'/^
					\s*
					<\?xml.*\?>
				/sxU',
				'$0<!DOCTYPE ' . self::DTD_ROOT_ELEMENT_NAME . ' SYSTEM "' . $this->dtdFilename . '">',
				$xmlContents,
				1
			);
		}
		else {
			$xmlContents = preg_replace(
				'/
					<\s*
						!DOCTYPE \s+
						' . self::DTD_ROOT_ELEMENT_NAME . '
						(?: \s+
							SYSTEM \s+
							[\'"][^\'"]*[\'"]
						)? \s*
					>
				/sx',
				'<!DOCTYPE ' . self::DTD_ROOT_ELEMENT_NAME . ' SYSTEM "' . $this->dtdFilename . '">',
				$xmlContents,
				1
			);
		}

		return $xmlContents;
	}
}

?>