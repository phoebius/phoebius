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
 * TODO cut view resolver functionality into a separate level
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
	 * @var array of IOutput
	 */
	private $outputBuffers = array();

	/**
	 * @var IOutput
	 */
	private $output;

	private $viewName;

	/**
	 * @return UIPage
	 */
	static function view($view, Model $model = null)
	{
		$presentation = new self ($view);

		$page = $presentation->getPage();

		$presentation->control = $page;
		$presentation->model =
			$model
				? $model
				: new Model;

		return $page;
	}

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
	 * @return void
	 */
	function __set($name, $value)
	{
		Assert::isScalar($name);

		$this->model[$name] = $value;
	}

	/**
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
	 * @return boolean
	 */
	function __isset($name)
	{
		Assert::isScalar($name);

		return isset($this->model[$name]);
	}

	/**
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

	function getModel()
	{
		return $this->model;
	}

	/**
	 * @var string ...
	 */
	function expect()
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
	 * @var string ...
	 */
	function accept()
	{
		$vars = func_get_args();

		foreach ($vars as $var) {
			if (!isset($this->model[$var])) {
				$this->model[$var] = null;
			}
		}
	}

	/**
	 * @return void
	 */
	function render(IOutput $output)
	{
		$this->output = $output;

		parent::render($output);

		$this->output = null;
	}

	/**
	 * @return UIViewPresentation
	 */
	function setUIControl(UIControl $control)
	{
		$this->control = $control;

		return $this;
	}

	/**
	 * @return UIControl|null
	 */
	function getUIControl()
	{
		return $this->control;
	}

	/**
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
		$presentation->model =
			$model
				? $model
				: $this->model;

		return $presentation;
	}
}

?>