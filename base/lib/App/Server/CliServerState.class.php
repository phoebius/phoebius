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
 * @ingroup Server
 */
class CliServerState implements IServerState
{
	/**
	 * @var array
	 */
	private $serverVars = array();

	/**
	 * @var array
	 */
	private $envVars = array();

	function __construct(
			CliServerStateDictionary $dictionary,
			array $envVars = array()
		)
	{
		$this->serverVars = $dictionary->getFields();
		$this->envVars = $envVars;
	}

	/**
	 * @return array
	 */
	function getEnvVars()
	{
		return $this->envVars;
	}

	/**
	 * @see IServerState::getArgc()
	 *
	 * @return integer
	 */
	function getArgc()
	{
		return $this->serverVars[CliServerStateDictionary::ARGC];
	}

	/**
	 * @see IServerState::getArgv()
	 *
	 * @return array
	 */
	function getArgv()
	{
		return $this->serverVars[CliServerStateDictionary::ARGV];
	}

	/**
	 * @see IServerState::getRequestTime()
	 *
	 * @return integer
	 */
	function getRequestTime()
	{
		return $this->serverVars[CliServerStateDictionary::REQUESTIME_FORMAT];
	}
}

?>