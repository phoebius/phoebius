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
 * Represents an abstract dialect with type association implementation
 *
 * @ingroup Dal_DB
 */
abstract class Dialect implements IDialect
{
	/**
	 * @var array
	 */
	private $typeNames = array();

	/**
	 * @return string
	 */
	function getTypeRepresentation(DBType $type)
	{
		$names = $this->typeNames[$type->getValue()];

		return $this->getComputedTypeRepresentation($names, $type);
	}

	protected function getComputedTypeRepresentation($name, DBType $type)
	{
		if ($type->canHaveSize()) {
			return $this->computeSized($name, $type->isNullable(), $type->getSize());
		}
		else if ($type->canHavePrecision()) {
			return $this->computeDecimal($name, $type->isNullable(), $type->getPrecision(), $type->getScale());
		}
		else {
			return $this->compute($name, $type->isNullable());
		}
	}

	protected function compute($name, $nullable)
	{
		if (!$nullable) {
			$name .= ' NOT NULL';
		}

		return $name;
	}

	protected function computeSized($name, $nullable, $size = null)
	{
		if ($size) {
			$name .= '(' . $size . ')';
		}

		if (!$nullable) {
			$name .= ' NOT NULL';
		}

		return $name;
	}

	protected function computeDecimal($name, $nullable, $precision = null, $scale = null)
	{
		if ($precision) {
			$name .= '(' . $precision;

			if ($scale) {
				$name .= ',' . $scale;
			}

			$name .= ')';
		}

		if (!$nullable) {
			$name .= ' NOT NULL';
		}

		return $name;
	}

	/**
	 * @return Dialect itself
	 */
	protected function registerType($baseTypeId, $name)
	{
		$this->typeNames[$baseTypeId] = $name;

		return $this;
	}
}

?>