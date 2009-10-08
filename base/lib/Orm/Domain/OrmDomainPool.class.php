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
 * @ingroup Dal
 */
final class OrmDomainPool extends Pool
{
	/**
	 * @var OrmDomain
	 */
	private $default;

	/**
	 * @var array of OrmDomain
	 */
	private $domains = array();

	/**
	 * @return OrmDomainPool
	 */
	static function getInstance()
	{
		return LazySingleton::instance(__CLASS__);
	}

	/**
	 * @return OrmDomain
	 */
	static function getDefault()
	{
		return self::getInstance()->getDefaultOrmDomain();
	}

	/**
	 * @return OrmDomain
	 */
	static function get($name)
	{
		return self::getInstance()->getOrmDomain($name);
	}

	/**
	 * @return OrmDomainPool
	 */
	static function add(OrmDomain $ormDomain, $setDefault = false)
	{
		return self::getInstance()->addOrmDomain($ormDomain, $setDefault);
	}

	/**
	 * @return OrmDomain
	 */
	function getDefaultDomain()
	{
		Assert::isTrue(
			!!$this->default,
			'no items inside, so default item is not specified'
		);

		return $this->default;
	}

	/**
	 * @return OrmDomainPool
	 */
	function addOrmDomain(OrmDomain $ormDomain, $setDefault = false)
	{
		$name = $ormDomain->getName();

		$this->domains[$name] = $ormDomain;

		if (!$this->default || $setDefault) {
			$this->default = $ormDomain;
		}

		return $this;
	}

	/**
	 * @return OrmDomain
	 */
	function getOrmDomain($name)
	{
		if (!isset($this->domains[$name])) {
			throw new ArgumentException('name', 'OrmDomain not found');
		}

		$ormDomain = $this->domains[$name];

		return $ormDomain;
	}

	/**
	 * @return OrmDomain
	 */
	function getDefaultOrmDomain()
	{
		return $this->default;
	}
}

?>