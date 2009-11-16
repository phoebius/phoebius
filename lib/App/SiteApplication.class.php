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
 * @ingroup App_Web
 */
abstract class SiteApplication
{
	/**
	 * @var IRouter
	 */
	private $router;
	
	/**
	 * @var WebContext
	 */
	private $webContext;
	
	function __construct(IRouter $router)
	{
		$this->router = $router;
		
		$request = new WebRequest(
			new WebRequestDictionary($_SERVER), $_GET, $_POST, $_COOKIE, $_FILES
		);
		$this->webContext = new WebContext(
			$request,
			new WebResponse($request),
			new WebServerState(new WebServerStateDictionary($_SERVER))
		);
	}
	
	/**
	 * @return IRouter
	 */
	function getRouter()
	{
		return $this->router;
	}
	
	/**
	 * @return void
	 */
	function run()
	{
		try {
			try {
				$trace = $this->router->getTrace($this->webContext);
				$trace->handle();
			}
			catch (RouteException $e) {
				$trace = $this->router->getFallbackTrace($trace);
				$trace->handle();
			}
		}
		catch (Exception $e) {
			$this->handle500($e);
		}
	}
	
	protected function handle404(Trace $trace)
	{
		$this->webContext->getResponse()->setStatus(new HttpStatus(HttpStatus::STATUS_404));
		
		$this->router->getFallbackTrace($trace);
	}
	
	protected function handle500(Exception $e)
	{
		$response = $this->webContext->getResponse();
		$request = $this->webContext->getRequest();
		
		$response->setStatus(new HttpStatus(HttpStatus::STATUS_500));
		
		$out = <<<EOT
	<h1>Internal Server Error</h1>
	{$e->getMessage()}
	<hr />
	<h2>Call Stack</h2>
	<pre>{$e->getTraceAsString()}</pre>
EOT;
		
		$response->write($out)->finish();

		if (defined('BUGS_EMAIL')) {
			
			$message = <<<EOT
Crash at {$request->getHttpUrl()->getHost()}:
{$e->getMessage()}

The request: {$request->getHttpUrl()}
Request method: {$request->getRequestMethod()}

{$e->getTraceAsString()}
EOT;
			
			mail(
				BUGS_EMAIL,
				PHOEBIUS_SHORT_PRODUCT_NAME. "crash at {$request->getHttpUrl()->getHost()} (" . get_class($e) . ")",
				$message
			);
		}
	}
}

?>