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
 * @ingroup Mvc
 */
abstract class Controller implements IController
{
	/**
	 * @var Model|null
	 */
	private $model = null;

	/**
	 * @return Model
	 */
	function getModel()
	{
		if (!$this->model) {
			$this->model = new Model();
		}

		return $this->model;
	}

	/**
	 * @return Controller an object itself
	 */
	function setModel(Model $model)
	{
		$this->model = $model;

		return $this;
	}
}

?>