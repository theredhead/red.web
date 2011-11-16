<?php

namespace red\xml
{
	use red\MBString;

	class XMLAttribute extends XMLNode
	{
		/**
		 * @var MBString
		 */
		protected $name;

		/**
		 * @return MBString
		 */
		public function getName()
		{
			return $this->name;
		}

		/**
		 * @param MBString $value
		 */
		protected function setName(MBString $value)
		{
			$this->name = $value;
		}
		
		/**
		 * @var MBString
		 */
		protected $value;
		
		/**
		 * @return MBString
		 */
		public function getValue()
		{
			return $this->value;
		}
		
		/**
		 * @param MBString $value 
		 */
		public function setValue(MBString $value)
		{
			$this->value = $value;
		}
	
		public function __construct($name, $value)
		{
			parent::__construct();
			$name instanceof MBString or $name = MBString::withString($name);
			$value instanceof MBString or $value = MBString::withString($value);
			$this->setName($name);
			$this->setValue($value);
		}
		
		/**
		 * Get a copy of this instance
		 */
		public function copy()
		{
			return clone $this;
		}
	}
}

#EOF