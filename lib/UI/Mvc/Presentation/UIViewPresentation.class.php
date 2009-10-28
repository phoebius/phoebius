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
 * @ingroup UI_Mvc_Presentation
 */
class UIViewPresentation
		extends UIPhpLayoutPresentation
		implements IUIControlBindedPresentation
{
	const VIEW_EXTENSION = '.view.php';

	/**
	 * @var UIControl
	 */
	private $control;

	/**
	 * @var Model
	 */
	private $model;

	/**
	 * @var array of IOutput
	 */
	private $outputBuffers = array();

	/**
	 * @var IOutput
	 */
	private $output;

	/**
	 * @return UIPage
	 */
	static function view($view, Model $model = null)
	{
		$presentation = new self ($view);

		$page = $presentation->getPage();

		$presentation->control = $page;
		$presentation->model = $model;

		return $page;
	}

	function __construct($viewName)
	{
		parent::__construct(
			APP_ROOT . DIRECTORY_SEPARATOR
			. 'views' . DIRECTORY_SEPARATOR
			. $viewName . self::VIEW_EXTENSION
		);
	}

	/**
	 * @return array
	 */
	function getVariables(array $vars)
	{
		$yield = array();
		foreach ($vars as $var) {
			if ($this->model) {
				try {
					$yield[$var] = $this->model->getValue($var);
				}
				catch (ArgumentException $e) {
					$yield[$var] = null;
				}
			}
		}

		return $yield;
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
		if ($model) {
			$presentation->model = $model;
		}

		return $presentation;
	}
}

?>