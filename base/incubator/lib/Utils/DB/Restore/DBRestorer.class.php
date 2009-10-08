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

class DBRestorer
{
	/**
	 * @var DBRestore
	 */
	private $driver;

	private static $drivers = array
	(
		DBDriver::MYSQL => 'MySqlRestore',
		DBDriver::PGSQL => 'PgSqlRestore'
	);

	function __construct(DBConnector $db)
	{
		Assert::isTrue(
			isset(self::$drivers[$db->getDriver()->getId()]),
			"unknown driver is specified: {$db->getDriver()}"
		);

		$className = self::$drivers[$db->getDriver()->getId()];

		$driver = new ReflectionClass($className);
		Assert::isTrue(
			$driver->isSubclassOf(new ReflectionClass('DBRestore')),
			"{$className} should implement DBRestore"
		);
		$this->driver = $driver->newInstance($db);
	}

	/**
	 * return DBRestorer
	 */
	static function create(DBConnector $db)
	{
		return new self($db);
	}

	function restore($file)
	{
		$this->driver->make($file);
	}
}

?>