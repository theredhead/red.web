<?php

namespace red\xml
{
	use red\MBString;

	class XMLElement extends XMLNode
	{
		static private $elementInstanceCounter = 0;
		private $elementInstanceId;
		
		// <editor-fold defaultstate="collapsed" desc="Property boolean IsVisible">
		private $isVisible = true;

		/**
		 * @return boolean
		 */
		public function isVisible()
		{
			return $this->isVisible == true;
		}

		/**
		 * @param boolean $newIsVisible
		 */
		public function setIsVisible($newIsVisible)
		{
			$this->isVisible = $newIsVisible == true;
		}

		// </editor-fold>
		
		/**
		 * @return integer
		 */
		public function getInstanceId()
		{
			return $this->elementInstanceId;
		}
		/**
		 * Get a string representing the path to this node
		 * 
		 * @return string
		 */
		public function getPath()
		{
			$path = array();
			$node = $this;
			do
			{
				array_unshift($path, $node->getInstanceId());
			}
			while($node = $node->getParentNode());
			return '/'.implode('/', $path);
		}

		/**
		 * The encoding of this element
		 *
		 * @var string
		 */
		protected $encoding = MBString::ENCODING_UTF8;
		
		/**
		 * @var MBString
		 */
		protected $tagName;

		/**
		 * @var MBString
		 */
		protected $localName;

		/**
		 * @var MBString
		 */
		protected $namespaceUri;

		/**
		 * @var XMLNodeList
		 */
		protected $childNodes;
		
		/**
		 * get this elements children
		 *
		 * @return XMLNodeList
		 */
		public function getChildNodes()
		{
			if ($this->childNodes === null)
			{
				$this->clear();
			}
			return $this->childNodes;
		}
		
		/**
		 * Determine if this element has children.
		 * 
		 * @return boolean
		 */
		public function hasChildren()
		{
			return $this->childNodes !== null && $this->childNodes->count() > 0;
		}

		/**
		 * get the number of children inside this element
		 *
		 * @return integer
		 */
		public function countChildren()
		{
			return $this->childNodes === null ? 0 : $this->childNodes->count();
		}

		/**
		 * Remove all children
		 */
		public function clear()
		{
			$this->childNodes = new XMLNodeList();
		}

		/**
		 * get the first childnode
		 * 
		 * @return XMLNode
		 */
		public function getFirstChild()
		{
			return $this->getChildNodes()->first();
		}		

		/**
		 * get the last childnode
		 * 
		 * @return XMLNode
		 */
		public function getLastChild()
		{
			return $this->getChildNodes()->last();
		}		

		/**
		 * get the tagname
		 *
		 * @return MBString
		 */
		public function getTagName()
		{
			return $this->tagName;
		}
		
		/**
		 * set this elements tagname
		 *
		 * @param MBString $value
		 * @return XMLElement this element
		 */
		public function setTagName($value)
		{
			$value instanceof MBString or $value = MBString::withString($value);
			$this->tagName = $value;
			$this->localName = null;
			return $this;
		}
		
		/**
		 * get the localname of this element (tagname without any namespace prefix)
		 * @return type 
		 */
		public function getLocalName()
		{
			if ($this->localName === null)
			{
				$index = $this->tagName->indexOf(':');
				if ($index > 0)
				{
					$index ++;
					$length = $this->tagName->length() - $index;
					$this->localName = $this->tagName->subString($index, $length);
				}
				else
				{
					$this->localName = $this->getTagName();
				}
			}
			return $this->localName;
		}
		
		/**
		 * @param MBString $tagName
		 */
		public function __construct($tagName=null)
		{
			parent::__construct();
			$this->elementInstanceId = self::$elementInstanceCounter++;

			if ($tagName !== null)
			{
				$tagName instanceof MBString or $tagName = MBString::withString($tagName);
				$this->setTagName($tagName);
			}
			return $this;
		}

