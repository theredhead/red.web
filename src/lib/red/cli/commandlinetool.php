<?php

namespace red\cli
{
	use \red\Object;

	abstract class CommandLineTool extends Object
	{
		/**
		 * Keeps the result of parsed commandline arguments
		 * 
		 * @var array
		 */
		protected $options = array();

		/**
		 * @param string $name
		 * @param string $value
		 * @return void
		 */
		protected function setOption($name, $value)
		{
			$this->options[$name] = $value;
		}

		/**
		 * @abstract
		 * @param array $arguments ($argv will be passed in)
		 * @return void
		 */
		abstract protected function main(array $arguments);

		/**
		 * @static
		 * @param CommandLineTool $tool
		 * @return void
		 */
		final static public function run(CommandLineTool $tool)
		{
			global $argc;
			global $argv;

			for ($i = 1; $i < $argc; $i ++)
			{
				$argumentName = $argv[$i];

				$numHyphens = 0;
				while(strlen($argumentName) > 1 && substr($argumentName, 0, 1) == '-')
				{
					$numHyphens ++;
					$argumentName = substr($argumentName, 1);
				}

				if ($numHyphens > 0 && isset($argv[$i+1]))
				{
					$argumentValue = $argv[++$i];

					$tool->setOption($argumentName, $argumentValue);
				}
				else
				{
					$tool->setOption($argumentName, $argumentName);
				}
			}

			try
			{
				$exitCode = $tool->main($argv);
				if ($exitCode === null)
				{
					$exitCode = 0;
				}
				if (! is_integer($exitCode))
				{
					static::fail('main did not return void or an integer.');
				}
				exit($exitCode);
			}
			catch(\Exception $ex)
			{
				printf("%s\n", get_class($ex));
				printf("%s\n\n", str_repeat("=", strlen(get_class($ex))));
				printf("%s\n\n", $ex->getMessage());
				printf("%s\n\n", $ex->getTraceAsString());
				exit(1);
			}
		}
	}
}

#EOF