<?php

namespace red
{
	class TypeInfo
	{
		protected $typeId;
		protected $reflector = null;

		public function getId()
		{
			return $this->typeId;
		}
		
		public function isClass()
		{
			return class_exists($this->getId(), true);
		}
		public function isInterface()
		{
			return interface_exists($this->getId(), true);
		}
			
		protected function __construct($typeId)
		{
			$this->typeId = $typeId;
		}
		
		private static $instances = array();
		static public function forTypeId($typeId)
		{
			if (!isset(self::$instances[$typeId]))
			{
				self::$instances[$typeId] = new self($typeId);
			}
			return self::$instances[$typeId];
		}
	}
}

#EOF