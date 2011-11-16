<?php

namespace red
{
	use red\Object;
	use red\MBString;

	class MBStringBuilder extends Object
	{
		protected $parts = array();
		
		public function clear()
		{
			$this->parts = array();
		}
		
		/**
		 * @param MBString $string 
		 */
		public function append($string)
		{
			array_push($this->parts, $string);
		}
		
		/**
		 * @return MBString 
		 */
		public function getString()
		{
			return new MBString(implode('', $this->parts));
		}
	}
}