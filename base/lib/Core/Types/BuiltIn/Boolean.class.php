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
 * @ingroup BuiltInCoreTypes
 */
final class Boolean extends Scalar
{
	/**
	 * @return Boolean
	 */
	static function create($value)
	{
		return new self ($value);
	}

	/**
	 * @return Boolean
	 */
	static function cast($value)
	{
		return new self ($value);
	}

	/**
	 * @return OrmPropertyType
	 */
	static function getHandler(AssociationMultiplicity $multiplicity)
	{
		return new BooleanPropertyType(
			null,
			$multiplicity->is(AssociationMultiplicity::ZERO_OR_ONE)
		);
	}

	/**
	 * @return Boolean
	 */
	function setValue($value)
	{
		if (is_bool($value)) {
			parent::setValue($value);
		}
		else {
			if (in_array($value, array (1, 'true', 't'))) {
				parent::setValue(true);
			}
			else if (in_array($value, array (0, 'false', 'f'))) {
				parent::setValue(false);
			}
			else {
				throw new TypeCastException(
					Type::typeof($this),
					$value,
					'not an Boolean value specified'
				);
			}
		}

		return $this;
	}

	/**
	 * @return boolean
	 */
	protected function isValidValue($value)
	{
		return is_bool($value);
	}
}

?>