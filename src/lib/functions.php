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

/**
 * Add a message to the trace log
 * 
 * @param type $va_arg 
 */
function trace($message)
{
	if(isset($_REQUEST['trace']))
	{
		printf("%s\n", func_num_args() == 1 ? $message : call_user_func_array('sprintf', func_get_args()));
	}
}

/**
 * Get the proper ordinal suffix for a number.
 *
 * @todo: make ordinal suffixes a localizable resource so it'll switch with language()
 * 
 * @param integer $number
 * @param array[0..9] of integer $suffixes
 * @return string
 */
function ordinalize($number, $suffixes=array('th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th'))
{
	assert(count($suffixes) === 10);
	$string = ''.$number;
	$lastChar = substr($string, -1);
	return $number . $suffixes[(integer)$lastChar];
}

/**
 * Expand a number into a format string based on its value, giving
 * different format strings for 0, 1 and greater than one.
 * 
 * example:
 *	cardinalize(count($apples), 'No apples', 'one apple', '%d apples.');
 *
 * @param integer $aNumber
 * @param string $ifZero
 * @param string $ifOne
 * @param string $ifGreaterThanOne
 * @return string 
 */
function cardinalize($aNumber, $ifZero, $ifOne, $ifGreaterThanOne)
{
	assert('is_integer($aNumber) && $aNumber > -1;');
	assert('is_string($ifZero);');
	assert('is_string($ifOne);');
	assert('is_string($ifGreaterThanOne);');

	$result = null;

	if ($aNumber == 0)
	{
		$result = strpos($ifZero, '%d') > -1 
				? sprintf($ifZero, $aNumber) 
				: $ifZero;
	}
	else if ($aNumber == 1)
	{
		$result = strpos($ifOne, '%d') > -1 
				? sprintf($ifOne, $aNumber) 
				: $ifOne;
	}
	else
	{
		$result = strpos($$ifGreaterThanOne, '%d') > -1 
				? sprintf($ifGreaterThanOne, $aNumber) 
				: $ifGreaterThanOne;
	}
	return $result;
}
	
#EOF