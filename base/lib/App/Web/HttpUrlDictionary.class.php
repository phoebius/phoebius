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
class HttpUrlDictionary extends Dictionary
{
	const URI = 'REQUEST_URI';
	const PORT = 'SERVER_PORT';
	const HOST = 'HTTP_HOST';
	const HTTPS = 'HTTPS';

	/**
	 * Overridden
	 * @return array
	 */
	protected function getDefaultValues()
	{
		return array (
			self::HTTPS => 0
		);
	}
}

?>