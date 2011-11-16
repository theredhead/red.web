<?php

namespace demo\pages
{
	use \red\web\ui\WebPage;
	use \red\web\ui\ScriptManager;
	
	class BasePage extends WebPage
	{
		const CSS_MAIN_STYLESHEET = '/css/main.css';
		
		public function __construct()
		{
			parent::__construct();
			$this->registerStyleSheet(static::CSS_MAIN_STYLESHEET);
			$this->registerClientScript('/js/events.js');
		}
		
		protected function init()
		{
			parent::init();
			$this->autoWireEvents();
		}
	}
}