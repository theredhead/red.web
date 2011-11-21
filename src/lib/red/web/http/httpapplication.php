<?php

namespace red\web\http
{
	abstract class HttpApplication extends \red\Object
	{
		public function mapPath($virtualPath)
		{
			return realpath($_SERVER['DOCUMENT_ROOT']) . DIRECTORY_SEPARATOR . $virtualPath;
		}

		abstract public function processRequest(HttpRequest $request, HttpResponse $response);
		
		static public function start()
		{
			$request = HttpRequest::withRequestArray($_SERVER);
			$response = new HttpResponse();
			
			$reflector = new \ReflectionClass(get_called_class());
			$application = $reflector->newInstance();
			
			$application->processRequest($request, $response);
			
			$response->send();
		}
	}
}