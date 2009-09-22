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
 * @ingroup Server
 */
class WebServerStateDictionary extends CliServerStateDictionary
{
	const SCRIPT_FILENAME = 'SCRIPT_FILENAME';
	const DOCUMENT_ROOT = 'DOCUMENT_ROOT';
	const SERVER_ADDR = 'SERVER_ADDR';
	const SERVER_PORT = 'SERVER_PORT';
	const REMOTE_ADDR = 'REMOTE_ADDR';
	const REMOTE_PORT = 'REMOTE_PORT';
}

?>