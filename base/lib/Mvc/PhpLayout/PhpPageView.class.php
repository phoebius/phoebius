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
 * TODO: add the required model vars, specified by the view itself.
 * I.e.:
 * $this->codeBehind(...);
 * $this->require(array('var1', 'var2'));
 *
 * If var1 or var2 is not passed via the model, compilation fails.
 * Otherwise those vars are exported to the local scope of the view.
 *
 *
 * @ingroup PhpLayout
 */
abstract class PhpPageView extends PhpView
{
	/**
	 * @var IViewContext
	 */
	private $currentContext;

	/**
	 * @var PhpMasterPageView|null
	 */
	private $masterPageView = null;

	/**
	 * @var Model|null
	 */
	private $masterPageModel = null;

	/**
	 * @var string|null
	 */
	private $contentBufferCurrentId = null;

	/**
	 * @var integer
	 */
	private $contentBufferCurrentLevel = 0;

	/**
	 * @var array
	 */
	private $contentBuffers = array();

	/**
	 * @var string
	 */
	private $defaultContentBufferId = null;

	function urlByRule($ruleName, array $parameters = array())
	{
		$this->assertInsideRenderingContext();

		Assert::isScalar($ruleName);

		$rrc = $this->currentContext->getRouteContext()->getRewriteRuleContext();
		$rule = $rrc->getRoutingPolicy()->getRule($ruleName);
		$route = new Route;

		$request = $rule->compose(
			$route->setParameters($parameters),
			$rrc->getRequest()->getCleanCopy()
		);

		return $request->getHttpUrl();
	}

	/**
	 * @return PhpPageView an object itself
	 */
	function setMasterPageView(PhpMasterPageView $masterView, $defaultContentPlaceholderId = null)
	{
		Assert::isScalarOrNull($defaultContentPlaceholderId);

		$this->assertInsideRenderingContext();

		Assert::isNull(
			$this->masterPageView,
			'master page view already set'
		);

		$this->masterPageView = $masterView;
		$this->defaultContentBufferId = $defaultContentPlaceholderId;

		// FIXME: set master page model here, taken from the currently rendering view

		return $this;
	}

	/**
	 * @return PhpMasterPageView
	 */
	function getMasterPageView()
	{
		Assert::isNotNull(
			$this->masterPageView,
			'master page view is not yet set'
		);

		return $this->masterPageView;
	}

	/**
	 * @return void
	 */
	function setMasterPageModel(Model $model)
	{
		Assert::isNotNull(
			$this->masterPageView,
			'master page view is not yet set, call PhpPageView::setMasterPageView()'
		);

		$this->masterPageModel = $model;
	}

	/**
	 * @return Model
	 */
	function getMasterPageModel()
	{
		Assert::isNotNull(
			$this->masterPageView,
			'master page view is not yet set'
		);

		if (!$this->masterPageModel) {
			$this->masterPageModel = new Model();
		}

		return $this->masterPageModel;
	}

	/**
	 * @return void
	 */
	function render(IViewContext $context)
	{
		$this->currentContext = $context;

		ob_start();
		$renderingBufferLevel = ob_get_level();

		parent::render($context);

		$this->currentContext = null;

		// close unclosed buffers
		if ($this->contentBufferCurrentId) {
			$this->endContent();
		}

		$currentBufferLevel = ob_get_level();
		Assert::isTrue(
			$renderingBufferLevel == $currentBufferLevel,
			'unclosed output buffers inside, expected %s level but %s found',
			$renderingBufferLevel, $currentBufferLevel
		);

		$renderingBuffer = ob_get_clean();

		if ($this->masterPageView) {
			if ($this->defaultContentBufferId) {
				$this->contentBuffers[$this->defaultContentBufferId] = $renderingBuffer;
			}
			else {
				Assert::isNotEmpty(
					$this->contentBuffers,
					'PhpPageView with specified PhpMasterPageView should define at least one contentBuffer or defaultContentBufferId'
				);
			}

			$this->masterPageView->addContentBuffers($this->contentBuffers);

			$this->contentBuffers = array();
			$this->defaultContentBufferId = null;
			$this->contentBufferCurrentId = null;
			$this->contentBufferCurrentLevel = 0;

			$this->masterPageView->render(
				new ViewContext(
					$context->getController(),
					$this->masterPageModel
						? $this->masterPageModel
						: $context->getModel(),
					$context->getRouteContext(),
					$context->getAppContext()
				)
			);

			$this->masterPageModel = null;
			$this->masterPageView = null;
		}
		else {
			$context->getAppContext()->getResponse()->out($renderingBuffer);
		}
	}

	/**
	 * @return void
	 */
	function beginContent($contentId)
	{
		$this->assertInsideRenderingContext();

		Assert::isEmpty(
			$this->contentBufferCurrentId,
			'already in buffer',
			$this->contentBufferCurrentId
		);

		Assert::isFalse(
			in_array($contentId, $this->contentBuffers),
			'contentId %s already defined in contentBuffers',
			$contentId
		);

		ob_start();
		$this->contentBufferCurrentId = $contentId;
		$this->contentBufferCurrentLevel = ob_get_level();
	}

	/**
	 * @return void
	 */
	function cleanContent()
	{
		$this->assertInsideRenderingContext();

		Assert::isNotEmpty(
			$this->contentBufferCurrentId,
			'buffer not yet started'
		);

		Assert::isTrue(
			$this->contentBufferCurrentLevel == ob_get_level(),
			'unknown buffers inside view are not closed'
		);

		ob_end_clean();

		$this->contentBufferCurrentId = null;
		$this->contentBufferCurrentLevel = 0;
	}

	/**
	 * @return void
	 */
	function endContent()
	{
		$this->assertInsideRenderingContext();

		Assert::isNotEmpty(
			$this->contentBufferCurrentId,
			'buffer not yet started'
		);

		Assert::isTrue(
			$this->contentBufferCurrentLevel == ob_get_level(),
			'unknown buffers inside view are not closed'
		);

		$this->contentBuffers[$this->contentBufferCurrentId] = ob_get_clean();

		$this->contentBufferCurrentId = null;
		$this->contentBufferCurrentLevel = 0;
	}

	/**
	 * @return void
	 */
	function setContent($contentId, $content)
	{
		$this->assertInsideRenderingContext();

		Assert::isScalar($contentId);
		Assert::isScalar($content);
		Assert::isTrue(
			$this->contentBufferCurrentId == $contentId,
			'already inside content buffer with the same id: %s',
			$contentId
		);

		$this->contentBuffers[$contentId] = $content;
	}
}

?>