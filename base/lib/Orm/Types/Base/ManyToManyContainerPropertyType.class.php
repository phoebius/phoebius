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
 * *:* relation implementation
 * @ingroup BaseOrmTypes
 */
class ManyToManyContainerPropertyType extends ContainerPropertyType
{
	/**
	 * @var OrmClass
	 */
	private $proxy;

	/**
	 * @var OrmProperty
	 */
	private $container;

	/**
	 * @var OrmProperty
	 */
	private $encapsulant;

	function __construct(
			OrmClass $proxy,
			OrmProperty $container,
			OrmProperty $encapsulant
		)
	{
		$this->proxy = $proxy;
		$this->container = $container;
		$this->encapsulant = $encapsulant;

		parent::__construct(
			$container->getType()->getContainer(),
			$encapsulant->getType()->getContainer()
		);
	}

	/**
	 * @return OrmClass
	 */
	function getProxy()
	{
		return $this->proxy;
	}

	/**
	 * @return OrmProperty
	 */
	function getContainerProxyProperty()
	{
		$this->container;
	}

	/**
	 * @return OrmProperty
	 */
	function getEncapsulantProxyProperty()
	{
		$this->encapsulant;
	}

	/**
	 * @return string
	 */
	function getImplClass()
	{
		return null;
	}
}

?>