<?php

namespace red\web\ui\controls
{
	class DirectoryBrowser extends BaseControl
	{
		const DISPLAYMODE_ICONS		= 'icons';
		const DISPLAYMODE_LIST		= 'list';
		const DISPLAYMODE_DETAILS	= 'details';

		/**
		 * The display mode value used when one isn't explicitly set.
		 */
		const DEFAULT_DISPLAY_MODE = self::DISPLAYMODE_ICONS;
		
		/**
		 * Get the current display mode
		 *
		 * @return string
		 */
		public function getDisplayMode()
		{
			return isset($this->state['mode'])
					? $this->state['mode']
					: self::DEFAULT_DISPLAY_MODE;
		}
		/**
		 * @param string $newDisplayMode 
		 */
		public function setDisplayMode($newDisplayMode)
		{
			$this->state['mode'] = $newDisplayMode;
		}
		
		/**
		 * expand logical properties from the template
		 *
		 * @param type $name
		 * @param type $value 
		 */
		protected function setAttribute($name, $value)
		{
			switch(strtolower($name))
			{
				case 'mode':
					$this->setDisplayMode($value);
					break;
				default:
					parent::setAttribute($name, $value);
					break;
			}
		}
	}
}

#EOF