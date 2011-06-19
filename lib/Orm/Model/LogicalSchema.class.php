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
 * Defines an interface for accessing logical schema information
 * @ingroup Orm_Model
 */
abstract class LogicalSchema implements ILogicallySchematic
{	
	function getEntityProperty(EntityPropertyPath $path)
	{
		return $this->getProperty($path->getCurrentChunk())->getEntityProperty($path);
	}
}

?>