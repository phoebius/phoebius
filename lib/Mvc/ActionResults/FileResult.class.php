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
 * TODO: export Content-Disposition logic (i.e. type: inline/attachment, filename, content-type,
 * creation/modification time into a separate class)
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
	private $unlinkOnFileFlush = false;

	/**
	 * @return FileResult
	 */
	static function create($filepath, $filename = null, $contentType = null)
	{
		return new self ($filepath, $filename, $contentType);
	}

	function __construct($filepath, $filename = null, $contentType = null)
	{
		Assert::isTrue(is_file($filepath));
		Assert::isScalarOrNull($filename);
		Assert::isScalarOrNull($contentType);

		$this->filepath = $filepath;
		$this->filename =
			$filename
				? $filename
				: basename($filepath);
		$this->contentType = $contentType;
	}

	/**
	 * @return FileResult
	 */
	function unlinkOnFileFlush()
	{
		$this->unlinkOnFileFlush = true;

		return $this;
	}

	/**
	 * @return FileResult
	 */
	function keepOnFileFlush()
	{
		$this->unlinkOnFileFlush = false;

		return $this;
	}

	/**
	 * @return void
	 */
	function handleResult(IViewContext $context)
	{
		$response = $context->getResponse();

		if ($this->contentType) {
			$response->addHeader('Content-type', $this->contentType);
		}

		$fileSize = filesize($this->filepath);
		$creation = date(DateTime::RFC822, filectime($this->filepath));
		$modif = date(DateTime::RFC822, filemtime($this->filepath));

		$response->addHeader(
			'Content-Disposition',
			"attachment; filename=\"{$this->filename}\"; size={$fileSize}; creation-date={$creation}; modification-date={$modif}"
		);
		$response->clean();
		$response->outFile($this->filepath);
		$response->finish();
	}

	function __destruct()
	{
		if ($this->unlinkOnFileFlush) {
			try {
				unlink($this->filepath);
			}
			catch (Exception $e) {
				// nothing to do
			}
		}
	}
}

?>