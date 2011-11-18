<?php

use \demo\DemoApplication;

/**
 * Initialize the framework 
 */
require_once 'bootstrap.php';

/**
 * Register our class loading mechanism 
 */
function demo_app_class_loader($fullyQualifiedClassName)
{
	static $baseDir = null;
	$baseDir = $baseDir !== null
			? $baseDir
			: realpath('./../lib');

	$result = false;

	$parts = explode(NAMESPACE_SEPARATOR, $fullyQualifiedClassName);
	$relativePath = strtolower(implode(DIRECTORY_SEPARATOR, $parts)) . '.php';

	$absolutePath = $baseDir .DIRECTORY_SEPARATOR. $relativePath;
	if (file_exists($absolutePath))
	{
		require_once $absolutePath;
		$result = true;
	}
	else
	{
//		throw new ErrorException(sprintf("<code>%s</code> not found in <code>%s</code><br/>", $fullyQualifiedClassName, $absolutePath));
	}

	return $result;
}
spl_autoload_register('demo_app_class_loader');

DemoApplication::start();