<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright (c) 2011 Scand Ltd.
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
 * Implements a propery path traverser
 * 
 * @aux
 * @ingroup Orm_Query_Builder
 */
final class EntityPropertyPath
{
	private $path;
	private $passed = array();
	private $left = array();
	
	/**
	 * @param string $path comma-separated property path
	 * @param EntityQueryBuilder $eqb object that encapsulates an entity as a start point of the path
	 */
	function __construct($path, EntityQueryBuilder $eqb)
	{
		$this->path = $path;
		$this->eqb = $eqb;
		$this->left = explode(".", $path);
		
		$this->move();
	}
	
	/**
	 * Returns an object that encapsulates an entity as a start point of the path
	 * 
	 * @return EntityQueryBuilder 
	 */
	function getEntityQueryBuilder()
	{
		return $this->eqb;
	}
	
	/**
	 * Determines whether we reached the tail of the path
	 * @return boolean
	 */
	function isEmpty()
	{
		return empty($this->left);
	}
	
	/**
	 * Moves the pointer to the next chunk of the path, and returns the new copy of it with
	 * the new start point.
	 *
	 * You can specify another object that that encapsulates an entity as a new point of the path
	 * 
	 * @param EntityQueryBuilder object that that encapsulates an entity as a new point of the path
	 * 
	 * @return PropertyPath
	 */
	function peek(EntityQueryBuilder $eqb = null)
	{
		$me = clone $this;
		
		$me->move();
		
		if ($eqb) {
			$me->eqb = $eqb;
		}
		
		return $me;
	}
	
	/**
	 * Returns the current chunk of the path
	 * 
	 * @return string
	 */
	function getCurrentChunk()
	{
		return end($this->passed);
	}
	
	/**
	 * Returns the next chunk of the path
	 * 
	 * @return string
	 */
	function getNextChunk()
	{
		return reset($this->left);
	}
	
	/**
	 * Returns the string representation of the path from the beginning till the current point
	 * 
	 * @return string
	 */
	function getCurrentPath()
	{
		return join(".", $this->passed);
	}
	
	function getFullPath()
	{
		return $this->path;
	}
	
	private function move()
	{
		Assert::isFalse($this->isEmpty(), 'property path %s reached its tail', $this->path);
		
		$this->passed[] = array_shift($this->left);
	}
}

?>