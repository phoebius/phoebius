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
 * Represents a result that consists of a file
 *
 *
 * @todo export Content-Disposition logic (i.e. type: inline/attachment, filename, content-type,
 * 				creation/modification time into a separate class)
 *
 * @ingroup Mvc_ActionResults
 */
class FileResult implements IActionResult
{
	/**
	 * @var string
	 */
	private $filepath;

	/**
	 * @var string
	 */
	private $filename;

	/**
	 * @var string
	 */
	private $contentType;

	/**
	 * @var boolean
	 */
	private $unlinkOnFinish = false;

	/**
	 * @param string $filepath path to the file that should be presented in response
	 * @param string $filename optional name of the file to be specified in the response
	 * @param string $contentType optional content type of the file to be specified in the response
	 * @param boolean $unlinkOnFinish whether to remove the file when response is finished
	 */
	function __construct($filepath, $filename = null, $contentType = null, $unlinkOnFinish = false)
	{
		Assert::isTrue(is_file($filepath));
		Assert::isScalarOrNull($filename);
		Assert::isScalarOrNull($contentType);
		Assert::isBoolean($unlinkOnFinish);

		$this->filepath = $filepath;
		$this->filename =
			$filename
				? $filename
				: basename($filepath);
		$this->contentType = $contentType;
		$this->unlinkOnFileFlush = $unlinkOnFinish;
	}

	function handleResult(IWebResponse $response)
	{
		if ($this->contentType) {
			$response->addHeader('Content-Type', $this->contentType);
		}

		$fileSize = filesize($this->filepath);
		$creation = date(DateTime::RFC822, filectime($this->filepath));
		$modif = date(DateTime::RFC822, filemtime($this->filepath));

		$response->addHeader(
			'Content-Disposition',
			"attachment; filename=\"{$this->filename}\"; size={$fileSize}; creation-date={$creation}; modification-date={$modif}"
		);

		$response->writeFile($this->filepath);
	}

	function __destruct()
	{
		if ($this->unlinkOnFileFlush) {
			try {
				@unlink($this->filepath);
			}
			catch (Exception $e) {
				// nothing to do
			}
		}
	}
}

?>