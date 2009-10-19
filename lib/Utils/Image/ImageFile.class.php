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
 * @ingroup Utils_Cipher
 */
class ImageFile
{
	/**
	 * Synced with getimagesize()
	 */
	const IMAGESIZE_STRUCT_WIDTH = 0;

	/**
	 * Synced with getimagesize()
	 */
	const IMAGESIZE_STRUCT_HEIGHT = 1;

	/**
	 * Synced with getimagesize()
	 */
	const IMAGESIZE_STRUCT_IMAGETYPE = 2;

	/**
	 * @var array of ImageFile
	 */
	private static $cachedImageSizes = array();

	private $filename;
	private $height;
	private $width;
	private $type;

	function __construct($filename)
	{
		if (!file_exists($filename)) {
			throw new FileNotFoundException($filename);
		}

		if (isset(self::$cachedImageSizes[$filename]))  {
			$this->importObject(self::$cachedImageSizes[$filename]);
		}
		else {
			$this->importFile($filename);
			self::$cachedImageSizes[$filename] = $this;
		}

		$this->filename = $filename;
	}

	/**
	 * @return string
	 */
	function getFilename()
	{
		return $this->filename;
	}

	/**
	 * @return int
	 */
	function getHeight()
	{
		return $this->height;
	}

	/**
	 * @return int
	 */
	function getType()
	{
		return $this->type;
	}

	/**
	 * @return int
	 */
	function getWidth()
	{
		return $this->width;
	}

	function getExtension($includeDot = false)
	{
		Assert::isBoolean($includeDot);

		if ($this->type == IMAGETYPE_JPEG || $this->type == IMAGETYPE_JPEG2000) {
			return ($includeDot ? '.' : '') . 'jpg';
		}
		else {
			return image_type_to_extension($this->type, $includeDot);
		}
	}

	private function importFile($filename)
	{
		$dimensions = getimagesize($filename);

		if (!is_array($dimensions)) {
			throw new ArgumentException('filename', 'not an image given');
		}

		$this->width = $dimensions[self::IMAGESIZE_STRUCT_WIDTH];
		$this->height = $dimensions[self::IMAGESIZE_STRUCT_HEIGHT];
		$this->type = $dimensions[self::IMAGESIZE_STRUCT_IMAGETYPE];
	}

	private function importObject(ImageFile $source)
	{
		$this->height = $source->height;
		$this->width = $source->width;
		$this->type = $source->type;
	}
}

?>