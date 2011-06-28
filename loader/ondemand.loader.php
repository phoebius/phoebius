<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright (c) 2011 Scand Ltd.
 *
 * This program is free software; you can redistribute it and/or modify it under the terms
 * of the GNU Lesser General Public License as published by the Free Software Foundation;
 * either version 3 of the License, or (at your option) any later version.
 *
 * You should have received a copy of the GNU Lesser General Public License along with
 * this program; if not, see <http://www.gnu.org/licenses/>.
 *
 ************************************************************************************************/

function __autoload($class) 
{
	try {
		include $class . PHOEBIUS_TYPE_EXTENSION;
	}
	catch (Exception $e) {
		$message = sprintf(
			'Exception thrown when autoloading %s from %s:%s: %s',
			$class, $e->getFile(), $e->getLine(), $e->getMessage()
		);
		
		throw new Exception($message);
	}
}
