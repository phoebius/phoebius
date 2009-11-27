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

class CountProjection extends AggrProjection
{
	private $lookupProperty;

	function __construct($property = null, $alias = null)
	{
		$this->lookupProperty = null === $property;

		parent::__construct('COUNT', $property ? $property : 'id', $alias);
	}

	protected function getValueExpression(EntityQueryBuilder $builder)
	{
		if ($this->lookupProperty) {
			return
				new AliasedSqlValueExpression(
					$builder->subject(
						$builder->getEntity()->getLogicalSchema()->getIdentifier()
					),
					$this->getAlias()
				);
		}

		return parent::getValueExpression($builder);
	}
}

?>