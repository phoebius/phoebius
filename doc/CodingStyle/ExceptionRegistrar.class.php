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
 * Our meaningless singleton implementation
 * @ingroup CodingStyle
 */
class ExceptionRegistrar extends LazySingleton
{
	/**
	 * @var array
	 */
	private $exceptions = array();

	/**
	 * @return ExceptionRegistrar
	 */
	static function getInstance()
	{
		return LazySingleton::instance(__CLASS__);
	}

	/**
	 * Registers one another exception. Alias for {@link ExceptionRegistrar::registerException}
	 * @return ExceptionRegistrar
	 */
	static function register(Exception $e)
	{
		self::getInstance()->registerException($e);
	}

	/**
	 * Returns the list of registered exceptions. Alias for
	 * {@link ExceptionRegistrar::getExceptionList}
	 * @return array
	 */
	static function getExceptions()
	{
		self::getInstance()->getExceptionList();
	}

	/**
	 * Returns the number of registered exceptions. Alias for
	 * {@link ExceptionRegistrar::getNumberOfExceptions}
	 * @return integer
	 */
	static function getExceptionsNumber()
	{
		self::getInstance()->getNumberOfExceptions();
	}

	/**
	 * Registers one another exception
	 * @return ExceptionRegistrar
	 */
	function registerException(Exception $e)
	{
		$this->exceptions[] = $e;

		return $this;
	}

	/**
	 * Returns the list of registered exceptions
	 * @return array
	 */
	function getExceptionList()
	{
		return $this->exceptions;
	}

	/**
	 * Returns the number of registered exceptions
	 * @return integer
	 */
	function getNumberOfExceptions()
	{
		return sizeof($this->exceptions);
	}
}

?>