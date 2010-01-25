<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright (c) 2009 Scand Ltd.
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
 * Contract for presentation logic encapsulator, used to render UIControl
 *
 * @ingroup UI_Presentation
 */
abstract class UIPresentation
{
	/**
	 * Renders the presentation logic to the source
	 *
	 * @param IOutput $output render destination
	 *
	 * @return void
	 */
	abstract function render(IOutput $output);
}

?>