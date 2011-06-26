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
 * Represents a scanner which accepts XML representation of ORM-related entity set and creates
 * and object representation of the entities (aka OrmClass)
 *
 * @aux
 * @ingroup Orm_Domain_Notation
 */
class XmlOrmDomainBuilder
{
	const DTD_BASE_PATH = '/share/domain.dtd';
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

	/**
	 * @param string $xmlFilename path to an XML file with the description of entity set
	 * @throws FileNotFoundException thrown when path is incorrect
	 */
	function __construct($xmlFilename)
	{
		Assert::isScalar($xmlFilename);

		if (!is_file($xmlFilename)) {
			throw new FileNotFoundException($xmlFilename);
		}

		$this->xmlFilename = $xmlFilename;
		$this->dtdFilename = str_replace('\\', '/', PHOEBIUS_BASE_ROOT . self::DTD_BASE_PATH);
	}

	/**
	 * Creates an object representation of ORM-related entites
	 *
	 * @throws OrmModeIntegritylException thrown when description has errors
	 * @return OrmDomain
	 */
	function build()
	{
		$this->ormDomain = new OrmDomain;
		$this->load();
		$this->generateDomain();
		
		$this->xmlElement = null;

		return $this->ormDomain;
	}

	/**
	 * @return void
	 */
	private function load()
	{
		$xmlContents = file_get_contents($this->xmlFilename);

		try {
			$this->xmlElement = new SimpleXMLElement(
				$this->fixDtdPath($xmlContents),
				LIBXML_DTDATTR | LIBXML_DTDLOAD | LIBXML_DTDVALID
			);
		}
		catch (ExecutionContextException $e) {
			$xmlError = libxml_get_last_error();
			throw new OrmModelIntegrityException(
				'Malformed XML: ' . $xmlError->message . ' in ' . $this->xmlFilename . ':' . $xmlError->line
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
	
	private $entitiesNodes = array();
	private $currentClass = null;

	/**
	 * @return void
	 */
	private function generateDomain()
	{
		if (isset($this->xmlElement['db-schema'])) {
			$this->ormDomain->setDbSchema((string) $this->xmlElement['db-schema']);
		}
		
		// scan entity nodes (expanding inclusions) 
		// and put them into internal hashmap
		foreach ($this->getChildNodeSet($this->xmlElement->entities, 'entity') as $entity) {
			$this->entitiesNodes[(string) $entity['name']] = $entity;
		}
		
		// then traverse over them, and build each recursively
		foreach (array_keys($this->entitiesNodes) as $name) {
			$this->importEntity($name);
		}
	}
	
	private function importEntity($name) {
		if (!isset($this->entitiesNodes[$name])) {
			return null;
		}
		
		if ($this->ormDomain->classExists($name)) {
			return $this->ormDomain->getClass($name);
		}
		
		$entity = $this->entitiesNodes[$name];
		$prevClass = $this->currentClass;
		$this->processingStack[$name] = $class = $this->currentClass = $this->generateEntity($entity);
		
		$this->ormDomain->addClass($class);
	
		// process an identifier (if specified). However, entity CAN BE identifierless
		if (isset($entity->properties->identifier)) {
			$id = $this->generateIdentifier($entity->properties->identifier);
			$class->setIdentifier($id);
		}
		
		foreach ($this->getChildNodeSet($entity->properties, 'property') as $property) {
			$property = $this->generateProperty($property);
			$class->addProperty($property);
		}
		
		foreach ($this->getChildNodeSet($entity->properties, 'container') as $container) {
			$container = $this->generateContainer($class, $container);
			$class->addProperty($container);
		}
		
		$this->currentClass = $prevClass;
		
		return $class;
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
		$type = $this->getPropertyType($xmlIdentifier, AssociationMultiplicity::exactlyOne());

		$identifier = new OrmProperty(
			(string) $xmlIdentifier['name'],
			isset($xmlIdentifier['db-columns'])
				? $this->getFields($xmlIdentifier['db-columns'])
				: $this->makeFields((string) $xmlIdentifier['name'], $type),
			$type,
			OrmPropertyVisibility::full(),
			AssociationMultiplicity::zeroOrOne(),
			false,
			true
		);

		return $identifier;
	}

	/**
	 * @return array
	 */
	private function getFields($fieldsList)
	{
		$fields = explode(' ', $fieldsList);
		$yield = array();
		foreach ($fields as $field) {
			$field = trim($field);
			if ($field) {
				$yield[] = $field;
			}
		}

		return $yield;
	}

	private function makeFields($propertyName, OrmPropertyType $ormPropertyTpe)
	{
		$fields = array();

		$propertyPrefix = strtolower(
			preg_replace(
				'/([a-z])([A-Z])/',
				'$1_$2',
				$propertyName
			)
		);

		foreach (array_keys($ormPropertyTpe->getSqlTypes()) as $key) {
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

	private function getTypeParameters(SimpleXMLElement $property)
	{
		$parameters = array();

		foreach ($property->attributes() as $key=>$value) {
			$parameters[$key] = $this->castMethodArg((string) $value);
		}

		foreach ($property->param as $param) {
			$parameters[(string) $param['name']] = $this->castMethodArg((string) $param['value']);
		}

		return $parameters;
	}

	/**
	 * @return OrmProperty
	 */
	private function generateProperty(SimpleXMLElement $xmlProperty)
	{
		$type = $this->getPropertyType($xmlProperty, new AssociationMultiplicity((string) $xmlProperty['multiplicity']));

		$property = new OrmProperty(
			(string) $xmlProperty['name'],
			isset($xmlProperty['db-columns'])
				? $this->getFields($xmlProperty['db-columns'])
				: $this->makeFields((string) $xmlProperty['name'], $type),
			$type,
			new OrmPropertyVisibility((string) $xmlProperty['visibility']),
			new AssociationMultiplicity((string) $xmlProperty['multiplicity']),
			$xmlProperty['unique'] == 'true',
			false,
			$xmlProperty['queryable'] == 'true'
		);

		return $property;
	}

	/**
	 * @return OrmProperty
	 */
	private function generateContainer(OrmClass $type, SimpleXMLElement $xmlContainer)
	{
		$referredTypeName = (string)$xmlContainer['type'];
		
		if (!$referredType = $this->importEntity($referredTypeName)) {
			if (@class_exists($referredTypeName, true) && TypeUtils::isChild($referredTypeName, 'IDaoRelated')) {
				$referredType = call_user_func(array($referredTypeName, 'orm'));
			}
			else {
				$referrer = $type->getName() .'.'. ((string) $xmlContainer['name']);
				throw new OrmModelIntegrityException($referrer . ' refers to unknown entity ' . $referredTypeName);
			}
		}

		try { // one-to-many
			$referredProperty = $referredType->getProperty((string)$xmlContainer['refs']);

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

				$this->ormDomain->addClass($proxy);
			}

			$propName = strtolower($type->getEntityName());
			try {
				$mtmType = new AssociationPropertyType(
					$type,
					AssociationMultiplicity::exactlyOne(),
					AssociationBreakAction::cascade()
				);
				$type_property = new OrmProperty(
					$propName,
					$this->makeFields($type->getTable(), $mtmType),
					$mtmType,
					OrmPropertyVisibility::full(),
					AssociationMultiplicity::exactlyOne()
				);

				$proxy->addProperty(
					$type_property
				);
			}
			catch (OrmModelIntegrityException $e) {
				$type_property = $proxy->getProperty($propName);
			}

			$propName = strtolower($referredType->getEntityName());
			try {
				$mtmType =
					new AssociationPropertyType(
						$referredType,
						AssociationMultiplicity::exactlyOne(),
						AssociationBreakAction::cascade()
					);
				$referredType_property = new OrmProperty(
					$propName,
					$this->makeFields($referredType->getTable(), $mtmType),
					$mtmType,
					OrmPropertyVisibility::full(),
					AssociationMultiplicity::exactlyOne()
				);

				$proxy->addProperty(
					$referredType_property
				);
			}
			catch (OrmModelIntegrityException $e) {
				$referredType_property = $proxy->getProperty($propName);
			}

			$propertyType = new ManyToManyContainerPropertyType(
				$proxy,
				$type_property,
				$referredType_property
			);
		}

		$property = new OrmProperty(
			(string) $xmlContainer['name'],
			array(),
			$propertyType,
			new OrmPropertyVisibility(OrmPropertyVisibility::READONLY),
			AssociationMultiplicity::zeroOrOne(),
			false
		);

		if (isset($xmlContainer['db-columns'])) {
			$property->setDBColumnNames(
				$this->getFields($xmlContainer['db-columns'])
			);
		}

		return $property;
	}

	/**
	 * Resolution order:
	 *  - IDaoRelated (check a class existance within the global scope and withing the domain scope) --> AssociationPropertyType
	 *  - IOrmRelated --> CompositionPropertyType
	 *  - IOrmPropertyAssignable --> IOrmPropertyAssignable::getOrmProperty()
	 *  - IBoxable --> BoxablePropertyType
	 *  - DBType
	 *  - any type with public ctor
	 * @return OrmPropertyType
	 */
	private function getPropertyType(SimpleXMLElement $element, AssociationMultiplicity $multiplicity)
	{
		$name = (string) $element['type'];
		$parameters = $this->getTypeParameters($element);
		
		if (($class = $this->importEntity($name))) { // force recursion
			if ($class->hasDao() && $class->getIdentifier()) {
				return new AssociationPropertyType(
					$class,
					$multiplicity,
					AssociationBreakAction::cascade()
				);
			}
			else {
				return new CompositePropertyType($class);
			}
		}
		else if (DBType::hasMember($name)) {
			$parameters['id'] = $name;

			if (!isset($parameters['nullable'])) {
				$parameters['nullable'] =
					$multiplicity->isNullable()
						? 'true'
						: 'false';
			}

			$dbType = $this->makeObject('DBType', $parameters);
			return $dbType->getOrmPropertyType();
		}
		else if (@class_exists($name, true)) {
			if (TypeUtils::isChild($name, 'IDaoRelated')) {
				return new AssociationPropertyType(
					call_user_func(array($name, 'orm')),
					$multiplicity,
					AssociationBreakAction::cascade()
				);
			}
			else if (TypeUtils::isChild($name, 'IOrmRelated')) {
				$orm = call_user_func(array($name, 'orm'));
				return new CompositePropertyType($orm);
			}
			else if (TypeUtils::isChild($name, 'IOrmPropertyAssignable')) {
				return
					call_user_func(
						array($name, 'getOrmPropertyType'),
						$multiplicity
					);
			}
			else if (TypeUtils::isChild($name, 'IBoxable')) {
				$parameters['id'] = DBType::VARCHAR;
				if (!isset($parameters['nullable'])) {
					$parameters['nullable'] =
						$multiplicity->isNullable()
							? 'true'
							: 'false';
				}

				return
					new BoxablePropertyType(
						$name,  $this->makeObject('DBType', $parameters)
					);
			}
			else if (TypeUtils::isChild($name, 'ISqlType')) { // for RawSqlType
				return new PrimitivePropertyType(
					$this->makeObject($name, $parameters)
				);
			}
			else if (TypeUtils::isChild($name, 'OrmPropertyType')) { // OrmPropertyType with public ctor
				return $this->makeObject($name, $parameters);
			}
		}

		throw new OrmModelIntegrityException(
			'Unknown type of ' . ((string) $element['name']) . ' property:  ' . $name
		);
	}

	private function makeObject($name, array $attributes)
	{
		$class = new ReflectionClass($name);

		$args = $this->getMethodArgs($class->getConstructor(), $attributes);

		return $class->newInstanceArgs($args);
	}

	private function getMethodArgs(ReflectionMethod $rm, array $attributes)
	{
		$yield = array();
		foreach ($rm->getParameters() as $parameter) {

			if (isset($attributes[$parameter->name])) {
				$yield[] = $this->castMethodArg($attributes[$parameter->name]);
			}
			else {
				// check for default values
				$yield[] = $this->getValue($parameter);
			}
		}

		return $yield;
	}

	private function castMethodArg($value)
	{
		switch (strtolower($value)) {
			case '':
			case 'null': return null;
			case 'true': return true;
			case 'false':return false;
			default: return $value;
		}
	}

	private function getValue(ReflectionParameter $rp)
	{
		if ($rp->isDefaultValueAvailable()) {
			return $rp->getDefaultValue();
		}
		else if ($rp->isArray()) {
			return array();
		}
		else if ($rp->allowsNull()) {
			return null;
		}
	}

	/**
	 * Xml requires strict unix-like path, so it should be fixed manually within xml
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