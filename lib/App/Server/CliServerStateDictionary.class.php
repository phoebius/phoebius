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
 * Defines variables that describe the state of the server that hosts the application.
 *
 * This dictionary can be used for running CLI applications.
 *
 * This dictionary $_SERVER-compatible.
 *
 * @ingroup App_Server
 */
class CliServerStateDictionary extends Dictionary
{
	const ARGV = 'argv';
	const ARGC = 'argc';
	const REQUEST_TIME = 'REQUEST_TIME';
}

?>