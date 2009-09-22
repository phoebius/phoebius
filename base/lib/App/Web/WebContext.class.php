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
 * @ingroup Web
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