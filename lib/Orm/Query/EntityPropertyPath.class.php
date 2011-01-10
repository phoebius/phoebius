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
 * @aux
 * @ingroup Orm_Query_Builder
 */
final class EntityPropertyPath
{
	private $path;
	private $passed = array();
	private $left = array();
	
	function __construct($path, EntityQueryBuilder $eqb)
	{
		$this->path = $path;
		$this->eqb = $eqb;
		$this->left = explode(".", $path);
		
		$this->move();
	}
	
	function getEntityQueryBuilder()
	{
		return $this->eqb;
	}
	
	function isEmpty()
	{
		return empty($this->left);
	}
	
	function peek(EntityQueryBuilder $eqb = null)
	{
		Assert::isFalse($this->isEmpty(), '%s is empty', $this->path);
		
		$me = clone $this;
		
		$me->move();
		
		if ($eqb) {
			$me->eqb = $eqb;
		}
		
		return $me;
	}
	
	private function move()
	{
		$this->passed[] = array_shift($this->left);
	}
	
	function getCurrentChunk()
	{
		return end($this->passed);
	}
	
	function getNextChunk()
	{
		return reset($this->left);
	}
	
	function getCurrentPath()
	{
		return join(".", $this->passed);
	}
	
	function getFullPath()
	{
		return $this->path;
	}
}

?>