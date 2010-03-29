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
 * *:* relation implementation
 * @ingroup Orm_Types
 */
class ManyToManyContainerPropertyType extends ContainerPropertyType
{
	/**
	 * @var IQueryable
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

	/**
	 * @param IQueryable $proxy
	 * @param OrmProperty $container
	 * @param OrmProperty $encapsulant
	 */
	function __construct(
			IQueryable $proxy,
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
	 * @return IQueryable
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
		return $this->container;
	}

	/**
	 * @return OrmProperty
	 */
	function getEncapsulantProxyProperty()
	{
		return $this->encapsulant;
	}

	protected function getCtorArgumentsPhpCode()
	{
		$proxyName = $this->proxy->getLogicalSchema()->getEntityName();

		return array(
			$proxyName . '::orm()',
			$proxyName . '::orm()->getLogicalSchema()->getProperty(\'' . $this->container->getName() . '\')',
			$proxyName . '::orm()->getLogicalSchema()->getProperty(\'' . $this->encapsulant->getName() . '\')',
		);
	}
}

?>