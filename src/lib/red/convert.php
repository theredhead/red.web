<?php

namespace red
{
	class Convert
	{
		/**
		 * Convert a value to a boolean.
		 *
		 * @param mixed $value
		 * @return boolean 
		 */
		static public function toBoolean($value)
		{
			$result = false;

			if (is_scalar($value))
			{
				$result = in_array(''.$value, array('1', 'true', 'yes', 'on'));
			}
			else if (is_object($value) && is_callable(array($value, 'booleanValue')))
			{
				$result = $value->booleanValue();
			}

			return $result;
		}
	}
}