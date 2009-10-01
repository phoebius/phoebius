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
	private static $entityExpressionWorkers = array(
		ExpressionType::BETWEEN => 'BetweenEntityExpression',
		ExpressionType::BINARY => 'BinaryExpression',
		ExpressionType::IN_SET => 'InSetEntityExpression',
		ExpressionType::PREFIX_UNARY => 'PrefixUnaryEntityExpression',
		ExpressionType::UNARY_POSTFIX => 'UnaryPostfixExpression'
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
	 * @return IEntityExpression
	 */
	function getEntityExpression(
			$table,
			OrmProperty $ormProperty,
			IExpression $expression
		)
	{
		Assert::isScalar($table);

		$entityExpressionClass = self::$entityExpressionWorkers[
			$expression->getExpressionType()->getValue()
		];

		return new $entityExpressionClass(
			$table,
			$ormProperty,
			$expression
		);
	}
}

?>