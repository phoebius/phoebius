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
 * Represents an extended version of presentation engine which is binded with UIControl objects
 * and MVC stack and provides various features of it with the protected methods
 *
 * Helper methods return strings, that are not appended to the output!
 *
 * See:
 * - UIViewPresentation::__get() for shorthand access to model variables came from the Controller
 * - UIViewPresentation::getSelfHref() for getting the requested URL
 * - UIViewPresentation::getHref() for generating URL via the route defined by the IRouteTable
 * - UIViewPresentation::getHtmlLink() for rendering <a href> container
 *
 * Hints:
 * - use "@" operator to set whether view expects a specific model variable or not:
 * 		@code
 * 		<?=@$this->var?>
 * 		@endcode
 * 		shows that a view can omit the situation when controller does not provide a "var" variable
 * 		within a passed model.
 *
 * 		@code
 * 		<?=$this->var?>
 * 		@endcode
 * 		will raise a compilation error if the variable is missing
 * - use getHref() to generate URL defined by the routing system:
 * 		@code
 * 		<?=$this->getHref("blog_entry", array("id" => $entry->getId()))?>
 * 		@endcode
 *
 *
 * @ingroup UI_Mvc_Presentation
 */
class UIViewPresentation
		extends UIPhpLayoutPresentation
		implements IUIControlBindedPresentation
{
	const VIEW_EXTENSION = '.view.php';

	/**
	 * @var UIControl|null
	 */
	protected $control;

	/**
	 * @var Model|null
	 */
	protected $model;

	/**
	 * @var Trace|null
	 */
	protected $trace;

	/**
	 * @var IRouteTable|null
	 */
	protected $routeTable;

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
	 * @param string $viewName name of the view located at $app_dir/views/<viewName>.view.php
	 */
	function __construct($viewName)
	{
		Assert::isScalar($viewName);

		$this->viewName = $viewName;

		parent::__construct(
			APP_ROOT . DIRECTORY_SEPARATOR
			. 'views' . DIRECTORY_SEPARATOR
			. $viewName . self::VIEW_EXTENSION
		);
	}

	/**
	 * A shorthand setter of the model variable
	 *
	 * @return void
	 */
	function __set($name, $value)
	{
		Assert::isScalar($name);

		$this->model[$name] = $value;
	}

	/**
	 * A shorthand getter of the model variables.
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

		if (array_key_exists($name, $this->model->toArray())) {
			return $this->model[$name];
		}
		else if (!error_reporting()) {
			$this->model[$name] = null;

			return null;
		}
		else {
			Assert::isUnreachable(
				'unknown model variable %s expected within %s view',
				$name, $this->viewName
			);
		}
	}

	/**
	 * A shorthand check whether the variable is defined within the model
	 *
	 * @return boolean
	 */
	function __isset($name)
	{
		Assert::isScalar($name);

		return isset($this->model[$name]);
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
	 * Gets the string representation of the URL constructed via the named Route that
	 * is defined within IRouteTable.
	 *
	 * Table is hold by the Trace.
	 *
	 * @param string $routeName name of the route
	 * @param array $parameters parameters to pass to Route's rules
	 *
	 * @return string
	 */
	function getHref($routeName, array $parameters = array())
	{
		// TODO: Trace can be not set when used in MVCless environments => force setting
		// the base SiteUrl explicitly like Trace and IRouteTable are set

		if ($this->trace) {
			$url = $this->trace->getWebContext()->getRequest()->getHttpUrl()->spawnBase();

			$this->trace
				->getRouteTable()
				->getRoute($routeName)
				->compose($url, $parameters);
		}
		else {
			Assert::isNotEmpty($this->routeTable, 'routeTable is not set');

			$url = new SiteUrl;

			$this
				->routeTable
				->getRoute($routeName)
				->compose($url, $parameters);

			// TODO: check whether formed url has the same schema, host and port as the requested
			// and strip those unused chunks leaving the URI only
		}

		return (string) $url;
	}

	/**
	 * Gets the text representation of the <A HREF> HTML tag.
	 * URL is constructed via the named Route that is defined within IRouteTable.
	 *
	 * @param string $href the contents of the tag
	 * @param string $routeName name of the route
	 * @param array $parameters parameters to pass to Route's rules
	 * @return string
	 */
	function getHtmlLink($href, $routeName, array $parameters = array())
	{
		return
			'<a href="'
				. $this->getHref($routeName, $parameters)
			. '">'
			. $href
			. '</a>';
	}

	/**
	 * Gets the text representastion of the requested URL
	 *
	 * @return string
	 */
	function getSelfHref()
	{
		Assert::isNotEmpty($this->trace, 'trace is not set');

		return $this->trace->getWebContext()->getRequest()->getHttpUrl()->getUri();
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
	 * Gets the model passed by the controller
	 *
	 * @return Model|null
	 */
	function getModel()
	{
		return $this->model;
	}

	/**
	 * Gets the trace used to invoke the MVC stack
	 *
	 * @return Trace|null
	 */
	function getTrace()
	{
		return $this->trace;
	}

	/**
	 * Gets the route table used to find the trace to the MVC controller
	 *
	 * @return IRouteTable|null
	 */
	function getRouteTable()
	{
		return $this->routeTable;
	}

	/**
	 * Sets the controller's model
	 *
	 * @param Model $model
	 *
	 * @return UIViewPresentation
	 */
	function setModel(Model $model)
	{
		$this->model = $model;

		return $this;
	}

	/**
	 * Sets the trace used to invoke the MVC stack
	 *
	 * @param Trace $trace
	 *
	 * @return UIViewPresentation
	 */
	function setTrace(Trace $trace)
	{
		$this->trace = $trace;

		if (!$this->routeTable) {
			$this->routeTable = $trace->getRouteTable();
		}

		return $this;
	}

	/**
	 * Sets the route table used to find the trace to the MVC controller
	 *
	 * @param IRouteTable $routeTable
	 *
	 * @return UIViewPresentation
	 */
	function setRouteTable(IRouteTable $routeTable)
	{
		$this->routeTable = $routeTable;

		return $this;
	}

	/**
	 * Forces the view to check whether the specified variables are presented in a Model
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
				isset($this->model[$var]),
				'%s expected but not found a model of %s',
				$var, $this->viewName
			);
		}
	}

	/**
	 * Tells the view to watch for variables that MAY come from the controller within the model
	 *
	 * @warning obsoleted wrt UIViewPresentation::__get()
	 *
	 * @var string ...
	 */
	protected function accept()
	{
		$vars = func_get_args();

		foreach ($vars as $var) {
			if (!isset($this->model[$var])) {
				$this->model[$var] = null;
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
	 * @param string $view name of the view to render
	 * @param Model $model optional custom model to pass; if not set, the current model will be passed
	 *
	 * @return void
	 */
	protected function renderPartial($view, Model $model = null)
	{
		$this->assertInsideRenderingContext();

		$presentation = $this->spawn($view, $model);
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
	protected function setMaster($view, Model $model = null)
	{
		$this->assertInsideRenderingContext();

		$presentation = $this->spawn($view, $model);
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
	private function spawn($view, Model $model = null)
	{
		$presentation = new self ($view);

		$presentation->trace = $this->trace;
		$this->routeTable = $this->routeTable;
		$presentation->model =
			$model
				? $model
				: $this->model;

		return $presentation;
	}
}

?>