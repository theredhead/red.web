<?php

namespace red\web\ui\controls
{	
	interface IStateful
	{
		/**
		 * @return PropertyBag
		 */
		public function getState();
		/**
		 * @param PropertyBag $state
		 */
		public function setState(PropertyBag $state);
	}
}