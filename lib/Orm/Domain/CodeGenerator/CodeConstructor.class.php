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
 * Represents an abstract PHP code generator
 *
 * @ingroup Orm_Domain_CodeGenerator
 */
abstract class CodeConstructor
{
	/**
	 * Gets the message that should be presented in a file header
	 * @return string
	 */
	abstract protected function getHeaderMessage();

	/**
	 * Generates a PHP code and writes it to the specified stream
	 *
	 * @param IOutput $stream stream to write the result to
	 *
	 * @return void
	 */
	abstract function make(IOutput $stream);

	/**
	 * Generates a textual representation of file header
	 *
	 * @return string
	 */
	protected function getFileHeader()
	{
		$product = PHOEBIUS_FULL_PRODUCT_NAME;
		$now = date('Y/m/d H:i');
		$message = $this->getHeaderMessage();

		return <<<EOT
<?php
/* ***********************************************************************************************
 *
 * {$product} Copyright (c) 2010 Scand Ltd.
 *
 * **********************************************************************************************
 *
 * Generated at {$now}
 *
 * {$message}
 *
 ************************************************************************************************/


EOT;
	}

	/**
	 * Generates a textual representation of file footer
	 *
	 * @return string
	 */
	protected function getFileFooter()
	{
		return <<<EOT


?>
EOT;
	}
}

?>