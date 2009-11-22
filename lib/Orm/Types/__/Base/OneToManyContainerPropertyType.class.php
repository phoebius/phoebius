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
 * 1:* relation implementation
 * @ingroup Orm_Types
 */
class OneToManyContainerPropertyType extends ContainerPropertyType
{
	/**
	 * @var OrmProperty
	 */
	private $encapsulantProperty;

	function __construct(
			IQueryable $container,
			IQueryable $encapsulant,
			OrmProperty $encapsulantProperty
		)
	{
		$this->encapsulantProperty = $encapsulantProperty;

		parent::__construct($container, $encapsulant);
	}

	/**
	 * @return OrmProperty
	 */
	function getEncapsulantProperty()
	{
		return $this->encapsulantProperty;
	}

	protected function getCtorArgumentsPhpCode()
	{
		return array(
			$this->getContainer()->getLogicalSchema()->getEntityName() . '::orm()',
			$this->getEncapsulant()->getLogicalSchema()->getEntityName() . '::orm()',
			$this->getEncapsulant()->getLogicalSchema()->getName() . '::orm()->getLogicalSchema()->getProperty(\'' . $this->encapsulantProperty->getName() . '\')',
		);
	}
}

?>