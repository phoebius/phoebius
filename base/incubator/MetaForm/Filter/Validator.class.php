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
 * Represents a filter observer
 *
 */
class FilterContainer
{
	private $filters = array();
	private $errors;

	function addFilter($filter_id, IFilter $filter)
	{
		$this->filters[$filter_id] = $filter;
	}

	function dropFilter($filter_id)
	{
		unset($this->filters[$filter_id]);
	}

	function dropFilters()
	{
		$this->filters = array();
	}

	function validate($value)
	{
		$this->processFilters($value);
		return $this->hasErrors();
	}

	function getErrors()
	{
		if ($this->hasErrors())
		{
			return $this->errors;
		}
		else
		{
			return false;
		}
	}

	function hasErrors()
	{
		return sizeof($this->errors) > 0 ? true : false;
	}

	private function processFilters(&$value)
	{
		$this->errors = array();
		foreach($this->filters as $filter_id => $filter)
		{
			$result = $filter->filter($value);
			if (false === $result)
			{
				$this->errors[] = $filter_id;
			}
		}
	}
}
?>