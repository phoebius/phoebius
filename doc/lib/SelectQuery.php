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

$selectQuery =
	SelectQuery::create()
		->get(
			SqlFunction::create('count')
				->aggregateDistinct()
				->addArg(new SqlColumn('sex')),
			'sexNumber'
		)
		->from('tableName')
		->groupBy(
			new SqlColumn('sex')
		)
		->having(
			Expression::eq(
				new SqlColumn('sex'),
				new ScalarSqlValue('male')
			)
		);

?>