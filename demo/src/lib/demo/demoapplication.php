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
					$page = new pages\AddressBook();
					break;

				case '/documentation' :
					$page = new pages\Documentation();
					break;

				default :
					$page = new pages\Hello();
					break;
			}

			$page->processRequest($request, $response);
		}
	}
}

#EOF