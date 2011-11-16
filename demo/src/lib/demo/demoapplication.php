<?php
namespace demo
{
	/**
	 * Demo application
	 * 
	 * This simple class defines the behaviour of the demo web application as
	 * a whole.
	 */
	class DemoApplication extends \red\web\http\HttpApplication
	{
		/**
		 * 
		 *
		 * @param \red\web\http\HttpRequest $request
		 * @param \red\web\http\HttpResponse $response 
		 */
		public function processRequest(\red\web\http\HttpRequest $request, \red\web\http\HttpResponse $response)
		{
			if (isset($_REQUEST['language']))
			{
				language($_REQUEST['language']);
			}

			$path = $request->getRequestUrl()->getPathAsString();
			switch($path)
			{
				case '/addressbook' :
					$page = \demo\pages\AddressBook::withTemplate();
					break;

				default :
					$page = \demo\pages\Hello::withTemplate();
					break;
			}

			$page->processRequest($request, $response);
		}
	}
}

#EOF