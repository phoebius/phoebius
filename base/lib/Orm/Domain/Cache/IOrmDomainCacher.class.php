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
 * @ingroup OrmDomainCache
 */
interface IOrmDomainCacher
{
	/**
	 * @return void
	 */
	function set(OrmDomain $ormDomain);

	/**
	 * @return OrmDomain
	 */
	function get($name);
}

?>