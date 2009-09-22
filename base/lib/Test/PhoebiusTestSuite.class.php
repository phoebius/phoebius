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
 * @ingroup Test
 */
class PhoebiusTestSuite extends PHPUnit_Framework_TestSuite
{
	private $extension = PHOEBIUS_TYPE_EXTENSION;
	private $testPostfix = 'Test';

	/**
	 * @return PHOEBIUS_TestSuite
	 */
	static function create()
	{
		return new self;
	}

	function __construct(array $paths)
	{
		parent::__construct();

		$this->setName(__CLASS__);

		foreach ($paths as $testPath) {
			$this->addPath($testPath);
		}
	}

	function getDefaultExtension()
	{
		return $this->extension;
	}

	function getTestFilePostfix()
	{
		return $this->testPostfix;
	}

	/**
	 * @return PhoebiusTestSuite
	 */
	function setDefaultExtension($extension)
	{
		Assert::isScalar($extension);

		$this->extension = '.'. trim($extension, '.');

		return $this;
	}

	/**
	 * @return PhoebiusTestSuite
	 */
	function setTestFilePostfix($testFilePostfix)
	{
		Assert::isScalar($testFilePostfix);

		$this->testPostfix = $testFilePostfix;

		return $this;
	}

	/**
	 * @return PhoebiusTestSuite
	 */
	function addPath($path)
	{
		foreach ((array)scandir($path) as $fsEntry) {
			if ($fsEntry{0} == '.') {
				continue;
			}

			$entryPath = $path . DIRECTORY_SEPARATOR . $fsEntry;

			if (is_dir($entryPath)) {
				$this->addPath($entryPath);

				continue;
			}

			if (preg_match('/'.preg_quote($this->testPostfix.$this->extension).'$/', $fsEntry)) {
				$_ = preg_replace('/'.preg_quote($this->extension).'$/', '', $fsEntry);
				$this->addTestSuite($_);

				continue;
			}
		}

		return $this;
	}
}


?>