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
 * Container generator
 *
 * @ingroup Orm_Domain_CodeGenerator
 */
abstract class OrmContainerClassCodeConstructor extends OrmRelatedClassCodeConstructor
{
	/**
	 * @var OrmProperty
	 */
	protected $ormProperty;

	/**
	 * @var ContainerPropertyType
	 */
	protected $propertyType;

	/**
	 * @param OrmClass $ormClass object that represents a class to be generated
	 * @param OrmProperty $ormProperty property that implements a ContainerPropertyType
	 */
	function __construct(OrmClass $ormClass, OrmProperty $ormProperty)
	{
		$this->ormProperty = $ormProperty;
		$this->propertyType = $ormProperty->getType();

		Assert::isTrue(
			$this->propertyType instanceof ContainerPropertyType
		);

		parent::__construct($ormClass);
	}
}

?>