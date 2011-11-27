<?php

namespace red\web\http
{
	abstract class HttpApplication extends \red\Object
	{
		/**
		 * Event hook for Plugin infrastructure
		 */
		const EV_BEFORE_PROCESSREQUEST = 0x0001;

		/**
		 * Event hook for Plugin infrastructure
		 */
		const EV_AFTER_PROCESSREQUEST = 0x0002;

		/**
		 * Hold the theme name.
		 * 
		 * @var string
		 */
		protected $theme = 'default';

		/**
		 * @return string
		 */
		public function getTheme()
		{
			return $this->theme;
		}

		/**
		 * @param string $theme
		 */
		public function setTheme($theme)
		{
			$this->theme = $theme;
		}

		/**
		 * Get the path to a resource in the current (or default) theme.
		 *
		 * @todo: figure out a nice way to make this localizable.
		 *
		 * @param $resourceName
		 * @return null|string
		 */
		public function getThemeResourcePath($resourceName)
		{
			static $appThemes = null;
			if ($appThemes === null)
			{
				$appThemes = realpath(realpath($_SERVER['DOCUMENT_ROOT']) .
				                      DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'app-themes');
			}
			$result = null;
			foreach(array_unique(array($this->getTheme(), 'default')) as $theme)
			{
				$resourcePath = $appThemes . DIRECTORY_SEPARATOR . $theme . DIRECTORY_SEPARATOR . $resourceName;
				if (file_exists($resourcePath))
				{
					$result = $resourcePath;
					break;
				}
			}
			return $result;
		}

		/**
		 * Log a message.
		 *
		 * @param $va_arg string (or sprintf parameters) to log.
		 * @return void
		 */
		protected function log($va_arg)
		{
			$message = func_num_args() === 1
					? $va_arg
					: call_user_func_array('sprintf', func_get_args());

			error_log($message, 4);
		}

		/**
		 * Translate a virtual path (absolute path as seen on the webserver)
		 * to a path on the servers' local filesystem.
		 * 
		 * @param $virtualPath
		 * @return string
		 */
		public function mapPath($virtualPath)
		{
			return realpath($_SERVER['DOCUMENT_ROOT']) . DIRECTORY_SEPARATOR . $virtualPath;
		}

		/**
		 * Handles requests where the path starts with '!'
		 *
		 * @param HttpRequest $request
		 * @param HttpResponse $response
		 * @return void
		 */
		private function handleFrameworkRequest(HttpRequest $request, HttpResponse $response)
		{
			$path = $request->getRequestUrl()->getPath();
			$action = array_shift($path);
			$action = substr($action, 1);

			switch($action)
			{
				case 'theme-css' :

					foreach($path as $resource)
					{
						$resourcePath = $this->getThemeResourcePath($resource . '.css');
						if (file_exists($resourcePath) && is_readable($resourcePath))
						{
							$response->writeLn(sprintf('/* -----8<----- %s -----8<----- */', $resource));
							$response->write(file_get_contents($resourcePath));
							$response->writeLn(sprintf('/* -----8<----- %s -----8<----- */', $resource));
						}
					}

					$response->setHeader('Content-type', 'text/css; charset=UTF-8');
					$response->send();
					exit;
					break;

				case 'theme-js' :
					foreach($path as $resource)
					{
						$resourcePath = $this->getThemeResourcePath($resource . '.js');
						if (file_exists($resourcePath) && is_readable($resourcePath))
						{
							$response->writeLn(sprintf('/* -----8<----- %s -----8<----- */', $resource));
							$response->write(file_get_contents($resourcePath));
							$response->writeLn(sprintf('/* -----8<----- %s -----8<----- */', $resource));
						}
					}

					$response->setHeader('Content-type', 'text/javascript; charset=UTF-8');
					$response->send();
					exit;
					break;
			}
		}

		/**
		 * Gets called right before processRequest. part of plugin infrastructure.
		 *
		 * @param HttpRequest $request
		 * @param HttpResponse $response
		 * @return void
		 */
		protected function beforeProcessRequest(HttpRequest $request, HttpResponse $response)
		{
			$path = $request->getRequestUrl()->getPathAsString();

			if (strlen($path) > 2 && substr($path, 0, 2) === '/!')
			{
				$this->handleFrameworkRequest($request, $response);
			}
		}

		/**
		 * Implement this method to handle the request.
		 *
		 * @abstract
		 * @param HttpRequest $request
		 * @param HttpResponse $response
		 * @return void
		 */
		abstract public function processRequest(HttpRequest $request, HttpResponse $response);

		/**
		 * Gets called immediately after processRequest. part of plugin infrastructure.
		 *
		 * @param HttpRequest $request
		 * @param HttpResponse $response
		 * @return void
		 */
		protected function afterProcessRequest(HttpRequest $request, HttpResponse $response)
		{

		}

		/**
		 * Solidifies the way an application must process requests.
		 *
		 * @throws \Exception
		 * @param HttpRequest $request
		 * @param HttpResponse $response
		 * @return void
		 */
		final protected function main(HttpRequest $request, HttpResponse $response)
		{
			try
			{
				$this->beforeProcessRequest($request, $response);
				$this->processRequest($request, $response);
				$this->afterProcessRequest($request, $response);
			}
			catch(\Exception $ex)
			{
				$this->log($ex);
				throw $ex;
			}
		}

		static public function start()
		{
			$request = HttpRequest::withRequestArray($_SERVER);
			$response = new HttpResponse();
			
			$reflector = new \ReflectionClass(get_called_class());
			$application = $reflector->newInstance();

			$application->main($request, $response);

			$response->send();
		}
	}
}