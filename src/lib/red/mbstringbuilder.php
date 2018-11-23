<?php

namespace red
{
	use red\Obj;
	use red\MBString;

	class MBStringBuilder extends Obj
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