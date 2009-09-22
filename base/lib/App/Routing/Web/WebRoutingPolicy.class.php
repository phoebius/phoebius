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
 * @ingroup WebRouting
 */
class WebRoutingPolicy extends ChainedRoutingPolicy
{
	/**
	 * @return ChainedRoutingPolicy
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