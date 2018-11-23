<?php

namespace red\web\http
{
	use \red\Obj;

	/**
	 * HttpApplicationPlugin defines one possible skeleton for an IHttpApplicationPlugin
	 * based plugin.
	 */
	abstract class HttpApplicationPlugin extends Obj implements IHttpApplicationPlugin
	{
		/**
		 * @var \red\web\http\HttpApplication
		 */
		private $application;

		/**
		 * @return \red\web\http\HttpApplication
		 */
		protected function getApplication()
		{
			return $this->application;
		}

		/**
		 * @param \red\web\http\HttpApplication $application
		 */
		public function setApplication($application)
		{
			$this->application = $application;
			$this->setupHooks();
		}

		public function __construct(HttpApplication $application=null)
		{
			parent::__construct();
			if($application !== null)
			{
				$this->setApplication($application);
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