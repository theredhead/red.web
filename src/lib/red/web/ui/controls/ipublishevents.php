<?php

namespace red\web\ui\controls
{
	interface IPublishEvents
	{
		/**
		 * @return array with event names
		 */
		public function getPublishedEvents();
	}
}