<?php

namespace red\web\ui\controls
{
	use \red\MBString;
	use \red\web\ui\html\HtmlTag;
	use \red\web\ui\html\HtmlText;
	use \red\web\ui\html\HtmlUnorderedList;
	use \red\web\ui\html\HtmlListItem;
	use \red\web\ui\controls\IBindable;
	use \red\web\ui\controls\IRepeaterDatasourceDelegate;
	
	use \red\xml\XMLText;
	use \red\xml\XMLElement;
	use \red\xml\XMLLiteral;
	
	class DataBinder extends \red\Object
	{
		// <editor-fold defaultstate="collapsed" desc="Property mixed DataItem">
		private $dataItem = null;

		/**
		 * @return mixed
		 */
		public function getDataItem()
		{
			return $this->dataItem;
		}

		/**
		 * @param mixed $newDataItem
		 */
		public function setDataItem($newDataItem)
		{
			$this->dataItem = $newDataItem;
		}

		// </editor-fold>
		public function __construct($dataItem)
		{
			parent::__construct();
			$this->setDataItem($dataItem);
		}

		/**
		 * For internal use. (could be overridden in subclass)
		 * 
		 * replace placeholders in a string with details from this instances' DataItem
		 * 
		 * @todo allow property notation using dot syntax: ${item.propertyName} => getPropertyName()
		 *
		 * @param MBString $string
		 * @return MBString
		 */
		protected function expandString($string)
		{
			static $counter = 0;
			
			$string instanceof MBString or $string = MBString::withString($string);
			
			$result = $string->replace('@item', ''.$this->getDataItem());
			$result = $result->replace('@dump', print_r($this->getDataItem(), true));
			
			return $result;
		}
		
		/**
		 * For internal use. (could be overridden in subclass)
		 * 
		 * Exopand this node and its children by calling ->bind($this->getDataItem) 
		 * on controls that implement IBindable and can bind to this instances' DataItem
		 * Also expands text nodes using $this->expandString()
		 *
		 * @param \red\xml\XMLNode $node 
		 */
		protected function expand(\red\xml\XMLNode $node)
		{
			if ($node instanceof XMLText || $node instanceof XMLLiteral)
			{
				$node->setTextContent($this->expandString($node->getTextContent()));
			}
			else if ($node instanceof IBindable && ! $node->isBound())
			{
				$dataItem = $this->getDataItem();
				if ($node->canBindTo($dataItem))
				{
					$node->bind($dataItem);
				}
			}
		}

		/**
		 * For internal use. (could be overridden in subclass)
		 * 
		 * Defines the behaviour that is externally accessible through $this->bind()
		 *
		 * @param \red\xml\XMLNode $node 
		 */
		protected function expandRecursive(\red\xml\XMLNode $node)
		{
			$this->expand($node);
			
			if ($node->hasChildren())
			{
				foreach($node->getChildNodes() as $child)
				{
					$this->expandRecursive($child);
				}
			}
		}
		
		/**
		 * For external use. Binds attempts to bind node and children
		 * to this instances' DataItem
		 *
		 * @param \red\xml\XMLNode $node 
		 */
		public function bind(\red\xml\XMLNode $node)
		{
			$this->expandRecursive($node);
		}
	}
}

#EOF