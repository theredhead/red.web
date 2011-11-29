<?php

namespace demo\pages
{
	use red\web\ui\WebPage;
	use red\web\ui\ScriptManager;
	use red\web\http\HttpRequest;
	use red\web\http\HttpResponse;
	
	class BasePage extends WebPage
	{
		const CSS_MAIN_STYLESHEET = '/css/main.css';
		
		public function __construct(\red\web\http\HttpApplication $application)
		{
			parent::__construct($application);
//			$this->registerClientScript(ScriptManager::CDN_URL_JQUERY);
			$this->registerStyleSheet(static::CSS_MAIN_STYLESHEET);
			$this->registerClientScript('/js/events.js');
			$this->loadTemplate();
		}
		
		protected function init(HttpRequest $request, HttpResponse $response)
		{
			parent::init($request, $response);
			$this->autoWireEvents();
		}
	}
}