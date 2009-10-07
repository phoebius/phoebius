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
 * @ingroup OrmExpression
 */
abstract class SingleRowEntityPropertyExpression extends EntityPropertyExpression
{
	/**
	 * @return IDalExpression
	 */
	protected function getSqlColumn()
	{
		$columns = $this->getEntityProperty()->getDbColumns();

		Assert::isTrue(sizeof($columns) == 1);

		return new SqlColumn(
			reset($columns),
			$this->getTable()
		);
	}

	/**
	 * @return ISqlValueExpression
	 */
	protected function getSqlValue($value)
	{
		if ($value instanceof ISqlValueExpression) {
			return $value;
		}

		if ($value instanceof EntityProperty) {
			return reset($value->getSqlColumns());
		}

		return reset($this->makeRawValue($value));
	}
}

?>