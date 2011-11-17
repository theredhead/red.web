<?php

namespace red\web\ui\controls
{
	/**
	 * This is a draft.
	 */
	interface IDirectoryBrowserDelegate
	{
		public function getFilesystemItemAtIndex(DirectoryBrowser $browser, $index);
				
		public function numberOfItemsInView();

		public function getRootDirectory();
		
		public function getCurrentDirectory();
		public function setCurrentDirectory($newValue);
	}
	
	/**
	 * Directory browser gives you a control that can display files in a
	 * directory.
	 * 
	 * @TODO: implement this control.
	 */
	class DirectoryBrowser extends BindableControl
	{
		const DISPLAYMODE_ICONS		= 'icons';
		const DISPLAYMODE_LIST		= 'list';
		const DISPLAYMODE_DETAILS	= 'details';

		/**
		 * The display mode value used when one isn't explicitly set.
		 */
		const DEFAULT_DISPLAY_MODE = self::DISPLAYMODE_ICONS;
		
		// <editor-fold defaultstate="collapsed" desc="State property string DisplayMode">
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
		// </editor-fold>
		
		
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

		/**
		 * implements BindableControls' buildControl to build this controls
		 * inner markup. 
		 */
		protected function buildControl()
		{
			switch($this->getDisplayMode())
			{
				case self::DISPLAYMODE_ICONS :
					$this->buildIconView();
					break;
				case self::DISPLAYMODE_LIST :
					$this->buildListView();
					break;
				case self::DISPLAYMODE_DETAILS :
					$this->buildDetailView();
					break;
				default:
					static::fail("Unknown DisplayMode '%s'", $this->getDisplayMode());
					break;
			}
		}

		public function canBindTo($dataItem)
		{
			
		}
	}
}

#EOF