<?php

define('REDWEB_BOOTSTRAP_FILE', __FILE__);

/**
 * Guarantee a de41bug const is defined everywhere. 
 */
defined('DEBUG') or define('DEBUG', getenv('DEBUG')==1);

/**
 * Report all errors, in strict mode. 
 */
error_reporting(E_ALL | E_STRICT);

/**
 * display errors while debugging.
 */
ini_set('display_errors', defined('DEBUG'));

require_once 'validateruntime.php';
require_once 'functions.php';
require_once 'autoloader.php';
require_once 'errorhandling.php';

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
function bootstrap_exception_handler(\Throwable $ex)
{
	$title = typeid($ex);
	$type = get_class($ex);
	
	// I know this is not entirely accurate, but it helps.
	$aOrAn = in_array(strtolower(substr($type, 0, 1)), 
			explode('|',' a|e|u|i|y|o|')) ? 'An' : 'A';

	$html = <<<HTML
<html>
	<head>
		<title>{$title}</title>
		<link rel="stylesheet" href="/css/main.css" type="text/css" />
		<link rel="stylesheet" href="/css/table.css" type="text/css" />
		<style type="text/css">
			h1, h2, h3, h4, h5, h6 {
				color: darkred;
				text-shadow: none;
			}
			.trace {
				width: 100%;
				}
			
			dl, dt, dd {
				position: relative;
				text-align: left;
				width: auto;
				top: 0;
				left: 0;
				display: block;
				float: none;
				clear: both;
				margin: 0;
				padding: 0;
				color: black;
			}
			dl {
				padding-left: 1em;
			}
			dd {
				padding-left: 1em;
			}
		
			.file {
				list-style-type: none;
				margin: 0 0 0 2em; 
				padding: 0;
			}
			.file li {
				display: block;
				white-space: pre;
				font-family: mono-space;
				list-style-type: none;
				margin: 0;
				padding: 0;
				line-height : 1.2em;
				background: white;
				color: black;
				vertical-align: top;
				position: relative;
			}
			.file .here {
				border: solid 1px red;
				background: lemonchiffon;
			}
			.file .lineNo {
				font-family: mono-space;
				text-align: right;
				position: absolute;
				color: gray;
				top: 0;
				left: -40px;
				clear: none;
				font-size: .8em;
				font-style: italic;
			}
			.file .variable {
				font-style: italic;
			}
			.file .comment {
				font-weight: normal;
				color: gray;
			}
		</style>
	</head>
	<body>
		<h1>{$title}</h1>
		<p>
			{$aOrAn} `{$type}` was thrown in file '<code>{$ex->getFile()}</code>' on line {$ex->getLine()}
		</p>
		<h2>The message was:</h2	>
		<p>
			{$ex->getMessage()}
		</p>
		<h3>Here's the stack trace:</h3>
		<!-- trace -->
		
		<h3>Here's where it happened:</h3>
		<!-- file -->
	</body>
</html>
HTML;
	
	// <editor-fold defaultstate="collapsed" desc="dumping trace into a table">
	ob_start();
    printf("<table class=\"Table trace\">\n");
	// printf("<caption>Stacktrace:</caption>\n");
	$trace = $ex->getTrace();
	$frame = reset($trace);
	printf("<thead>\n");

	$fields = array("file","line","function/method","arguments");
	foreach($fields as $label)
	{
		printf('<th>%s</th>', $label);
	}
	printf("</thead>\n");
	printf("<tbody>\n");
    foreach($trace as $frame)
    {
		echo '<tr>';
		
		foreach($fields as $ix => $field)
		{
			$value  = isset($frame[$field])
					? $frame[$field]
					: '';

			switch($ix)
			{
				case 0 :
					if ($value != '')
					{
						$title = $value;
	//					$caption = substr($value, (strpos($value, '/lib/')));
						$caption = basename($value);
						$value = sprintf('<span title="%s">%s</span>', $title, $caption);
					}
					else
					{
						$value = '{closure}';
					}
					break;
				case 2 :
					if (isset($frame['class']) && isset($frame['type']) && isset($frame['function']))
					{
						$value = $frame['class'] . $frame['type'] . $frame['function'];
					}
					break;
				case 3 :
                    $value = (isset($frame['args']))
                            ? bootstrap_exception_handler_var_dump($frame['args'])
                            : '';
					break;
				break;
			}
			printf('<td class="%s"><code>%s</code></td>', $field, $value);
		}

		echo '</tr>';
    }
	printf("<tbody>\n");
    printf("</table>\n");
	
	$traceTable = ob_get_clean();
	// </editor-fold>
	// <editor-fold defaultstate="collapsed" desc="getting some lines from the file">
	
	$content = file($ex->getFile());
	
	$fileDump = '<ol class="file">';
	
	foreach ($content as $lineNo => $line)
	{
		if (distance($ex->getLine(), $lineNo-1) < 10)
		{
			$line = htmlentities(str_replace("\t", "  ", $line));
			
			$line = preg_replace('~(\$[^\b]+?)\b~', '<span class="variable">\1</span>', $line);
			$line = preg_replace('~((\/\/|\/\*|\s\*).+)?$~', '<span class="comment">\1</span>', $line);
			
			if ($lineNo == $ex->getLine()-1)
			{
				$fileDump .= sprintf('<li class="here"><span class="lineNo">%d</span>%s</li>',$lineNo, ($line));
			}
			else
			{
				$fileDump .= sprintf('<li><span class="lineNo">%d</span>%s</li>',$lineNo, ($line));
			}
		}
	}
	$fileDump .= '</ol>';
	// </editor-fold>
	
	$replacers = array(
		'<!-- trace -->' => $traceTable,
		'<!-- file -->' => $fileDump
	);
	
	echo str_replace(array_keys($replacers), $replacers, $html);
    die(1);
}

