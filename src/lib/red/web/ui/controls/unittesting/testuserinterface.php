<?php

namespace red\web\ui\controls\unittesting
{
	use \red\web\ui\controls\TemplateControl;
	use \red\web\ui\controls\ButtonClickedEventArgument;
	use \red\web\ui\controls\Button;
	use \red\web\ui\controls\Container;
	use \red\web\ui\html\HtmlTag;
	use \red\web\ui\html\HtmlText;
	use \red\web\ui\IThemable;
	use \red\io\FileAggregator;

	use ReflectionClass;

	class TestUserInterface extends TemplateControl implements IThemable
	{
		/**
		 * The button that will trigger the running of the selected tests
		 * 
		 * @var \red\web\ui\controls\Button;
		 */
		protected $btnRunSelectedTests;

		/**
		 * @var Container
		 */
		protected $listContainer;

		/**
		 * @var Container
		 */
		protected $resultContainer;

		/**
		 * @var \red\web\ui\controls\StaticText
		 */
		protected $hdrStatusText;

		/**
		 * The typeId collection of testables that could be tested.
		 * @var array
		 */
		protected $foundTestables = array();

		public function setTestDirectory($directory)
		{
			$this->state['testdir'] = $directory;
		}
		public function getTestDirectory()
		{
			return isset($this->state['testdir'])
					? $this->state['testdir']
					: implode(DIRECTORY_SEPARATOR, array(realpath(dirname(REDWEB_BOOTSTRAP_FILE)) , 'tests'));
		}

		public function getPathToTool()
		{
			return isset($this->state['tool'])
					? $this->state['tool']
					: implode(DIRECTORY_SEPARATOR, array(realpath(dirname(REDWEB_BOOTSTRAP_FILE)) , '..' ,'..', 'tools', 'unittest.php'));
		}

		protected function templateLoaded()
		{
			$this->foundTestables = $this->findTestables($this->getTestDirectory());
			sort($this->foundTestables);

			parent::templateLoaded();

			$list = $this->listContainer;
			foreach($this->foundTestables as $testable)
			{
				$item = $list->appendChild(new HtmlTag('li'));
				$this->createTestableSelectable($item, $testable);
			}

			$this->btnRunSelectedTests->registerEventListener(Button::EV_CLICKED, 'onBtnRunSelectedTests_Clicked', $this);
		}

		/**
		 * @param \red\web\ui\html\HtmlTag $li
		 * @param string $testable
		 * @return void
		 */
		protected function createTestableSelectable(HtmlTag $li, $testable)
		{
			$label = new HtmlTag('label');
			$checkbox = new \red\web\ui\controls\Checkbox();
			$checkbox->setChecked(true);
			$label->appendChild($checkbox);
			$label->appendChild(new HtmlText($testable));

			$checkbox->setAttribute('rel', sprintf('test:%s', $testable));

			$li->appendChild($label);
		}

		/**
		 * @param $directory
		 * @return void
		 */
		protected function findTestables($directory)
		{
			is_dir($directory) and is_readable($directory) or static::fail('Not a readable directory: %s', $directory);

			$before = get_declared_classes();
			$aggregator = new FileAggregator($directory,
					function(SplFileInfo $file) {
						return strlen($file) > 4 && substr($file, -4) === '.php';
					});

			$aggregator->each(function($file){ include_once $file ;});
			$after = get_declared_classes();
			$newClasses = array_diff($after, $before);
			$results = array();
			foreach($newClasses as $item)
			{
				$reflector = new ReflectionClass($item);
				if ($reflector->implementsInterface('\\red\\ITestable'))
				{
					array_push($results, str_replace(NAMESPACE_SEPARATOR, '.', $item));
				}
			}
			return $results;
		}

