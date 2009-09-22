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
interface IPhpViewDispatcher
{
	/**
	 * @throws ArgumentException
	 * @param string $viewName
	 * @return PhpControlView
	 */
	function getControl($viewName);

	/**
	 * @throws ArgumentException
	 * @param string $viewName
	 * @return PhpContentPageView
	 */
	function getContentPage($viewName);

	/**
	 * @throws ArgumentException
	 * @param string $viewName
	 * @return PhpMasterPageView
	 */
	function getMasterPage($viewName);
}

?>