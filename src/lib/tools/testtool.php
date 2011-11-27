<?php

namespace tools
{
	use red\cli\CommandLineTool;

	class TestTool extends CommandLineTool
	{
		protected $input;
		protected $output;
		protected $alias;

		protected function assert($what, $messageIfNot)
		{
			if (! $what)
			{
				throw new \ErrorException($messageIfNot);
			}
		}

		protected function validate()
		{
			$this->assert(ini_get('phar.readonly') == false,
					'phar.readonly is enabled in php.ini');
			$this->assert(is_dir($this->input),
					'Input directory is not set to a directory. use --input (or -i)');
			$this->assert(is_writable(dirname($this->output)),
					'Output is not set to a writable location');
			$this->assert(is_string($this->alias) or $this->alias = basename($this->input),
					'Unable to set alias');
		}

		protected function setOption($name, $value)
		{
			switch(strtolower($name))
			{
				case 'input' :
				case 'i' :
					$this->input = $value;
					break;

				case 'output' :
				case 'o' :
					$this->output = $value;
					break;

				case 'alias' :
				case 'a' :
					$this->alias = $value;
					break;

				default:
					parent::setOption($name, $value);
					break;
			}
		}

		/**
		 * @param array $arguments
		 * @return void
		 */
		protected function main(array $arguments)
		{
			$this->validate();
			$outputFileName = $this->output . DIRECTORY_SEPARATOR . $this->alias . '.phar';

			$phar = new \Phar($outputFileName, null, $this->alias);
			$phar->buildFromDirectory($this->input);

			printf("\nCompiled %d files into %s\n", $phar->count(), $outputFileName);
		}
	}

	// this line should not be in here because class files inside lib should
	// never have side effects when included.
	// CommandLineTool::run(new TestTool());
}