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
 * @ingroup PhpLayout
 */
class ApplicationMasterPageView extends PhpMasterPageView
{
	const VIEW_EXTENSION = '.view.php';

	function __construct($viewName)
	{
		parent::__construct(
			APP_ROOT . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . $viewName . self::VIEW_EXTENSION
		);
	}
}

?>