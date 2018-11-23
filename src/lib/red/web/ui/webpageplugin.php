<?php

namespace red\web\ui
{
	use \red\IPlugin;
	use \red\Obj;

	abstract class WebPagePlugin extends Obj implements IWebPagePlugin
	{
		/**
		 * @var \red\web\ui\WebPage
		 */
		private $page;

		/**
		 * Assign a page to this plugin
		 *
		 * @abstract
		 * @param WebPage $webPage
		 * @return void
		 */
		public function setPage(WebPage $webPage)
		{
			$this->page = $webPage;
			$this->setupHooks();
		}

		public function __construct(WebPage $page=null)
		{
			parent::__construct();
			if($page !== null)
			{
				$this->setPage($page);
			}
		}

		/**
		 * Implement this method to set up the event listeners your plugin uses.
		 *
		 * @abstract
		 * @return void
		 */
		abstract protected function setupHooks();

	}
}