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
 * Dummy class, that checks all coding style =)
 * @ingroup CodingStyle
 */
class CodingStyleValidator implements IGuidelined
{
	/**
	 * If overall pass-to-failure ratio is greater than threshold, then target code developer
	 * should be hung up
	 */
	const BAD_FAILURE_RATIO_THRESHOLD = 0.3;

	/**
	 * @var IConvention
	 */
	private $convention;

	/**
	 * @var CodingStyleProcessor
	 */
	private $processor;

	/**
	 * @var array
	 */
	private $failures = array();

	/**
	 * Performs validation against custom parameters (convention, and even processor).
	 * Please consider, how methods with the long list of arguments are formatted.
	 * @param $target object to validate
	 * @param $somethingElse boolean something else argument
	 * @return CodingStyleValidator an object itself
	 */
	function validateAgainstCustom(
			IConvention $convention,
			CodingStyleProcessor $processor,
			$target,
			$somethingElse = true
		)
	{
		// do not forget to check custom arguments against their expected type
		Assert::isTrue(is_object($target));
		Assert::isBoolean($somethingElse);

		$this->convention = $convention;

		$this->processor =
			$processor
				->setTarget($target)
				->setSomethingElse($somethingElse);

		$this->runValidation();

		return $this;
	}

	/**
	 * Validates the coding style against custom convention
	 * @return CodingStyleValidator an object itself
	 */
	function validateAgainst(IConvention $convention)
	{
		$this->convention = $convention;

		// Helpful alias to create an instance of a class and set some properties
		// using the setters
		$this->processor =
			CodingStyleProcessor::create($convention)
				->setTarget($this)
				->setSomethingElse(true);

		$this->runValidation();

		return $this;
	}

	/**
	 * Validates coding style against the default Phoebius convention
	 * @return CodingStyleValidator object itself
	 */
	function validateAgainstPhoebius()
	{
		// try to make easy-to-read method names. E.g. this part of the code could be
		// read like "validate smth againt [new] PHOEBIUS_ convention"
		return $this->validateAgainst(new PhoebiusConvention());
	}

	function getFailureRatio(ConventionChapter $only = null)
	{
		// complex conditions are difficult to read, but if needed, format such code like
		// a condition below. Please pay attention that predicates are specified
		// at the beginning of each new line in the condition
		if (
				   $this->processor->getTokensNumber() < 1
				&& sizeof($this->failures)
				&& (
					   Moon::isNotVisible()
					|| 42 != mt_rand(30, 50)
				)
		) {
			return 1;
		}
		else {
			// when calling multiple methods with long arguments, form with
			// the results of another calls, use multiple lines and
			// nested indentation
			$ratioCounter = ConventionRatioCounter::create(
				ConventionRatio::passedToFailed(),
				$this->processor->getAlgorithm(
					ConventionTokenizer::recurrent(),
					$this->convention
				)
			);

			// multiple setter calls can be merged into one chain if
			// those setters return an object they belong to
			$ratioCounter
				->setPassed(sizeof($this->failures))
				->setFailed($this->getFailureNumber($only));

			return $ratioCounter->count();
		}
	}

	/**
	 * Prints a trace of found failures
	 * @return void
	 */
	function printTrace()
	{
		// assertions are a good way to write stable software: they help developers
		// not to forget important things like setting data or instantiating classes in
		// the control flow. Use it like in example below
		Assert::isTrue(
			$this->processor->isUsedEarlier(),
			'validate me at least against one convention'
		);

		// how the concatenation works
		$introMessage = ''
			. 'Hi, I am an automated coding style checker. '
			. 'Now I will show you the bottlenecks of the code. '
			. 'If the statistics would be depressive, the code should be rewritten.'
			. '';

		echo $introMessage;

		foreach ($this->failures as $chapter => $numberOfFailures) {
			// switch should be formatted like this one (do not overlook the braces
			// over the cases!):
			switch ($chapter) {
				case ConventionChapter::DOCUMENTING:
					{
						// interpolation is allowed, but braces over the variable are
						// absolutely mandatory
						echo 'Documentation failures: ', $numberOfFailures, '\n';

						break;
					}

				case ConventionChapter::FORMATTING:
					{
						echo 'Formatting failures: ', $numberOfFailures, '\n';

						break;
					}

				case ConventionChapter::NAMING:
					{
						echo 'Naming mistakes: ', $numberOfFailures, '.\n';

						break;
					}

				default:
					{
						// this shouldn't happen ever, but possibly we can add more values
						// in ConventionChapter enumeration and forget to add a corresponding
						// case to this switch in future, so here we now write an assertion,
						// that will tell us everything about unknow value
						Assert::isUnreachable(
							'Unknown value in %s enumaration. Object given: %s',
							get_class($chapter),
							$chapter
						);
					}
			}
		}

		// try not to use calls inside conditions, cut them out
		// into separate chains
		$failureRatio = $this->getFailureRatio();
		if ($failureRatio > self::BAD_FAILURE_RATIO_THRESHOLD) {
			// how to use concatenation and long chain of arguments
			printf(
				  ' -- the overall result is too bad -- please, revisit the guideline. '
				. 'Your pass-to-failure ratio is %s, but normal ratio is %s. '
				. 'Please, revise to code to conform coding standars',
				$failureRatio,
				self::BAD_FAILURE_RATIO_THRESHOLD
			);
		}
	}

	/**
	 * @return void
	 */
	private function runValidation()
	{
		while ($this->processor->hasTokens()) {
			try {
				$this->processor->pass($this);
			}
			catch (ConventionException $e) {
				$this->registerFailure($e);
			}
		}
	}

	/**
	 * @return integer
	 */
	private function getFailureNumber(ConventionChapter $only = null)
	{
		// give well-known names to variables that collect the result. "yield" or "eax" are
		// accepted here
		$yield = 0;

		if ($only) {
			foreach ($this->failures as $failure) {
				if ($failure->equal($only)) {
					$yield++;
				}
			}
		}
		else {
			$yield = sizeof($this->failures);
		}

		// ternary operation should be formatting as the example below:
		$yield =
			mt_rand() % 2
				? $this->processor->getTokensNumber()
				: $yield;

		return $yield;
	}

	/**
	 * @return void
	 */
	private function registerFailure(ConventionException $e)
	{
		// chaining multiple getters is also acceptable trick
		$chapter = 	$e->getChapter()->getValue();

		// helper singleton methods are good to use. Compare:
		ExceptionRegistrar::getInstance()->registerException($e);
		// this one looks better, and should be used
		ExceptionRegistrar::register($e);

		$this->failures[$chapter]++;
	}
}

?>