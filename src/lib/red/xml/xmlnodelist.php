<?php

namespace red\xml
{

	use red\MBString;

	/**
	 * XMLNodeList represents a list of XMLNode instances 
	 */
	class XMLNodeList extends \red\Obj implements \Countable, \Iterator, \ArrayAccess
	{
		/**
		 * Keeps the nodes in this list.
		 *
		 * @var array
		 */
		protected $nodes = array();

		/**
		 * Append a new node to the list.
		 *
		 * @param XMLNode $value
		 * @return integer new length of the list
		 */
		public function append(XMLNode $value)
		{
			return array_push($this->nodes, $value);
		}

		/**
		 * Prepend a new node to the list.
		 *
		 * @param XMLNode $value
		 * @return integer new length of the list
		 */
		public function prepend(XMLNode $value)
		{
			return array_unshift($this->nodes, $value);
		}
		
		
		/**
		 * Get the number of items in this list
		 *
		 * @return integer
		 */
		public function count()
		{
			return count($this->nodes);
		}
		
		/**
		 * Get the number of items in this list
		 *
		 * @return integer
		 */
		public function length()
		{
			return $this->count();
		}

		/**
		 * Get the curren item
		 *
		 * @return integer
		 */
		public function current()
		{
			return current($this->nodes);
		}

		/**
		 * Get the index of the current item
		 * 
		 * @return integer
		 */
		public function key()
		{
			return (int)key($this->nodes);
		}

		/**
		 * Move to the next item in this list
		 * 
		 * @return XMLNode
		 */
		public function next()
		{
			return next($this->nodes);
		}

		/**
		 * Determine if a given $offset exists in this list
		 * 
		 * @param integer $offset
		 * @return boolean 
		 */
		public function offsetExists($offset)
		{
			return isset($this->nodes[(int)$offset]);
		}

		/**
		 * Get an item in this list at a given $offest
		 *
		 * @param integer $offset
		 * @return XMLNode
		 */
		public function offsetGet($offset)
		{
			return $this->nodes[(int)$offset];
		}

		/**
		 * Set an XMLNode for an $ofset
		 *
		 * @param integer $offset
		 * @param XMLNode $value
		 * @return XMLNode the node just set 
		 */
		public function offsetSet($offset, $value)
		{
			$value instanceof XMLNode or static::fail(
					new \InvalidArgumentException('expected instanceof XMLNode for $value'));
			
			return $this->nodes[(int)$offset] = $value;
		}

		/**
		 * Remove an item from this list. Note that this reindexes the list.
		 *
		 * @param integer $offset 
		 */
		public function offsetUnset($offset)
		{
			unset($this->nodes[(int)$offset]);
		}

		/**
		 * Move back to the beginning of this list
		 * 
		 * @return XMLNode
		 */
		public function rewind()
		{
			return reset($this->nodes);
		}

		/**
		 * Check if we can loop further over this list
		 *
		 * @return boolean
		 */
		public function valid()
		{
			return $this->current() !== false;
		}

		
		/**
		 * get the first element without resetting the internal pointer
		 * 
		 * @return XMLNode 
		 */
		public function first()
		{
			return $this->nodes[0];
		}
		
		/**
		 * get the last element without resetting the internal pointer
		 * 
		 * @return XMLNode 
		 */
		public function last()
		{
			$this->nodes[count($this->nodes)-1];
		}
	}

}

#EOF