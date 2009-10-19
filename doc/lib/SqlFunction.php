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

// Using SqlFunction in different ways

// Call of simple function
// E.g.: get_user_by_id ( '1' )
$functionCall =
	SqlFunction::create('get_user_by_id')
		->addArg(new ScalarSqlValue(1));

// Call of aggregate function
// E.g.: count ( DISTINCT "id" )
$aggregateFunctionCall =
	SqlFunction::create('count')
		->addArg(new SqlColumn('id'))
		->aggregateDistinct();

// Call of aggregate function
// E.g.: count ( * )
$aggregateFunctionCall =
	SqlFunction::create('count')
		->aggregateWithNulls();

?>