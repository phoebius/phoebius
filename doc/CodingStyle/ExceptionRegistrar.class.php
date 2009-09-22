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