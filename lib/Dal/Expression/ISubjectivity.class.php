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
 * Represents a contract for objects that are allowed for subjecting other objects
 *
 * @ingroup Dal_Expression
 */
interface ISubjectivity
{
	/**
	 * @param mixed $subject what to subject
	 * @param ISubjective $object obsoleted optional subject container
	 * @return mixed new object
	 */
	function subject($subject, ISubjective $object = null);
}

?>