<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright (c) 2009 Scand Ltd.
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
 * Grabs the standart incoming request, wraps it with appropriate objects, and handles the request
 * by passing those objects to the corresponding route.
 *
 * Consider index.php example:
 * @code
 * $app = new SiteApplication(new ChainedRouter);
 * $app->run();
 * @endcode
 *
 * The best practise is to implement your own SiteApplication class by extending this one
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
	 * @var MvcDispatcher
	 */
	private $dispatcher;

	/**
	 * @var WebContext
	 */
	private $webContext;

	/**
	 * @param IRouter router to use when handling the request. See Router as basic impl.
	 */
	function __construct(IRouter $router)
	{
		$this->router = $router;
		$this->dispatcher = new MvcDispatcher();

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
	 * Gets the WebContext
	 *
	 * @return WebContext
	 */
	function getWebContext()
	{
		return $this->webContext;
	}

	/**
	 * Gets the application router
	 *
	 * @return IRouter
	 */
	function getRouter()
	{
		return $this->router;
	}

	/**
	 * Gets the application dispatcher
	 *
	 * @return MvcDispatcher
	 */
	function getDispatcher()
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
				$routeData = $this->router->process($this->webContext->getRequest());
				
				$this->dispatcher->handle($routeData, $this->webContext);
			}
			catch (DispatchException $e) {
				$this->handle404($e);
			}
			catch (RouteException $e) {
				$this->handle404($e);
			}
		}
		catch (Exception $e) {
			$this->handle500($e);
		}
	}

	/**
	 * Handles the situation when the application failed to found the corresponding route
	 *
	 * @throws Exception in case of application fault
	 * @return void
	 */
	protected function handle404(Exception $e)
	{
		$response = $this->webContext->getResponse();
		
		$response->setStatus(new HttpStatus(HttpStatus::CODE_404));

		$clname = get_class($e);

		$out = <<<EOT
	<h1>Not Found</h1>
	{$clname} : {$e->getMessage()}
	<hr />
	<h2>Call Stack</h2>
	<pre>{$e->getTraceAsString()}</pre>
EOT;

		$response->write($out)->finish();
	}

	/**
	 * The failover stub. Handles the application fault
	 *
	 * @param Exception $e uncaught exception that caused fault
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
	 * Notify about the fault
	 *
	 * @param string $email address to be notified about the fault
	 * @param Exception $e uncaught exception that caused application fault
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