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
 * Represents an database index.
 *
 * Index can be optinally named.
 *
 * @ingroup Dal_DB_Schema
 */
class DBIndex implements ISqlCastable
{
	/**
	 * @var string
	 */
	private $name;
	
	/**
	 * @var string
	 */
	private $type;
	
	/**
	 * @var array
	 */
	private $fields;
	
	/**
	 * @param array of string $fields
	 */
	function __construct(array $fields, $name = null, $type = null)
	{
		Assert::isNotEmpty($fields, 'index cannot be across zero fields');
		
		$this->fields = $fields;
		
		if ($name) {
			$this->setName($name);
		}
		
		$this->type = $type;
	}
	
	/**
	 * Gets the list of columns to be indexed
	 */
	function getFields()
	{
		return $this->fields;
	}

	/**
	 * Gets the name of the index
	 *
	 * @return string
	 */
	function getName()
	{
		return $this->name;
	}

	/**
	 * Gets the type of index
	 *
	 * @return string
	 */
	function getType()
	{
		return $this->type;
	}

	/**
	 * Sets the new name of the index
	 *
	 * @param string $name name of the index
	 *
	 * @return DBIndex itself
	 */
	function setName($name)
	{
		Assert::isScalar($name);

		$this->name = $name;

		return $this;
	}
	
	function toDialectString(IDialect $dialect)
	{
		return 
			'INDEX ' 
			. ($this->name ? $dialect->quoteIdentifier($this->getName()) . ' ' : '')
			. ($this->type ? $this->type . ' ' : '')
			. '(' . $this->getFieldsAsString($dialect) . ')';
	}
	
	/**
	 * Gets the list of covered fields as string
	 * @param IDialect $dialect
	 * @return string
	 */
	protected function getFieldsAsString(IDialect $dialect)
	{
		$fields = new SqlFieldArray($this->fields);
		
		return $fields->toDialectString($dialect);
	}
}

?>