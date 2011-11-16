<?php

namespace red\web\ui\controls
{
	class PropertyBag extends \red\Object implements \ArrayAccess
	{
		/**
		 * holds this property bags actual data
		 *
		 * @var array
		 */
		protected $data = array();

		public function __construct(array $data = array())
		{
			parent::__construct();
			foreach($data as $offset => $value)
			{
				$this->offsetSet((string)$offset, (string)$value);
			}
		}
		
		/**
		 * ArrayAccess::offsetExists
		 * 
		 * @param string $offset
		 * @return boolean
		 */
		public function offsetExists($offset)
		{
			return isset($this->data[(string)$offset]);
		}

		/**
		 * ArrayAccess::offsetGet
		 * 
		 * @param string $offset
		 * @return string
		 */
		public function offsetGet($offset)
		{
			return $this->offsetExists($offset) 
					? $this->data[(string)$offset] 
					: null;
		}

		/**
		 * ArrayAccess::offsetSet
		 * 
		 * @param string $offset
		 * @param string $value
		 * @return boolean
		 */
		public function offsetSet($offset, $value)
		{
			if (! is_string($offset) && is_scalar($value))
			{
				static::fail('%s both $offset and $value must be scalar types.', __METHOD__);
			}
			$this->data[(string)$offset] = (string)$value;
		}

		/**
		 * ArrayAccess::offsetUnset
		 * @param string $offset 
		 */
		public function offsetUnset($offset)
		{
			if ($this->offsetExists((string)$offset))
			{
				unset($this->data[(string)$offset]);
			}
		}
		
		/**
		 * Get the inner array of this property bag
		 * 
		 * @return array
		 */
		public function toArray()
		{
			return $this->data;
		}
	}
}