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
 * @ingroup OrmTypes
 */
abstract class OrmPropertyType implements IPropertyMappable, IPropertyStructurized
{
	private static $entityPropertyExpressionWorkers = array(
		ExpressionType::BETWEEN => 'BetweenEntityPropertyExpression',
		ExpressionType::BINARY => 'BinaryPropertyExpression',
		ExpressionType::IN_SET => 'InSetEntityPropertyExpression',
		ExpressionType::PREFIX_UNARY => 'PrefixUnaryEntityPropertyExpression',
		ExpressionType::UNARY_POSTFIX => 'UnaryPostfixPropertyExpression'
	);

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
	 * @return IEntityPropertyExpression
	 */
	function getEntityPropertyExpression(
			$table,
			OrmProperty $ormProperty,
			IExpression $expression
		)
	{
		Assert::isScalar($table);

		$entityPropertyExpressionClass = self::$entityPropertyExpressionWorkers[
			$expression->getExpressionType()->getValue()
		];

		return new $entityPropertyExpressionClass(
			$table,
			$ormProperty,
			$expression
		);
	}
}

?>