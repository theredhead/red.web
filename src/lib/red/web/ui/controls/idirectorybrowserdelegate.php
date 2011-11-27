<?php

namespace red\web\ui\controls
{
	/**
	 * This is a draft.
	 */
	interface IDirectoryBrowserDelegate
	{
		public function getFilesystemItemAtIndex($index);
				
		public function numberOfItemsInView();

		public function getRootDirectory();
		
		public function getCurrentDirectory();
		public function setCurrentDirectory($newValue);
	}
}

#EOF