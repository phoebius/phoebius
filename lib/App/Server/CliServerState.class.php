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
 * @ingroup App_Server
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