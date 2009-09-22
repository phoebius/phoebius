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
	 * @var OrmProperty
	 */
	private $encapsulantProperty;

	function __construct(
			IQueried $container,
			IQueried $encapsulant,
			OrmProperty $encapsulantProperty
		)
	{
		$this->encapsulantProperty = $encapsulantProperty;

		parent::__construct($container, $encapsulant);
	}

	/**
	 * @return OrmProperty
	 */
	function getEncapsulantProperty()
	{
		return $this->encapsulantProperty;
	}
}

?>