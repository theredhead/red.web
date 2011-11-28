<?php

namespace demo\pages
{
	use \red\Object;
	use \red\web\http\HttpRequest;
	use \red\web\http\HttpResponse;
	use SplFileObject;

	class TheDelegate extends Object implements \red\web\ui\controls\IDirectoryBrowserDelegate
	{
		protected $rootDirectory = null;
		public function setRootDirectory($root)
		{
			file_exists($root) and is_dir($root) and is_readable($root)
				or static::fail('Root path "%s" does not exist, is not a directory or is unreadable.');

			$this->rootDirectory = $root;
		}

		protected $files = null;
		protected function getFiles()
		{
			if(! is_array($this->files))
			{
				$this->files = array();
				$dir = $this->getRootDirectory();
				if ($this->getCurrentDirectory() != '')
				{
					$dir .= DIRECTORY_SEPARATOR . $this->getCurrentDirectory();
				}

				$iterator = glob($dir . DIRECTORY_SEPARATOR . '*');
				foreach($iterator as $file)
				{
					array_push($this->files, new SplFileObject($file));
				}
			}

			return $this->files;
		}


		public function getFilesystemItemAtIndex($index)
		{
			$files = $this->getFiles();
			return isset($files[$index])
					? $files[$index]
					: null;
		}

		public function numberOfItemsInView()
		{
			return count($this->getFiles());
		}

		public function getRootDirectory()
		{
			return $this->rootDirectory;
		}

		protected $currentDirectory = '';
		public function getCurrentDirectory()
		{
			return $this->currentDirectory;
		}

		public function setCurrentDirectory($newValue)
		{
			$this->currentDirectory = $newValue;
		}
	}

	class Finder extends BasePage
	{
		/**
		 * @var \red\web\ui\controls\DirectoryBrowser
		 */
		protected $finder;

		protected function init(HttpRequest $request, HttpResponse $response)
		{
			parent::init($request, $response);

			$delegate = new TheDelegate();
			$delegate->setRootDirectory($_SERVER['DOCUMENT_ROOT']);
			$this->finder->bind($delegate);
		}


	}
}

#EOF