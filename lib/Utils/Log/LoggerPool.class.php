<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright (c) 2010 Scand Ltd.
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
 * Represents a pool of named loggers.
 *
 * Every component may obtain a logger object for debugging purposes exposing its own name.
 *
 * By default a default logger is presented, but this pool allows to split the pipes
 * by specifying custom named loggers for each component that exposes the used names.
 *
 * E.g., the Dal/DB component logs everything to DB::LOG_VERBOSE and DB::LOG_QUERY, so you can
 * cut out all verbose information to /dev/null and all queries to tmp/query.log, allowing
 * all other components to log everything to tmp/application.log:
 *
 * @code
 * LoggerPool::getInstance()
 * 	->setDefault(new AppLogger('application'))
 * 	->addNamed(DB::LOG_VERBOSE, new DevNullLogger)
 * 	->addNamed(DB::LOG_QUERY, new AppLogger('query'));
 * @endcode
 *
 * @ingroup Utils_Log
 */
final class LoggerPool extends LazySingleton
{
	/**
	 * @var array of ILogger
	 */
	private $loggers = array();

	/**
	 * @var ILogger
	 */
	private $default;

	/**
	 * Gets the instance of the loggers pool
	 *
	 * @return LoggerPool
	 */
	static function getInstance()
	{
		$instance = self::instance(__CLASS__);
		if (!$instance->default) {
			$instance->default = new DevNullLogger;
		}

		return $instance;
	}

	/**
	 * Sets the default logger to be used by all components
	 *
	 * @return LoggerPool an object itself
	 */
	function setDefault(ILogger $logger)
	{
		$this->default = $logger;

		return $this;
	}

	/**
	 * Gets the default logger
	 *
	 * @return ILogger
	 */
	function getDefault()
	{
		return $this->default;
	}

	/**
	 * Adds a custom named logger
	 *
	 * @param string $name
	 * @return LoggerPool an object itself
	 */
	function addNamed($name, ILogger $logger)
	{
		$this->loggers[$name] = $logger;

		return $this;
	}

	/**
	 * Gets the named logger or default logger if no logger by the specified name found
	 *
	 * @param string $name
	 * @return LoggerPool an object itself
	 */
	function getNamed($name)
	{
		return
			isset($this->loggers[$name])
				? $this->loggers[$name]
				: $this->default;
	}
}

?>