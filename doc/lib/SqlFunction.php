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