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
 * Represents an layout
 *
 * Helper methods return strings, that are not appended to the output!
 *
 * See:
 * - View::__get() for shorthand access to view data variables came from the Controller
 *
 * Hints:
 * - use "@" operator to set whether view expects a specific view data variable or not:
 * 		@code
 * 		<?=@$this->var?>
 * 		@endcode
 * 		shows that a view can omit the situation when controller does not provide a "var" variable
 * 		within a passed view data.
 *
 * 		@code
 * 		<?=$this->var?>
 * 		@endcode
 * 		will raise a compilation error if the variable is missing
 *
 *
 * @ingroup UI_Mvc_Presentation
 */
class View
{
	private $name;
	private $data;
	
	/**
	 * @var View
	 */
	private $master;
	
	/**
	 * Name of the view. Should be accessible through include_path
	 * @param string $name name of a view
	 * @param array $data data to be passed to view
	 */
	function __construct($name, array $data = array())
	{
		Assert::isTrue(
			@fopen($name, "r", true), 
			'don`t know where view %s is located',
			$name
		);
		
		$this->name = $name;
		$this->data = $data;
	}

	/**
	 * A shorthand getter of the view data.
	 *
	 * If the variable is missing, and error_reporting is turned off (by prepending the call
	 * with the "@" operator) then the variable is treated as NULL. Otherwise a compilation
	 * error is raised
	 *
	 * @return mixed
	 */
	function __get($name)
	{
		Assert::isScalar($name);

		if (array_key_exists($name, $this->data)) {
			return $this->data[$name];
		}
		else if (!error_reporting()) {
			$this->data[$name] = null;

			return null;
		}
		else {
			Assert::isUnreachable(
				'unknown view data `%s` expected within %s view',
				$name, $this->name
			);
		}
	}

	/**
	 * A shorthand check whether the variable is defined within the view data
	 *
	 * @return boolean
	 */
	function __isset($name)
	{
		Assert::isScalar($name);

		return array_key_exists($name, $this->data);
	}

	/**
	 * Renders the view and returns the resulting string
	 * @return string
	 */
	function render()
	{
		ob_start();
		include ($this->name);
		$content = ob_get_clean();
		
		if ($this->master) {
			$this->master->innerContent = $content;
			$content = $this->master->render();
		}
		
		return $content;
	}

	/**
	 * Sets the master view
	 * @return void
	 */
	function useMasterView($masterViewName, array $data = array())
	{
		$this->master = $this->spawn($masterViewName, $data);
	}

	/**
	 * Renders a partial view 
	 */
	function renderPartial($viewName, array $data = array())
	{
		return 
			$this->spawn($viewName, $data)
				->render();
	}

	/**
	 * Copies the view
	 * @return View
	 */
	function spawn($view, array $data = array())
	{
		$class = get_class($this);
		return new $class ($view, array_replace($this->data, $data));
	}
}

?>