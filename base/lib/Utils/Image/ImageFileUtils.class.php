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
 * @ingroup Image
 */
class ImageFileUtils extends StaticClass
{
	/**
	 * @param string $file путь к файлу
	 * @return bool|ImageFile
	 */
	static function isImage(
			$filename,
			array $supportedImageTypes = array(
				IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_JPEG2000, IMAGETYPE_PNG
			)
		)
	{
		try {
			$image = new ImageFile($filename);
		}
		catch (Exception $e) {
			return false;
		}

		if (
				empty($supportedImageTypes)
				|| in_array($image->getType(), $supportedImageTypes)
		) {
			return $image;
		}

		return false;
	}
}

?>