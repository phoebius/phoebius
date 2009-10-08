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
	private $mtm;

	/**
	 * @var IOrmProperty
	 */
	private $container;

	/**
	 * @var IOrmProperty
	 */
	private $encapsulant;

	function __construct(
			OrmClass $proxy,
			IOrmProperty $container,
			IOrmProperty $encapsulant
		)
	{
		$this->mtm = $proxy;
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
		return $this->mtm;
	}

	/**
	 * @return IOrmProperty
	 */
	function getContainerProxyProperty()
	{
		$this->container;
	}

	/**
	 * @return IOrmProperty
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