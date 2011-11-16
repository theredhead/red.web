<?php

/**
 * Guarantee a de41bug const is defined everywhere. 
 */
defined('DEBUG') or define('DEBUG', getenv('DEBUG')==1);

/**
 * Rep[ort all errors, in strict mode. 
 */
error_reporting(E_ALL | E_STRICT);

/**
 * display errors while debugging.
 */
ini_set('display_errors', true);

/**
 * Make errors, notices, warnings etc. into exceptions.
 */
function bootstrap_error_handler($errno, $errstr, $errfile, $errline)
{
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
};
set_error_handler('bootstrap_error_handler');

/**
 * Temporary autonomous error handler
 * Required because the  "pretty" error handler requires parts of
 * the framework, but there is still stuff that could go wrong while
 * bootstrapping. Like running an incompatible version of PHP
 */
function bootstrap_exception_handler(\Exception $ex)
{
    printf("<h1>Exception: %s</h1>\n", get_class($ex));
    printf("<h2>%s</h2>\n", $ex->getMessage());
    printf("In file '<code>%s</code>' on line %d.\n", $ex->getFile(), $ex->getLine());
    printf("<table width=\"100%%\" border=\"1\">\n");
	
	$trace = $ex->getTrace();
	$frame = reset($trace);
	printf("<thead>\n");

	$fields = array("file","line","class","type","function","args");
	foreach($fields as $label)
	{
		printf('<th>%s</th>', $label);
	}
	printf("</thead>\n");
	printf("<tbody>\n");
    foreach($trace as $frame)
    {
		echo '<tr>';

		foreach($fields as $field)
		{
			$value  = isset($frame[$field])
					? bootstrap_exception_handler_var_dump($frame[$field])
					: '<br />';

        	printf('<td class="%s"><code>%s</code></td>', $field, $value);
		}

		echo '</tr>';
    }
	printf("<tbody>\n");
    printf("</table>\n");
    exit;
}

function bootstrap_exception_handler_var_dump($var, $nesting=0)
{
	static $maxNestingLevel = 10;
	static $entries = 0;
	
	$result  = '';

	$clickable = function($name, $val) {
		$id = uniqid();

		$result  = sprintf('<dt style="cursor: pointer; outline: solid 1px gray;" onclick="document.getElementById(\'_dump_%s\').style.display=document.getElementById(\'_dump_%s\').style.display == \'none\' ? \'block\' : \'none\'; return false;">%s</dt>',$id, $id, $name);
		$result .= sprintf('<dd id="_dump_%s" style="white-space: nowrap; display: none;">%s</dd>', $id, $val);
		
		return $result;
	};
	
	if ((is_array($var)) && $nesting <= $maxNestingLevel)
	{
		$result .= '<dl>';
		foreach($var as $key => $value)
		{
			$result .= $clickable($key, bootstrap_exception_handler_var_dump($value, 1+$nesting));
		}
		$result .= '</dl>';
	}
	else if ((is_object($var)) && $nesting <= $maxNestingLevel)
	{
		$result .= sprintf('<strong>%s</strong><dl>', typeid($var));
		$reflector = new ReflectionObject($var);
		foreach($reflector->getProperties() as $property)
		{
			$property->setAccessible(true);
			// $result .= sprintf('<dt>%s<dt>', bootstrap_exception_handler_var_dump($key, 1+$nesting));
			$result .= $clickable($property->getName(), bootstrap_exception_handler_var_dump($property->getValue($var), 1+$nesting));
		}
		$result .= '</dl>';
	}
	else
	{
		$result .= is_scalar($var) ? sprintf("[%s '%s']", typeid($var), $var) : typeid($var);
	}
	
	return $result;
}

set_exception_handler('bootstrap_exception_handler');



date_default_timezone_set('Europe/Amsterdam');

require_once 'validateruntime.php';
require_once 'functions.php';
require_once 'autoloader.php';
require_once 'errorhandling.php';

#EOF