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
 * Defines variables that describe the state of the application web-server.
 *
 * This dictionary $_SERVER-compatible.
 *
 * @ingroup App_Server
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