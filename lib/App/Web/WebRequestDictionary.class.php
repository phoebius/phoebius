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
 * Defines variables that describe the request state invoked over HTTP.
 *
 * This dictionary $_SERVER-compatible.
 *
 * @ingroup App_Web
 */
class WebRequestDictionary extends HttpUrlDictionary
{
	const PROTOCOL = 'SERVER_PROTOCOL';
	const REQUEST_METHOD = 'REQUEST_METHOD';
	const HTTP_REFERER = 'HTTP_REFERER';

	protected function getDefaultValues()
	{
		return array (
			self::HTTP_REFERER => ''
		) + parent::getDefaultValues();
	}
}

?>