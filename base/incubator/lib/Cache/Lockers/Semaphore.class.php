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

final class Semaphore extends Pool
{
	/**
	 * @var Locker
	 */
	private $locker = null;

	/**
	 * @return Semaphore
	 */
	static function getInstance()
	{
		return LazySingleton::instance(__CLASS__);
	}

	/**
	 * @return Semaphore
	 */
	static function set(Locker $locker)
	{
		return self::getInstance()->setLocker($locker);
	}

	static function acquire($key)
	{
		return self::getInstance()->getLocker()->acquire($key);
	}

	static function release($key)
	{
		return self::getInstance()->getLocker()->release($key);
	}

	static function drop($key)
	{
		return self::getInstance()->getLocker()->drop($key);
	}

	/**
	 * @return Locker
	 */
	function getLocker()
	{
		Assert::isNotNull(
			$this->locker,
			'locker is not set'
		);

		return $this->locker;
	}

	/**
	 * @return Semaphore
	 */
	function setLocker(Locker $locker)
	{
		$this->locker = $locker;

		return $this;
	}
}

?>