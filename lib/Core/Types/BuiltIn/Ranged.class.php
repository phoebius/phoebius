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
 * @ingroup Core_Types_BuiltIn
 */
abstract class Ranged extends Decimal
{
	/**
	 * @return integer
	 */
	abstract protected function getMin();

	/**
	 * @return integer
	 */
	abstract protected function getMax();

	/**
	 * @return BuiltInType
	 */
	function setValue($value)
	{
		$this->checkLimits($value);

		return parent::setValue($value);
	}

	/**
	 * @return boolean
	 */
	protected function checkLimits($value)
	{
		Assert::isTrue($this->getMin() < $this->getMax());

		if ($this->getMin() > $value) {
			throw new TypeCastException($this, $value, 'value is out of range');
		}

		if ($this->getMax() < $value) {
			throw new TypeCastException($this, $value, 'value is out of range');
		}
	}
}

?>