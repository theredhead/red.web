<?php

namespace red\web\ui
{
	/**
	 * This interface can be tacked onto any element you wish to make themable
	 */
	interface IThemable
	{
		/**
		 * get an array of resource types to try and register.
		 *
		 * array should hold filename extensions to register as values
		 *
		 * example:
		 *  return array('css', 'js');
		 *
		 *
		 *
		 * @return array
		 */
		static public function getThemeResourceTypes();
	}
}

#EOF