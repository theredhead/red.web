<?php

namespace red\xml
{
	use red\MBString;
	
	abstract class XMLNode extends \red\Obj
	{
		// <editor-fold defaultstate="collapsed" desc="Property XMLNode ParentNode">
		private $parentNode = null;

		/**
		 * @return XMLNode
		 */
		public function getParentNode()
		{
			return $this->parentNode;
		}

		protected function attachToParent(XMLNode $parent)
		{
			$this->parentNode = $parent;
		}
		// </editor-fold>
		// <editor-fold defaultstate="collapsed" desc="Property XMLDocument OwnerDocument">
		private $ownerDocument = null;

		/**
		 * @return XMLDocument
		 */
		public function getOwnerDocument()
		{
			if($this->ownerDocument === null)
			{
				$node = $this;
				while($node->getParentNode() instanceof XMLNode)
				{
					$node = $node->getParentNode();
				}
				if ($node instanceof XMLDocument)
				{
					$this->ownerDocument = $node;
				}
			}
			return $this->ownerDocument;
		}
		// </editor-fold>

		public function hasChildren()
		{
			return false;
		}
		
		/**
		 *
		 * @param Callback $callback 
		 * @return XMLNode
		 */
		final public function findFirst($callback, $deep=true)
		{
			if (is_array($callback)) static::fail('Array given as callback => %s', implode(', ', $callback));
			is_callable($callback) or static::fail('Invalid callback provided');

			$result = null;
			if (true === $callback($this))
			{
				$result = $this;
			}
			else if($deep && $this->hasChildren())
			{
				foreach($this->getChildNodes() as $child)
				{
					if ($child->findFirst($callback, $deep))
					{
						return $child;
					}
				}
			}
			else if(!$deep && $this->hasChildren())
			{
				foreach($this->getChildNodes() as $child)
				{
					if ($callback($child))
					{
						return $child;
					}
				}
			}
			return $result;
		}
		
		/**
		 *
		 * @param Callback $callback 
		 * @return XMLNodeList
		 */
		final public function findAll($callback, $deep=true)
		{
			$result = new XMLNodeList();
			$this->internalFindAll($callback, $deep, $result);
			return $result;
		}

		/**
		 * @access private
		 * 
		 * Worker method that collects all nodes causing a callback to 
		 * return true into an XMLNodeList 
		 * 
		 * @param Callable $callback
		 * @param XMLNodeList $result 
		 */
		final protected function internalFindAll(Callable $callback, $deep, XMLNodeList &$result)
		{
			if ($callback($this))
			{
				$result->append($this);
			}
			if ($deep && $this->hasChildren())
			{
				foreach($this->getChildNodes() as $child)
				{
					$child->internalFindAll($callback, $deep, $result);
				}
			}
			else if (!$deep && $this->hasChildren())
			{
				foreach($this->getChildNodes() as $child)
				{
					$child->internalFindAll($callback, false, $result);
				}
			}

		}
		
		/**
		 * Give descendants the ability to execute a piece of code for every node in the tree.
		 *
		 * @param type $callback 
		 */
		protected function foreachInTree($callback, $includeAttributes=false)
		{
			$callback($this);

			if ($includeAttributes && $this->hasAttributes())
			{
				foreach($this->getAttributes() as $attribute)
				{
					$callback($attribute);
				}
			}

			if ($this->hasChildren())
			{
				foreach($this->getChildNodes() as $child)
				{
					$child->foreachInTree($callback);
				}
			}
		}

		/**
		 * create a clone.
		 */
		abstract public function copy();
		
		/**
		 * Gives the node a chance to finalize itself before it is written.
		 */
		public function normalize()
		{
			
		}
	}
}

#EOF