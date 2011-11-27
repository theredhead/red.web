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
		public function __construct()
		{
			session_start();
			parent::__construct();

			language($this->getLanguage());
		}

		public function setTheme($theme)
		{
			$_SESSION['theme'] = $theme;
		}

		public function getTheme()
		{
			return isset($_SESSION['theme']) ? $_SESSION['theme'] : 'default';
		}

		public function setLanguage($language)
		{
			$_SESSION['lang'] = $language;
			language($language);
		}
		public function getLanguage()
		{
			return isset($_SESSION['lang']) ? $_SESSION['lang'] : 'en-us' ;
		}

		/**
		 * 
		 *
		 * @param \red\web\http\HttpRequest $request
		 * @param \red\web\http\HttpResponse $response 
		 */
		public function processRequest(\red\web\http\HttpRequest $request, \red\web\http\HttpResponse $response)
		{
			$path = $request->getRequestUrl()->getPathAsString();
			switch($path)
			{
				case '/addressbook' :
					$page = new pages\AddressBook($this);
					break;

				case '/documentation' :
					$page = new pages\Documentation($this);
					break;

				case '/tests' :
					$page = new pages\Tests($this);
					break;

				default :
					$page = new pages\Hello($this);
					break;
			}

			$page->processRequest($request, $response);
		}
	}
}

#EOF