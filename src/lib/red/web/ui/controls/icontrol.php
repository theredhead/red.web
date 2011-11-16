<?php

namespace red\web\ui\controls
{
	interface IControl
	{
		/**
		 * @return WebPage The page this control is a child of
		 */
		public function getPage();
		
		/**
		 * @return string A string that can be used to uniquely identify this instance.
		 */
		public function getUniqueId();
		
		/**
		 * Called before the document is serialized to html
		 * 
		 * @return void
		 */
		public function preRender();
		
		/**
		 * Called during request processing if there was a postback
		 */
		public function notePostback(\red\web\http\HttpRequest $request);
	}
}