		/**
		 * internal php method 
		 */
		public function __clone()
		{
			$this->elementInstanceId = self::$elementInstanceCounter++;
		}

		/**
		 * this elements attributes
		 * 
		 * @var array 
		 */
		protected $attributes = array();

		/**
		 * Get this elements attributes
		 * 
		 * @return type 
		 */
		public function getAttributes()
		{
			return $this->attributes;
		}
		
		/**
		 * remove an attribute by name
		 * 
		 * @param string $name 
		 */
		public function unsetAttribute($name)
		{
			$name instanceof MBString or $name = MBString::withString($name);

			if (isset($this->attributes[$name->toString()]))
			{
				unset($this->attributes[$name->toString()]);
			}
		}

		/**
		 * set an attribute
		 * 
		 * @param MBString $name
		 * @param MBString $value 
		 */
		public function setAttribute($name, $value)
		{
			$name instanceof MBString or $name = MBString::withString($name);
			$value instanceof MBString or $value = MBString::withString($value);
			
			if ($this->hasAttribute($name))
			{
				$this->unsetAttribute($name);
			}

			$this->attributes[$name->toString()] = new XMLAttribute($name, $value);
		}

		/**
		 * see if this element has an attribute
		 * 
		 * @param MBString $name
		 * @return boolean
		 */
		public function hasAttribute($name)
		{
			$name instanceof MBString or $name = MBString::withString($name);

			return isset($this->attributes[$name->toString()]);
		}

		/**
		 * Get an attributes value by name
		 *
		 * @param type $name
		 * @return type 
		 */
		public function getAttribute($name)
		{
			$name instanceof MBString or $name = MBString::withString($name);

			return $this->hasAttribute($name) 
					? $this->attributes[$name->toString()]->getValue()
					: null;
		}
		
		/**
		 * Add a node to the end of this elements childNodes list
		 *
		 * @param XMLNode $node
		 * @return XMLNode The node added
		 */
		public function appendChild(XMLNode $node)
		{
			!$this->isSameInstance($node) or static::fail('Cannot append me to myself.');

			$this->getChildNodes()->append($node);
			$node->attachToParent($this);
			return $node;
		}

		/**
		 * Add a node to the beginning of this elements childNodes list
		 *
		 * @param XMLNode $node
		 * @return XMLNode The node added
		 */
		public function prependChild(XMLNode $node)
		{
			!$this->isSameInstance($node) or static::fail('Cannot append me to myself.');

			$this->getChildNodes()->prepend($node);
			$node->attachToParent($this);
			return $node;
		}
		
		/**
		 * @return XMLNodeList
		 */
		public function getElementsByTagName($tagName)
		{
			return $this->findAll(function(XMLNode $node) use ($tagName) {
				return $node instanceof XMLElement 
					&& $node->getTagName() == $tagName;
			});
		}

		/**
		 * There seems to be a bug in here. skipping at text nodes or something like that.
		 * 
		 * @return XMLElement
		 */
		public function getElementById($id)
		{
			$id instanceof MBString or $id = MBString::withString($id);

			$result = $this->findFirst(function(XMLNode $node) use ($id) {
				if($node instanceof XMLElement)
				{
					return $node->getAttribute('id') == $id;
				}
			});
			
			return $result;
		}
		
		/**
		 * copy this element
		 *
		 * @return XMLElement
		 */
		public function copy()
		{
			$copy = new static($this->getTagName());
			
			// ridiculous, but this seems to be a whole lot faster....
			foreach($this->attributes as $attribute)
			{
				$copy->setAttribute($attribute->getName(), $attribute->getValue());
			}
			// than this...
			// $copy->attributes = clone $this->attributes;
			
			// don't forget the kids!
			if ($this->hasChildren())
			{
				foreach($this->getChildNodes() as $child)
				{
					$copy->appendChild($child->copy());
				}
			}
			return $copy;
		}
	}
}

#EOF