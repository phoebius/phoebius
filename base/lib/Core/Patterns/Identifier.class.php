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
 * Basic implementation of an entity, which has a special "feature" - a possibility to be
 * identified
 * @ingroup Patterns
 */
class Identifier implements IIdentifiable
{
	/**
	 * An identifer of an entity
	 * @param scalar
	 */
	protected $id;

	/**
	 * Creates an instance of {@link Identifier}
	 * @return Identifier
	 */
	static function create()
	{
		return new self;
	}

	/**
	 * Gets the identifier of an entity
	 * @return integer
	 */
	function getId()
	{
		return $this->id;
	}

	/**
	 * Sets the identifier of an entity
	 * @param $id scalar
	 * @return Identifier an object itself
	 */
	function setId($id)
	{
		Assert::isScalar($id);

		$this->id = $id;

		return $this;
	}
}

?>