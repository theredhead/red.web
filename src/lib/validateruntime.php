<?php

/**
 * Declare the required version of php because it is used several times. 
 * Oldest version we should be compatible with is 5.3.2
 */
defined('REQUIRED_PHP_VERSION') or define('REQUIRED_PHP_VERSION', '5.3.2');

/**
 * Make sure we're running a sufficiently up-to-date version of PHP 
 */
if (version_compare(PHP_VERSION, REQUIRED_PHP_VERSION) < 0)
{    
    throw new RuntimeException(sprintf(
              'red.web requires PHP version %s or newer. You are running %s'
            , REQUIRED_PHP_VERSION
            , PHP_VERSION));
}

#EOF