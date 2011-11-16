<?php

use red\MBString;

/**
 * @param mixed $var 
 * @return string
 */
function typeid($var)
{
	$result = gettype($var);
	switch($result)
	{
		case 'object' : 
			$result = str_replace(NAMESPACE_SEPARATOR, '.', get_class($var));
		break;
	}
	return $result;
}

/**
 * 
 * @return \red\TypeInfo
 */
function typeof($var)
{
	return \red\TypeInfo::forTypeId(typeid($var));
}


/**
 * Get a UTF8 encoded string from a binary string
 * 
 * @param string
 * @return MBString 
 */
function mbstring($aString)
{
	return MBString::withString($aString, MBString::ENCODING_UTF8);
}

/**
 * Get the currently preffered languages in order.
 *
 * @param $newLanguages array|va_arg[string]
 */
function language($newLanguages=null)
{
	static $language = array();
	
	is_array($newLanguages) or $newLanguages = array_filter(func_get_args(), function($val){return $val !== null;});
	$result = $language;
	if(count($newLanguages) > 0)
	{
		$language = $newLanguages;
	}
	return $result;
}

/**
 * Get the distance between two values
 *
 * @param integer $a
 * @param integer $b 
 * @return integer
 */
function distance($a, $b)
{
	return abs($a - $b);
}