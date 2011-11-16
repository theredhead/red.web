<?php

namespace red\web\http
{
	abstract class HttpApplication extends \red\Object
	{
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