<?php

namespace red\io
{
	use red\Object;
	use SplFileInfo;
	use ArrayIterator;

	class FileAggregator extends Object implements \IteratorAggregate
	{
		/**
		 * @var Callable null
		 */
		protected $matchFunc = null;
		
		/**
		 * @param \SplFileInfo $file
		 * @return void
		 */
		public function isMatch(SplFileInfo $file)
		{
			return call_user_func($this->matchFunc, $file);
		}

		/**
		 * Holds the files we've collected.
		 *
		 * @var array[SplFileInfo]
		 */
		protected $files = array();

		/**
		 * Get a new Aggregator with the files from this one after applying $filterFunc
		 *
		 * @param $filterFunc
		 * @return FileAggregator
		 */
		public function filter($filterFunc)
		{
			$files = array();
			foreach($this->getIterator() as $item)
			{
				if (call_user_func($filterFunc, $item))
				{
					array_push($files, $item);
				}
			}
			$result = new static();
			$result->matchFunc = $this->matchFunc;
			$result->files = $files;
			return $result;
		}

		/**
		 * Apply a callback to each item and return the result
		 *
		 * @param Callable $callback
		 * @return array
		 */
		public function each($callback)
		{
			assert(is_callable($callback));
			foreach($this->getIterator() as $item)
			{
				call_user_func($callback, $item);
			}
			return $this;
		}

		/**
		 * (PHP 5 &gt;= 5.1.0)<br/>
		 * Retrieve an external iterator
		 * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
		 * @return Traversable An instance of an object implementing Iterator or
		 * Traversable
		 */
		public function getIterator()
		{
			return new ArrayIterator($this->files);
		}

		public function __construct()
		{
			parent::__construct();

			$this->matchFunc = function() {return true;};

			if (func_num_args() > 0)
			{
				$args = func_get_args();
				foreach($args as $ix => $arg)
				{
					if (is_callable($arg))
					{
						$this->matchFunc = $arg;
					}
					else if(is_dir($arg) && is_readable($arg))
					{
						$this->aggregateFilesFromDirectory($arg);
					}
					else
					{
						static::fail('Unrecognied argument to cunstructor at position %d', $ix);
					}
				}
			}
		}

		/**
		 * Add all files beneath the indicated directory to the internal list.
		 *
		 * @param string|SplFileInfo $directory
		 * @return void
		 */
		public function aggregateFilesFromDirectory($directory)
		{
			assert(is_dir($directory) && is_readable($directory));

			$iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory));
			foreach($iterator as $item)
			{
				if ($this->isMatch($item))
				{
					array_push($this->files, $item);
				}
			}
		}
	}
}

#EOF