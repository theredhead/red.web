<?php

namespace tools
{
	use red\cli\CommandLineTool;

	require_once '../bootstrap.php';

	assert(defined('REDWEB_BOOTSTRAP_FILE') && file_exists(REDWEB_BOOTSTRAP_FILE));

	class TypeLister extends CommandLineTool
	{
		/**
		 * scan directory for php files and include them.
		 *
		 * @param $inDirectory
		 * @return void
		 */
		protected function includeAllPhpFiles($inDirectory, $filterFunction = null)
		{
			$iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($inDirectory));

			foreach($iterator as $file)
			{
				if (preg_match('/\.php$/', $file) === 1)
				{
					if ($filterFunction == null || call_user_func($filterFunction, $file))
					{
						include_once $file;
					}
				}
			}
		}

		public function getDeclaredClasses($filterFunction)
		{
			$result = array();

			foreach(get_declared_classes() as $class)
			{
				$reflector = new \ReflectionClass($class);
				if (call_user_func($filterFunction, $reflector))
				{
					array_push($result, $class);
				}
			}

			return $result;
		}

		/**
		 * @param array $arguments ($argv will be passed in)
		 * @return void
		 */
		protected function main(array $arguments)
		{
			$this->includeAllPhpFiles(dirname(REDWEB_BOOTSTRAP_FILE) . DIRECTORY_SEPARATOR . 'red');
			$this->includeAllPhpFiles(dirname(REDWEB_BOOTSTRAP_FILE) . DIRECTORY_SEPARATOR . 'tests');

			$testable = $this->getDeclaredClasses(function($o){
					return $o->implementsInterface('\\red\\ITestable');
				});

			$cmd = sprintf('php -q unittest.php -t %s', implode(' -t ', array_map(function($o){return str_replace(NAMESPACE_SEPARATOR, '.', $o);}, $testable)));
			echo $cmd;
			system($cmd);
		}
	}

	CommandLineTool::run(new TypeLister());
}
