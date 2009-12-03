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
 * Projection that counts the resulting rows by the property and optionally labels the result
 *
 * @ingroup Orm_Query_Projections
 */
class CountProjection extends AggrProjection
{
	private $lookupProperty;

	/**
	 * @param string $property property to be used for aggregation. If not set, PK is used
	 * @param string $alias optional label for the result of the aggregator
	 */
	function __construct($property = null, $alias = null)
	{
		$this->lookupProperty = !$property;

		parent::__construct(
			'COUNT',
			$property
				? $property
				: '___lookup_me_in_CountProjection::getValueExpression()___',
			$alias
		);
	}

	protected function getValueExpression(EntityQueryBuilder $builder)
	{
		if ($this->lookupProperty) {
			$alias = $this->getAlias();
			$builder->registerIdentifier($alias);

			return
				new AliasedSqlValueExpression(
					$builder->subject(
						$builder->getEntity()->getLogicalSchema()->getIdentifier()
					),
					$alias
				);
		}

		return parent::getValueExpression($builder);
	}
}

?>