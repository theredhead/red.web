<?php

namespace red\web
{
	use \red\Obj;
	use \red\web\URL;
	use \red\web\http\HttpRequest;
	use \red\web\http\HttpResponse;

	class Client extends Obj
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