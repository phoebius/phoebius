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
 * $_SERVER-compatible dictionary
 * @ingroup Web
 */
class WebRequestDictionary extends HttpUrlDictionary
{
	const PROTOCOL = 'SERVER_PROTOCOL';
	const REQUEST_METHOD = 'REQUEST_METHOD';
	const HTTP_REFERER = 'HTTP_REFERER';

	/**
	 * Overridden
	 * @return array
	 */
	protected function getDefaultValues()
	{
		return array (
			self::HTTP_REFERER => ''
		) + parent::getDefaultValues();
	}
}

?>