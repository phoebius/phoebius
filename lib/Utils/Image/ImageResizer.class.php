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
 * TODO: refactor comments and {g,s}etters
 * TODO: optimze $imageFile getters' calls (map them to private props within resizer ctor)
 * @ingroup Utils_Image
 */
class ImageResizer
{
	private $useResample = true;
	private $allowEnlargement = false;
	private $enableInterlacing = false;

	/**
	 * @var ImageFile
	 */
	private $input;
	private $out = null;

	function __construct(ImageFile $imageFile)
	{
		$this->input  = $imageFile;
	}

	function setOutputFilename($outputFilename = null)
	{
		$this->out = $outputFilename;

		return $this;
	}

	/**
	 * Изменить размер изображения, "подогнав" к ширине
	 * @param int $width ширина
	 */
	function resizeByWidth($width)
	{
		$maxHeight = ceil($this->input->getHeight() * $width / $this->input->getWidth());
		return $this->resize($width, $maxHeight);
	}

	/**
	 * Изменить размер изображения, "подогнав" к высоте
	 * @param int $height высота
	 */
	function resizeByHeight($height)
	{
		$maxWidth = ceil($this->input->getWidth() * $height / $this->input->getHeight());
		return $this->resize($maxWidth, $height);
	}

	/**
	 * Изменить размер изображения, "подогнав" к  ширине и высоте
	 * @param int $width ширина
	 * @param int $height высота
	 */
	function resize($width, $height)
	{
		list($w, $h) = $this->scale($width, $height);
		try {
			return $this->process($w,$h);
		}
		catch (ExecutionContextException $e) {
			throw new StateException($e->getMessage());
		}
	}

	private function process($width, $height)
	{
		if ($width == $this->input->getWidth() && $height == $this->input->getHeight()) {
			if (!is_null($this->out)) {
				try {
					unlink($this->out);
				}
				catch (ExecutionContextException $e){}

				copy($this->input->getFilename(), $this->out);
			}

			return file_get_contents($this->input->getFilename());
		}

		ob_start();

		// Creating destination img
		$dest = imagecreatetruecolor($width, $height);
		if ($this->enableInterlacing) {
			imageinterlace($dest,1);
		}

		// Opening source img
		$imagefunc = $this->ImageTypeToFunctionName("imagecreatefrom");
		$src = $imagefunc($this->input->getFilename());

		$func =
			($this->useResample && function_exists('imagecopyresampled') )
				? 'imagecopyresampled'
				: 'imagecopyresized';
		$func(
				$dest, $src,
				0, 0,
				0, 0,
				$width, $height,
				$this->input->getWidth(), $this->input->getHeight()
		);
		imagedestroy($src);

		// Output
		$func = $this->imageTypeToFunctionName("image");
		$args = array();
		$args[] =& $dest;

		if ($this->out){
			$args[] =  $this->out;
		}

		if (IMAGETYPE_JPEG == $this->input->getType()) {
			if (!$this->out) {
				$args[] = null;
			}

			$args[] = 100;
		}

		call_user_func_array($func, $args);
		imagedestroy($dest);

		return ob_get_clean();
	}

	private function imageTypeToFunctionName($function)
	{
		$name = '';
		switch($this->input->getType())
		{
			case IMAGETYPE_GIF:
				{
					$name = $function.'gif';
					break;
				}

			case IMAGETYPE_JPEG:
				{
					$name = $function.'jpeg';
					break;
				}

			case IMAGETYPE_PNG:
				{
					$name = $function.'png';
					break;
				}

			default:
				{
					Assert::isUnreachable(
						'unsupported image type passed to resizer: %s',
						$this->input->getType()
					);
				}
		}
		return function_exists($name) ? $name : false;
	}

	private function scale($maxwidth, $maxheight)
	{
		$inputWidth = $this->input->getWidth();
		$inputHeight = $this->input->getHeight();

		if ($inputWidth <= $maxwidth && $inputHeight <= $maxheight) {
			return $this->scaleEnlarge($maxwidth,$maxheight);
		}

		if ($inputWidth - $maxwidth > $inputHeight - $maxheight) {
			// fit width
			$ratio =  $maxwidth / $inputWidth;
			$new_width = $inputWidth * $ratio;
			$new_height = $inputHeight * $ratio;
		}
		else {
			//fit height
			$ratio = $maxheight/$inputHeight;
			$new_width = $inputWidth * $ratio;
			$new_height = $inputHeight * $ratio;
		}

		return array(round($new_width), round($new_height));
	}

	private function scaleEnlarge($maxwidth,$maxheight)
	{
		$inputWidth = $this->input->getWidth();
		$inputHeight = $this->input->getHeight();

		if (
				$this->allowEnlargement
				&& $inputWidth < $maxwidth
				&& $inputHeight < $maxheight
		) {
			// allow enlargements
			if ($maxwidth - $inputWidth > $maxheight - $inputHeight) {
				//enlarge according to height
				$new_height = $maxheight;
				$new_width = $new_height * $inputWidth / $inputHeight;
			}
			else {
				//enlarge accoring to width
				$new_width = $maxwidth;
				$new_height = $new_width * $inputHeight / $inputWidth;
			}
			return array(round($new_width), round($new_height));
		}
		else {
			return array($inputWidth, $this->input->getHeight());
		}
	}
}


?>