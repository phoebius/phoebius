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
 * @ingroup PhpLayout
 */
class SimplePhpView implements IView
{
	/**
	 * @var string
	 */
	private $layoutPath;

	/**
	 * @throws FileNotFoundException
	 * @param string $layoutPath
	 */
	function __construct($layoutPath)
	{
		Assert::isScalar($layoutPath);

		$this->layoutPath = $layoutPath;

		if (!file_exists($layoutPath)) {
			throw new FileNotFoundException($layoutPath, 'view does not exists at the specified path');
		}
	}

	/**
	 * @return void
	 */
	function render(IViewContext $context)
	{
		// isolate inclusion to introduce a clean scope
		$this->injectLayout();
	}

	/**
	 * Overridden
	 * @return void
	 */
	private function injectLayout()
	{
		require $this->layoutPath;
	}
}

?>