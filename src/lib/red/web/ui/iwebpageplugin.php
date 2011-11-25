<?php

namespace red\web\ui
{
	use \red\IPlugin;
	use \red\web\ui\WebPage;

	interface IWebPagePlugin extends IPlugin
	{
		/**
		 * Assign a page to this plugin
		 * 
		 * @abstract
		 * @param WebPage $webPage
		 * @return void
		 */
		public function setPage(WebPage $webPage);
	}
}