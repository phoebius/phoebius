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
 * @ingroup
 */
final class PropertyValueGenerator implements IIDGenerator
{
	/**
	 * @var OrmPropertyType
	 */
	private $type;

	/**
	 * @var IIDGenerator
	 */
	private $generator;

	function __construct(OrmPropertyType $type, IIDGenerator $actualGenerator)
	{
		$this->type = $type;
		$this->generator = $actualGenerator;
	}

	function getType()
	{
		return $this->generator->getType();
	}

	function generate(IdentifiableOrmEntity $entity)
	{
		$value = $this->generator->generate($entity);

		if (!is_null($value)) {
			$value = $this->type->makeValue(
				array($value),
				new FetchStrategy(FetchStrategy::CASCADE)
			);
		}

		return $value;
	}
}

?>