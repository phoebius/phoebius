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
 * Represents an extended version of presentation engine which is binded with UIControl objects
 * and MVC stack and provides various features of it with the protected methods
 *
 * Helper methods return strings, that are not appended to the output!
 *
 * See:
 * - UIViewPresentation::__get() for shorthand access to view data variables came from the Controller
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
class UIViewPresentation
		extends UIPhpLayoutPresentation
		implements IUIControlBindedPresentation
{
	/**
	 * @var UIControl|null
	 */
	protected $control;

	/**
	 * @var array of IOutput
	 */
	private $outputBuffers = array();

	/**
	 * @var IOutput
	 */
	private $output;

	private $viewName;
	
	/**
	 * @var ViewData
	 */
	protected $viewData;

	/**
	 * @param string $viewName name of the view located at PHOEBIUS_APP_VIEWS_ROOT/<viewName>.view.php
	 */
	function __construct($viewName, ViewData $viewData = null)
	{
		Assert::isScalar($viewName);

		$this->viewName = $viewName;
		$this->viewData = $viewData;

		parent::__construct(
			PHOEBIUS_APP_VIEWS_ROOT . DIRECTORY_SEPARATOR
			. $viewName . PHOEBIUS_VIEW_EXTENSION
		);
	}

	/**
	 * A shorthand setter of view data variable
	 *
	 * @return void
	 */
	function __set($name, $value)
	{
		Assert::isScalar($name);

		$this->viewData[$name] = $value;
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

		if (array_key_exists($name, $this->viewData->toArray())) {
			return $this->viewData[$name];
		}
		else if (!error_reporting()) {
			$this->viewData[$name] = null;

			return null;
		}
		else {
			Assert::isUnreachable(
				'unknown view data `%s` expected within %s view',
				$name, $this->viewName
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

		return isset($this->viewData[$name]);
	}

	/**
	 * Looks up for the method in the binded UIControl hierarchy, and invokes the first matched
	 * one.
	 *
	 * @param string $name name of the invoked method
	 * @param array $arguments arguments to pass
	 *
	 * @return mixed
	 */
	function __call($name, array $arguments)
	{
		if ($this->control) {
			$object = $this->findMethod($this->control, $name);
			if ($object) {
				return call_user_func_array(array($object, $name), $arguments);
			}
		}

		Assert::isUnreachable('unknown method %s', $name);
	}

	/**
	 * @return object
	 */
	private function findMethod(UIControl $control, $name)
	{
		if (method_exists($control, $name)) {
			return $control;
		}

		$parent = $control->getParentControl();

		if ($parent && ($found = $this->findMethod($parent, $name))) {
			return $found;
		}
	}

	/**
	 * Forces the view to check whether the specified variables are presented in a view data
	 * passed by the controller
	 *
	 * @warning obsoleted wrt UIViewPresentation::__get()
	 *
	 * @var string ...
	 */
	protected function expect()
	{
		$vars = func_get_args();

		foreach ($vars as $var) {
			Assert::isTrue(
				isset($this->viewData[$var]),
				'%s expected but not found a view data of %s',
				$var, $this->viewName
			);
		}
	}

	/**
	 * Tells the view to watch for variables that MAY come from the controller within the view data
	 *
	 * @warning obsoleted wrt UIViewPresentation::__get()
	 *
	 * @var string ...
	 */
	protected function accept()
	{
		$vars = func_get_args();

		foreach ($vars as $var) {
			if (!isset($this->viewData[$var])) {
				$this->viewData[$var] = null;
			}
		}
	}

	function render(IOutput $output)
	{
		$this->output = $output;

		parent::render($output);

		$this->output = null;
	}

	function setUIControl(UIControl $control)
	{
		$this->control = $control;

		return $this;
	}

	function getUIControl()
	{
		return $this->control;
	}

	/**
	 * In-place render of a separate UIControl
	 *
	 * @return void
	 */
	protected function renderPartial($view, ViewData $viewData = null)
	{
		$this->assertInsideRenderingContext();

		$presentation = $this->spawn($view, $viewData);
		$control = $this->getUserControl($presentation);

		if ($this->control) {
			$control->setParentControl($this->control);
		}

		$presentation->setUIControl($control);

		// this is not good to handle output buffering cause the actual implemention
		// of output buffering can be unknown in the internals of a base class but
		// this is the only way to implement renderPartial() and we know explicilty how
		// is the base class implemented
		$this->output->write(ob_get_clean());
		ob_start();

		$control->render(
			$this->output
		);
	}

	/**
	 * Sets the master control
	 *
	 * @return void
	 */
	protected function setMaster($view, ViewData $viewData = null)
	{
		$this->assertInsideRenderingContext();

		$presentation = $this->spawn($view, $viewData);
		$masterPage = $this->getMasterPage($presentation);

		if ($this->control) {
			Assert::isTrue(
				$this->control instanceof UIPage,
				'masterpage can be set for UIPage only, %s given',
				get_class($this->control)
			);

			$this->control->setMasterPage($masterPage);
		}

		$presentation->setUIControl($masterPage);
	}

	/**
	 * Creates an instance of (a custom) UIPage with UIPresentation inside
	 *
	 * @param UIPresentation $presentation presentation to set; if not specified then the current
	 * 										context will be used
	 *
	 * @return UIPage
	 */
	protected function getPage(UIPresentation $presentation = null)
	{
		return new UIPage(
			$presentation
				? $presentation
				: $this
		);
	}

	/**
	 * Creates an instance of (a custom) UIMasterPage with UIPresentation inside
	 *
	 * @param UIPresentation $presentation presentation to set; if not specified then the current
	 * 										context will be used
	 * @return UIMasterPage
	 */
	protected function getMasterPage(UIPresentation $presentation = null)
	{
		return new UIMasterPage(
			$presentation
				? $presentation
				: $this
		);
	}

	/**
	 * Creates an instance of (a custom) UIUserControl with UIPresentation inside
	 *
	 * @param UIPresentation $presentation presentation to set; if not specified then the current
	 * 										context will be used
	 * @return UIUserControl
	 */
	protected function getUserControl(UIPresentation $presentation = null)
	{
		return new UIUserControl(
			$presentation
				? $presentation
				: $this
		);
	}

	/**
	 * @return UIViewPresentation
	 */
	private function spawn($view, ViewData $viewData = null)
	{
		return new self ($view, $viewData ? $viewData : $this->viewData);
	}
}

?>