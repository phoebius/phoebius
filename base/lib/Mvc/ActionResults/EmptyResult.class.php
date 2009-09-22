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
 * Represents an empty result
 * @ingroup ActionResults
 */
class EmptyResult implements IActionResult
{
	/**
	 * @return void
	 */
	function handleResult(IViewContext $context)
	{
		$context->getAppContext()->getResponse()->finish();
	}
}

?>