<?php

namespace demo\pages
{
	use red\web\http\HttpRequest;
	use red\web\http\HttpResponse;
	use red\web\ui\controls\apidoc\Documentor;
	use \ReflectionClass;

	class Documentation extends BasePage
	{
		/**
		 * @var Documentor
		 */
		protected $documentor;
		
		/**
		 * @param HttpRequest $request
		 * @param HttpResponse $response
		 * @return void
		 */
		protected function init(HttpRequest $request, HttpResponse $response)
		{
			parent::init($request, $response);

			$requestUrl = $request->getRequestUrl();
			
			$typeId = isset($requestUrl['class']) 
					? $requestUrl['class'] 
					: 'red.Object';
			
			$typename = NAMESPACE_SEPARATOR . str_replace('.', NAMESPACE_SEPARATOR, $typeId);
		
			try
			{
				$reflector = new ReflectionClass($typename);
				$this->documentor->bind($reflector);
			}
			catch(\ReflectionException $ex)
			{
				
			}
		}
	}
}