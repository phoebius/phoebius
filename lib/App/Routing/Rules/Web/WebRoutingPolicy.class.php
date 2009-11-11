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
 * @ingroup App_Routing_Web
 */
class WebRoutingPolicy extends ChainedRouter
{
	/**
	 * @return ChainedRouter
	 */
	static function create()
	{
		return new self;
	}

	/**
	 * @return WebRoutingPolicy an object itself
	 */
	function mapRoute(
			$name,
			$pattern,
			array $parameters = array(),
			array $constraints = array(),
			IRouteDispatcher $dispatcher = null
		)
	{
		Assert::isTrue(
			$dispatcher
				? !!$this->getDefaultDispatcher()
				: true,
			'defaultDispatcher is not specified'
		);

		$this->addRule(
			$name,
			RewriteRuleChain::create(array
				(
					WebUrlRewriteRule::create($pattern),
					ParametricRewriteRule::create()
						->addParameters($parameters)
						->addConstraints($constraints)
				)
			),
			$dispatcher
				? $dispatcher
				: $this->getDefaultDispatcher()
		);

		return $this;
	}
}

?>