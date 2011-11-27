<?php

namespace tools
{
	use red\cli\CommandLineTool;
	use \red\io\FileAggregator;
	use SplFileInfo;

	assert("defined('REDWEB_BOOTSTRAP_FILE') && file_exists(REDWEB_BOOTSTRAP_FILE);");

	/**
	 * UnitTestRunner scans for test classes and runs their test methods.
	 */
	class UnitTestRunner extends CommandLineTool
	{
		protected $failedTestCount = 0;
		protected $classesToTest = array();

		/**
		 * add a class to be tested by typeId
		 *
		 * @param $typeId
		 * @return void
		 */
		protected function addTest($typeId)
		{
			array_push($this->classesToTest, $typeId);
		}

		/**
		 * add all ITestable classes found inside a directory
		 *
		 * @param $directory
		 * @return void
		 */
		protected function addTestablesFromDirectory($directory)
		{
			assert('is_dir($directory) && is_readable($directory); // enforce that we can operate on the given dir');

			$before = get_declared_classes();

			$aggregator = new FileAggregator($directory,
					function(SplFileInfo $file) {
						return strlen($file) > 4 && substr($file, -4) === '.php';
					});

			$aggregator->each(function($file){ include_once $file ;});

			$after = get_declared_classes();

			$newClasses = array_diff($after, $before);
			
			foreach($newClasses as $item)
			{
				$this->addTest(str_replace(NAMESPACE_SEPARATOR, '.', $item));
			}
		}

		/**
		 * Test results are kept in here, on a per typeId, per method per result basis.
		 *
		 * @var array
		 */
		protected $results = array();

		/**
		 * Create the code to include the bootstrap file and set up some php parameters.
		 *
		 * @param $testFileName
		 * @return void
		 */
		protected function createBootstrap($testFileName)
		{
			$bootstrap  = '<?php '
						. PHP_EOL
			            . sprintf("ini_set('include_path', '%s');", dirname(REDWEB_BOOTSTRAP_FILE))
						. PHP_EOL
						.'assert_options(ASSERT_ACTIVE, 1);'
						. PHP_EOL
						.'assert_options(ASSERT_WARNING, 0);'
						. PHP_EOL
						.'assert_options(ASSERT_QUIET_EVAL, 1);'
						. PHP_EOL
						.'assert_options(ASSERT_CALLBACK, function($file, $line, $code)'
			                    .'{printf("%s|%s|%s\n", $file, $line, $code);});'
			            . PHP_EOL
			            . sprintf("require_once '%s';", REDWEB_BOOTSTRAP_FILE)
			            . PHP_EOL
			            . sprintf("require_once '%s';", $testFileName)
						. PHP_EOL;

			return $bootstrap;
		}

		/**
		 * Write out a test stub and return the name of the file written.
		 *
		 * @return string
		 */
		protected function writeTestStub($testClassFile, $testClassName, $testMethodName)
		{
			$tmp = realpath(sys_get_temp_dir());

			$file = implode(DIRECTORY_SEPARATOR, array(
					$tmp,
					implode('-', array(
							 str_replace(NAMESPACE_SEPARATOR, '.', $testClassName)
							,$testMethodName
							,'runner'
					)).'.php'));

			$content    = $this->createBootstrap($testClassFile)
						. PHP_EOL
			            . sprintf('$reflector = new ReflectionClass(\'%s\');', addslashes($testClassName))
						. PHP_EOL
			            . '$instance = $reflector->newInstance();'
		                . PHP_EOL
						. sprintf('$method = $reflector->getMethod(\'%s\');', $testMethodName)
						. PHP_EOL
						. 'try {'
						. '$method->invoke($instance);'
						. '} catch(\\Exception $ex) {printf("%s|%s|%s\n", $ex->getFile(), $ex->getLine(), $ex->getCode());}'
			            . PHP_EOL
					;

			file_put_contents($file, $content);

			return $file;
		}

		/**
		 * expand commandline options to runtime application settings
		 *
		 * @param $name
		 * @param $value
		 * @return void
		 */
		protected function setOption($name, $value)
		{
			switch(strtolower($name))
			{
				case 't':
				case 'test':
					$this->addTest($value);
					break;

				case 'd':
				case 'dir':
					$this->addTestablesFromDirectory(realpath($value));
					break;
				
				default:
					parent::setOption($name, $value);
					break;
			}
		}


		/**
		 * Test a single class.
		 *
		 * @param $typeId
		 * @return void
		 */
		protected function testClass($typeId)
		{
			$className = str_replace('.', NAMESPACE_SEPARATOR, $typeId);
			$reflector = new \ReflectionClass($className);
			if ($reflector->implementsInterface('\\red\\ITestable'))
			{
				$this->results[$typeId] = array();
				foreach($reflector->getMethods(\ReflectionMethod::IS_PUBLIC) as $method)
				{
					if ($method instanceof \ReflectionMethod && $method->getNumberOfParameters() == 0)
					{
						$docBlock = $method->getDocComment();
						if (strpos($docBlock, '@test') > -1)
						{
							$executor = $this->writeTestStub($reflector->getFileName(), $className, $method->getShortName());
							$cmd = sprintf('php -q %s', $executor);
							ob_start();
							system($cmd);
							$result = ob_get_clean();
							if ($result == '')
							{
								$result = 'OK';
							}
							else
							{
								$this->failedTestCount ++;
							}
							$this->results[$typeId][$method->getShortName()] = $result;
						}
					}
				}
			}
		}

		/**
		 * Application entry point.
		 * 
		 * @param array $arguments
		 * @return integer
		 */
		protected function main(array $arguments)
		{
			foreach($this->classesToTest as $classToTest)
			{
				$this->testClass($classToTest);
			}

			echo $this->export($this->results);
			return $this->failedTestCount;
		}

		/**
		 * Export the test results to xml
		 *
		 * @param array $results
		 * @return string
		 */
		protected function export(array $results)
		{
			$document = new \DOMDocument('1.0', 'UTF-8');
			$document->preserveWhiteSpace = false;
			$document->formatOutput = true;

			$root = $document->appendChild($document->createElement('test-results'));
			foreach($results as $typeId => $methods)
			{
				$group = $root->appendChild($document->createElement('test-group'));
				$group->setAttribute('typeId', $typeId);

				foreach($methods as $method => $result)
				{
					$test = $group->appendChild($document->createElement('test'));
					$test->setAttribute('name', $method);
					if (is_string($result) && $result == 'OK')
					{
						$test->setAttribute('result', 'PASSED');
					}
					else
					{
						$test->setAttribute('result', 'FAILED');
						foreach(explode("\n", $result) as $failure)
						{
							if (strlen($failure) > 0)
							{
								list($file, $line, $code) = explode('|', $failure);
								$details = $test->appendChild($document->createElement('test-failure'));

								$elFile = $details->appendChild($document->createElement('file'));
								$elFile->appendChild($document->createTextNode($file));

								$elLine = $details->appendChild($document->createElement('line'));
								$elLine->appendChild($document->createTextNode($line));

								if (strlen($code) > 0)
								{
									$elCode = $details->appendChild($document->createElement('code'));
									$elCode->appendChild($document->createTextNode($code));
								}
							}
						}
					}
				}
			}

			return $document->saveXML($document);
		}
	}
}