		/**
		 * @param array $testables
		 * 
		 * @return string
		 */
		protected function runTool(array $testables)
		{
			$arguments = '-t ' . implode(' -t ', $testables);
			$cmd = sprintf('php -q %s %s', $this->getPathToTool(), $arguments);
			ob_start();
			$exitCode = -1;
			system($cmd, $exitCode);
			return array($exitCode, ob_get_clean());
		}

		/**
		 * Handle the "Clicked" event for the button "btnRunSelectedTests"
		 * 
		 * @param \red\web\ui\controls\Button $sender
		 * @param \red\web\ui\controls\ButtonClickedEventArgument $argument
		 * @return void
		 */
		private function onBtnRunSelectedTests_Clicked(Button $sender, ButtonClickedEventArgument $argument)
		{
			 $this->bindResults($this->runTool($this->getSelectedTestables()));
		}

		protected function getSelectedTestables()
		{
			$result = array();

			$checkboxes = $this->findAll(
				function($o)
				{
					return $o instanceof \red\web\ui\controls\Checkbox
						&& substr($o->getAttribute('rel'), 0, 5) == 'test:';
				});

			foreach($checkboxes as $checkbox)
			{
				if ($checkbox->isChecked())
				{
					array_push($result, substr($checkbox->getAttribute('rel'), 5));
				}
			}

			return $result;
		}

		protected function bindResults(array $runResult)
		{
			list($exitCode, $output) = $runResult;

			if ($exitCode == 0)
			{
				$this->hdrStatusText->setText('All tests passed.');
			}
			else if ($exitCode == 1)
			{
				$this->hdrStatusText->setText(sprintf('%d failed test.', $exitCode));
			}
			else
			{
				$this->hdrStatusText->setText(sprintf('%d failed tests.', $exitCode));
			}

			$doc = new \DOMDocument('1.0', 'UTF-8');
			$doc->loadXml($output);

			foreach($doc->getElementsByTagName('test-group') as $testGroup)
			{
				$this->resultContainer->appendChild(new \red\web\ui\html\HtmlLineBreak());
				foreach($testGroup->getElementsByTagName('test') as $testResult)
				{
					$this->resultContainer->appendChild($this->testResultToUIElement($testResult));
				}
			}
		}

		protected function testResultToUIElement(\DOMElement $testResult)
		{
			assert($testResult->tagName == 'test');

			$success = $testResult->getAttribute('result') == 'PASSED';
			$name = $testResult->getAttribute('name');

			$dl = new HtmlTag('dl');
			$dt = $dl->appendChild(new HtmlTag('dt'));
			$dd = $dl->appendChild(new HtmlTag('dd'));

			if (! $success)
			{
				$this->getPage()->registerClientScript(\red\web\ui\ScriptManager::CDN_URL_JQUERY);
				$dt->setAttribute('onclick', "\$(this).next('dd').slideToggle();");

				$ol = $dd->appendChild(new HtmlTag('ol'));
				$ol->addCssClass('failed-assertions');
				foreach($testResult->getElementsByTagName('test-failure') as $failedAssertion)
				{
					$file = $failedAssertion->getElementsByTagName('file')->item(0)->nodeValue;
					$lineIx = (integer)$failedAssertion->getElementsByTagName('line')->item(0)->nodeValue;

					$lines = file($file);
					$line = $lines[$lineIx-1];
					
					$li = $ol->appendChild(new HtmlTag('li'));
					$li->addCssClass('offending-line');

					$lineNo = $li->appendChild(new HtmlTag('span'));
					$lineNo->appendChild(new HtmlText(trim($lineIx)));

					$code = $li->appendChild(new HtmlTag('code'));
					$code->appendChild(new HtmlText(trim($line)));
				}
			}

			$item = new HtmlTag('li');
			$dt->appendChild(new HtmlText($name));

			$item->appendChild($dl);
			$item->addCssClass($success ? 'passed-test' : 'failed-test');

			return $item;
		}

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
		static public function getThemeResourceTypes()
		{
			return array('css');
		}
	}
}

#EOF