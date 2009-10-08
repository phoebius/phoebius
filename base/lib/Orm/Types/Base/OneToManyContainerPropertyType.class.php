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
 * 1:* relation implementation
 * @ingroup BaseOrmTypes
 */
class OneToManyContainerPropertyType extends ContainerPropertyType
{
	/**
	 * @var IOrmProperty
	 */
	private $encapsulantProperty;

	function __construct(
			IQueryable $container,
			IQueryable $encapsulant,
			IOrmProperty $encapsulantProperty
		)
	{
		$this->encapsulantProperty = $encapsulantProperty;

		parent::__construct($container, $encapsulant);
	}

	/**
	 * @return IOrmProperty
	 */
	function getEncapsulantProperty()
	{
		return $this->encapsulantProperty;
	}

	/**
	 * @return string
	 */
	function getImplClass()
	{
		return null;
	}
}

?>