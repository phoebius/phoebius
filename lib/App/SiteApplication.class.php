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
 * Application infrastructure initializer.
 *
 * Grabs the standart incoming request, wraps it with appropriate objects handles the request
 * by passing those objects to the corresponding route.
 *
 * index.php example:
 * @code
 * $app = new SiteApplication(new ChainedRouter);
 * $app->run();
 * @endcode
 *
 * @ingroup App_Web
 */
class SiteApplication
{
	/**
	 * @var IRouter
	 */
	private $router;

	/**
	 * @var WebContext
	 */
	private $webContext;

	/**
	 * @param IRouter router to use when handling the request. See ChainedRouter as basic impl.
	 */
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
	 * Runs the application.
	 *
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

	/**
	 * Handles the situation when the application failed to found the corresponding route
	 *
	 * @throws Exception
	 * @return void
	 */
	protected function handle404(Trace $trace)
	{
		$this->webContext->getResponse()->setStatus(new HttpStatus(HttpStatus::CODE_404));

		$this->router->getFallbackTrace($trace);
	}

	/**
	 * Handles the abnormal application failover
	 *
	 * @return void
	 */
	protected function handle500(Exception $e)
	{
		$response = $this->webContext->getResponse();

		$response->setStatus(new HttpStatus(HttpStatus::CODE_500));

		$clname = get_class($e);

		$out = <<<EOT
	<h1>Internal Server Error</h1>
	{$clname} : {$e->getMessage()}
	<hr />
	<h2>Call Stack</h2>
	<pre>{$e->getTraceAsString()}</pre>
EOT;

		$response->write($out)->finish();

		if (defined('BUGS_EMAIL')) {
			$this->notify500(BUGS_EMAIL, $e);
		}
	}

	/**
	 * Notify about abnormal application failover
	 *
	 * @return void
	 */
	protected function notify500($email, Exception $e)
	{
		//
		// TODO log this
		//

		$request = $this->webContext->getRequest();

		$message = <<<EOT
Crash at {$request->getHttpUrl()->getHost()}:
{$e->getMessage()}

The request: {$request->getHttpUrl()}
Request method: {$request->getRequestMethod()}

{$e->getTraceAsString()}
EOT;

		mail(
			$email,
			PHOEBIUS_SHORT_PRODUCT_NAME. "crash at {$request->getHttpUrl()->getHost()} (" . get_class($e) . ")",
			$message
		);
	}
}

?>