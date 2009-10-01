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
abstract class SingleRowEntityExpression extends EntityExpression
{
	/**
	 * @return IDalExpression
	 */
	protected function getSqlColumn()
	{
		$columns = $this->getProperty()->getDbColumns();
		reset($columns);

		Assert::isTrue(sizeof($columns) == 1);

		return new SqlColumn(
			key($columns),
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

		return reset($this->makeRawValue($value));
	}
}

?>