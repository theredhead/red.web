<?php

namespace red\web
{
	use \red\Object;
	use \red\web\URL;
	use \red\web\http\HttpRequest;
	use \red\web\http\HttpResponse;

	class Client extends Object
	{

		/**
		 * @param HttpRequest $request
		 * @return HttpResponse
		 */
		public function send(HttpRequest $request)
		{
			$response = new HttpResponse();
			$curl = curl_init($request->getRequestUrl());
		}
	}
}
#EOF