function bootstrap_exception_handler_var_dump($var, $nesting=0)
{
	static $maxNestingLevel = 10;
	static $entries = 0;
	
	$result  = '';

	$clickable = function($name, $val) {
		$id = uniqid();

		$result  = sprintf('<dt style="cursor: pointer;" onclick="document.getElementById(\'_dump_%s\').style.display=document.getElementById(\'_dump_%s\').style.display == \'none\' ? \'block\' : \'none\'; return false;">%s</dt>',$id, $id, $name);
		$result .= sprintf('<dd id="_dump_%s" style="white-space: nowrap; display: none;">%s</dd>', $id, $val);
		
		return $result;
	};
	
	if ((is_array($var)) && $nesting <= $maxNestingLevel)
	{
		if (count($var) > 0)
		{
			$result .= sprintf('Array (%d):<dl>', count($var));
			foreach($var as $key => $value)
			{
				$result .= $clickable($key, bootstrap_exception_handler_var_dump($value, 1+$nesting));
			}
			$result .= '</dl>';
		}
	}
	else if ((is_object($var)) && $nesting <= $maxNestingLevel)
	{
		$reflector = new ReflectionObject($var);
		
		$result .= sprintf('<strong>%s</strong><dl>', typeid($var));
		foreach($reflector->getProperties() as $property)
		{
			$property->setAccessible(true);
			$value = $property->getValue($var);
			
			$displayName = '('.typeid($value).') $'.$property->getName();
			
			
			// $result .= sprintf('<dt>%s<dt>', bootstrap_exception_handler_var_dump($key, 1+$nesting));
			$result .= $clickable($displayName, bootstrap_exception_handler_var_dump($value, 1+$nesting));
		}
		$result .= '</dl>';
	}
	else
	{
		$result .= is_scalar($var) ? sprintf("[%s '%s']", typeid($var), $var) : typeid($var);
	}
	
	return $result;
}
if (php_sapi_name() != 'cli')
{
	set_exception_handler('bootstrap_exception_handler');
}


date_default_timezone_set('Europe/Amsterdam');

#EOF