<?php

namespace red\web\ui\html
{
	use red\MBString;
	
	class XHTMLElement extends \red\xml\XMLElement
	{
		protected $cssClasses = array();
		public function getCssClasses()
		{
			return $this->cssClasses;
		}
		
		/**
		 * @return string A string that can be used to uniquely identify this instance.
		 */
		public function getUniqueId()
		{
			$path = array();
			$node = $this;
			do
			{
				$name = is_callable($node, 'getName')
						? $node->getName() // custom property
						: $node->getLocalName() . $node->getInstanceId(); // namespace-less name of tag

				array_unshift($path, $name);
			}
			while($node = $node->getParentNode());
			return '/'.implode('/', $path);
		}
		
		public function getClientId()
		{
			return str_replace('/', '_', $this->getUniqueId());
		}
		
		/**
		 * Add a css class to this element
		 * 
		 * @param MBString $className 
		 */
		public function addCssClass($className)
		{
			$className instanceof MBString 
				or $className = MBString::withString($className);

			$this->cssClasses[$className->toString()] = $className;
		}
		
		/**
		 * Check if this element has a css class
		 * 
		 * @param \MBString $className
		 * @return booleab
		 */
		public function hasCssClass($className)
		{
			assert($className instanceof MBString or $className = MBString::withString($className));
			return isset($this->cssClasses[$className->toString()]);
		}
		
		/**
		 * Remove a css class from this element
		 *
		 * @param MBString $className 
		 */
		public function removeCssClass(MBString $className)
		{
			if ($this->hasCssClass($className))
			{
				unset($this->cssClasses[$className->toString()]);
			}
		}
		
		/**
		 * Find all elements from this point in the tree that have a css class name $className set
		 * 
		 * @return XMLNodeList
		 */
		public function getElementsByClassName($className)
		{
			return $this->findAll(function(\red\xml\XMLNode $node) use ($className) {
				return $node instanceof \red\xml\XMLElement && $node->hasCssClass($className);
			});
		}

		public function getAttribute($name)
		{
			switch(strtolower($name))
			{
				case 'class' :
					return implode(' ', $this->getCssClasses());
					break;
				default :
					return parent::getAttribute($name);
					break;
			}
		}
		
		public function setAttribute($name, $value)
		{
			switch(strtolower($name))
			{
				case 'remove-class' :
					$this->removeCssClass($value);
					break;

				case 'class' : 
				case 'add-class' : 
					$this->addCssClass($value);
					break;

				default:
					parent::setAttribute($name, $value);
					break;
			}
		}

		public function normalize()
		{
			if(count($this->getCssClasses()) > 0)
			{
				parent::setAttribute('class', implode(' ', $this->getCssClasses()));
			}
		}
	}
}

#EOF