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
 * ArgumentException is thrown when a method is invoked and at least one of the passed arguments
 * does not meet the parameter specification of the called method. All instances of
 * ArgumentException should carry a meaningful error message describing the invalid argument,
 * as well as the expected range of values for the argument.
 * @ingroup Core_Exceptions
 */
class ArgumentException extends ApplicationException
{
	protected $argumentName;

	/**
	 * @param string $argumentName
	 * @param string $message
	 */
	function __construct($argumentName, $message)
	{
		Assert::isScalar($argumentName);

		parent::__construct($message);

		$this->argumentName = $argumentName;
	}

	/**
	 * Returns the argument name that caused an exception
	 * @return string
	 */
	function getArgumentName()
	{
		return $this->argumentName;
	}
}

?>