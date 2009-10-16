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
	 * @var IQueryable
	 */
	private $proxy;

	/**
	 * @var IOrmProperty
	 */
	private $container;

	/**
	 * @var IOrmProperty
	 */
	private $encapsulant;

	function __construct(
			IQueryable $proxy,
			IOrmProperty $container,
			IOrmProperty $encapsulant
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
	 * @return IQueryable
	 */
	function getProxy()
	{
		return $this->proxy;
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

	protected function getCtorArgumentsPhpCode()
	{
		return array(
			$this->mtm->getLogicalSchema()->getEntityName() . '::orm()',
			$this->getContainer()->getLogicalSchema()->getName() . '::orm()->getLogicalSchema()->getProperty(\'' . $this->container->getName() . '\')',
			$this->getEncapsulant()->getLogicalSchema()->getName() . '::orm()->getLogicalSchema()->getProperty(\'' . $this->encapsulant->getName() . '\')',
		);
	}
}

?>