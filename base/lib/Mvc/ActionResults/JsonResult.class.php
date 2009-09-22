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
 * Represents a JavaScript Object Notation result that can be used in an AJAX application
 * @ingroup ActionResults
 */
class JsonResult extends ContentResult
{
	/**
	 * @var array
	 */
	private $json;

	function __construct(array $json)
	{
		$this->json = $json;

		parent::__construct(json_encode($this->json));
	}
}

?>