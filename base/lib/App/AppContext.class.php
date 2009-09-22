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
 * @ingroup App
 */
class AppContext implements IAppContext
{
	/**
	 * @var IServerState
	 */
	private $server;

	/**
	 * @var IAppRequest
	 */
	private $request;

	/**
	 * @var IAppResponse
	 */
	private $response;

	function __construct(IAppRequest $request, IAppResponse $response, IServerState $server)
	{
		$this->request = $request;
		$this->response = $response;
		$this->server = $server;
	}

	/**
	 * @return IServerState
	 */
	function getServer()
	{
		return $this->server;
	}

	/**
	 * @return IAppRequest
	 */
	function getRequest()
	{
		return $this->request;
	}

	/**
	 * @return IAppResponse
	 */
	function getResponse()
	{
		return $this->response;
	}

	function __clone()
	{
		$this->request = clone $this->request;
		$this->respones = clone $this->response;
		$this->server = clone $this->server;
	}
}

?>