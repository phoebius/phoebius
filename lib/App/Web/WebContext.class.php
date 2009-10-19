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
 * @ingroup App_Web
 */
final class WebContext extends AppContext implements IWebContext
{
	/**
	 * @var WebContext
	 */
	static private $current;

	/**
	 * @return WebContext
	 */
	static function getCurrent()
	{
		Assert::isNotNull(self::$current);

		return self::$current;
	}

	/**
	 * @return void
	 */
	static function setCurrent(WebContext $context)
	{
		self::$current = $context;
	}

	function __construct(WebRequest $request, WebResponse $response, IServerState $server)
	{
		if (!self::$current) {
			self::$current = $this;
		}

		parent::__construct($request, $response, $server);
	}

	/**
	 * @return WebRequest
	 */
	function getRequest()
	{
		return parent::getRequest();
	}

	/**
	 * @return WebResponse
	 */
	function getResponse()
	{
		return parent::getResponse();
	}
}

?>