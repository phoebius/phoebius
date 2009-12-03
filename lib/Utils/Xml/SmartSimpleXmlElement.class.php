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
 * @todo overwrite __toString() to force valid object2string cast
 * @ingroup Utils_Xml
 */
class SmartSimpleXmlElement extends SimpleXMLElement
{
	/**
	 * @return DOMNode
	 */
	function toDomNode()
	{
		return dom_import_simplexml($this);
	}

	/**
	 * @return string
	 */
	function getCData()
	{
		return $this->toDomNode()->nodeValue;
	}
}

?>