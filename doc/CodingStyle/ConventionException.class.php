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
 * Thrown when a code does not follow the guideline
 * @ingroup CodingStyle
 */
class ConventionException extends Exception
{
	/**
	 * @var ConventionChapter
	 */
	private $chapter;

	function __construct(ConventionChapter $occuredErrorChapter)
	{
		$this->chapter = $occuredErrorChapter;
	}

	/**
	 * @return ConventionChapter
	 */
	function getChapter()
	{
		return $this->chapter;
	}
}

?>