<?php

// make sure the NAMESPACE_SEPARATOR is defined.
defined('NAMESPACE_SEPARATOR') or define('NAMESPACE_SEPARATOR', '\\');

/**
 * Register our class loading mechanism
 */
function red_web_framework_class_loader($fullyQualifiedClassName)
{
	$result = false;

	$parts = explode(NAMESPACE_SEPARATOR, $fullyQualifiedClassName);
	$relativePath = strtolower(implode(DIRECTORY_SEPARATOR, $parts)) . '.php';

	foreach(explode(PATH_SEPARATOR, ini_get('include_path')) as $baseDir)
	{
		$fullPath = realpath($baseDir) . DIRECTORY_SEPARATOR . $relativePath;
		
		if (file_exists($fullPath))
		{
			require_once $fullPath;
			$result = true;
		}
		else
		{
//			printf("Framework loader:<code>%s</code> not found in <code>%s</code><br/>", $fullyQualifiedClassName, $fullPath);
		}
	}

	return $result;
}

spl_autoload_register(
	  'red_web_framework_class_loader'
	, true /* prepend to anything else that may be registered. */);