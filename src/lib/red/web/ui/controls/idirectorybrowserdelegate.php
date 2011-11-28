<?php

namespace red\web\ui\controls
{
	/**
	 * This is a draft.
	 */
	interface IDirectoryBrowserDelegate
	{
		/**
		 * @abstract
		 * @param $index
		 * @return \SplFileInfo
		 */
		public function getFilesystemItemAtIndex($index);
				
		public function numberOfItemsInView();

		public function getRootDirectory();
		
		public function getCurrentDirectory();
		public function setCurrentDirectory($newValue);
	}
}

#EOF