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
 * @ingroup Orm_Types
 */
abstract class OrmPropertyType implements IPropertyMappable, IPropertyStructurized
{
	/**
	 * @return string
	 */
	abstract function getImplClass();

	/**
	 * @return mixed
	 */
	function getDefaultValue()
	{
		Assert::isUnreachable('no default value');
	}

	/**
	 * @return boolean
	 */
	function hasDefaultValue()
	{
		return false;
	}

	/**
	 * @return string
	 */
	function toPhpCodeCall()
	{
		return join('', array(
			'new ',
			get_class($this),
			'(',
			join(',', $this->getCtorArgumentsPhpCode()),
			')'
		));
	}

	protected function getCtorArgumentsPhpCode()
	{
		return array();
	}
}

?>