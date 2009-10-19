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