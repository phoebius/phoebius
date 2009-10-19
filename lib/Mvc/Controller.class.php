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