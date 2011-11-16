<?php

namespace red\web\ui\controls
{
	use \red\MBString;
	use \red\web\ui\html\HtmlTag;
	use \red\web\ui\html\HtmlText;
	use \red\web\ui\html\HtmlUnorderedList;
	use \red\web\ui\html\HtmlListItem;
	use \red\web\ui\controls\IRepeaterDatasourceDelegate;
	
	use \red\EventArgument;
	use \red\xml\XMLText;
	use \red\xml\XMLElement;
	use \red\xml\XMLLiteral;	

	class ItemClickedEventArgument extends EventArgument
	{
		// <editor-fold defaultstate="collapsed" desc="Property integer RowIndex">
		private $rowIndex = null;

		/**
		 * @return integer
		 */
		public function getRowIndex()
		{
			return $this->rowIndex;
		}

		/**
		 * @param integer $newRowIndex
		 */
		private function setRowIndex($newRowIndex)
		{
			$this->rowIndex = $newRowIndex;
		}
		// </editor-fold>
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

		public function __construct($rowIndex, $dataItem)
		{
			parent::__construct();
			$this->setRowIndex($rowIndex);
			$this->setDataItem($dataItem);
		}
	}

	class Repeater extends BaseControl implements IBindable
	{
		const EV_ITEM_CLICKED = 'ItemClicked';
		const EV_SELECTEDINDEX_CHANGED = 'SelectedIndexChanged';
		
		// <editor-fold defaultstate="collapsed" desc="Property boolean AutoPostback">
		private $autoPostback = true;

		/**
		 * @return boolean
		 */
		public function getAutoPostback()
		{
			return $this->autoPostback == true;
		}

		/**
		 * @param boolean $newAutoPostback
		 */
		public function setAutoPostback($newAutoPostback)
		{
			$this->autoPostback = $newAutoPostback == true;
		}
		// </editor-fold>
		// <editor-fold defaultstate="collapsed" desc="Property integer SelectedIndex">
		/**
		 * @return integer
		 */
		public function getSelectedIndex()
		{
			return isset($this->state['selIx']) ? $this->state['selIx'] : null;
		}

		/**
		 * @param integer $newSelectedIndex
		 */
		public function setSelectedIndex($newSelectedIndex)
		{
			if ($newSelectedIndex !== $this->getSelectedIndex())
			{
				if ($newSelectedIndex === false)
				{
					unset($this->state['selIx']);
					$this->selectedIndexChanged();
				}
				else if ((integer)$newSelectedIndex == $newSelectedIndex)
				{
					$this->state['selIx'] = (integer)$newSelectedIndex;
					$this->selectedIndexChanged();
				}
				else
				{
					static::fail('SelectedIndex must be an integral value, or null');
				}
			}
		}

		/**
		 * Notifies listeners that the selected index of this repeater has changed.
		 */
		protected function selectedIndexChanged()
		{
			$this->notifyListenersOfEvent(self::EV_SELECTEDINDEX_CHANGED, new EventArgument());
		}
		// </editor-fold>		
		
		// <editor-fold defaultstate="collapsed" desc="Property HTMLTag HeaderTemplate">
		private $headerTemplate = null;

		/**
		 * @return HtmlTag
		 */
		public function getHeaderTemplate()
		{
			if($this->headerTemplate === null)
			{
				$this->headerTemplate = $this->findFirstElementByLocalName('HeaderTemplate', false, false);
			}
			return $this->headerTemplate;
		}

		/**
		 * @param HtmlTag $newHeaderTemplate
		 */
		public function setHeaderTemplate(HtmlTag $newHeaderTemplate)
		{
			$this->headerTemplate = $newHeaderTemplate;
		}
		// </editor-fold>
		// <editor-fold defaultstate="collapsed" desc="Property HtmlTag FooterTemplate">
		private $footerTemplate = null;

		/**
		 * @return HtmlTag
		 */
		public function getFooterTemplate()
		{
			if($this->footerTemplate === null)
			{
				$this->footerTemplate = $this->findFirstElementByLocalName('FooterTemplate', false, false);
			}
			return $this->footerTemplate;
		}

		/**
		 * @param HtmlTag $newFooterTemplate
		 */
		public function setFooterTemplate(HtmlTag $newFooterTemplate)
		{
			$this->footerTemplate = $newFooterTemplate;
		}
		// </editor-fold>
		// <editor-fold defaultstate="collapsed" desc="Property HtmlTag ItemTemplate">
		private $itemTemplate = null;

		/**
		 * @return HtmlTag
		 */
		public function getItemTemplate()
		{
			if($this->itemTemplate === null)
			{
				$this->itemTemplate = $this->findFirstElementByLocalName('ItemTemplate', false, false);
				if($this->itemTemplate === null)
				{
					static::fail('No ItemTemplate found.');
				}
			}
			return $this->itemTemplate;
		}

		/**
		 * @param HtmlTag $newItemTemplate
		 */
		public function setItemTemplate(HtmlTag $newItemTemplate)
		{
			$this->itemTemplate = $newItemTemplate;
		}
		// </editor-fold>
		// <editor-fold defaultstate="collapsed" desc="Property HtmlTag EmptyTemplate">
		private $emptyTemplate = null;

		/**
		 * @return HtmlTag
		 */
		public function getEmptyTemplate()
		{
			if($this->emptyTemplate === null)
			{
				$this->emptyTemplate = $this->findFirstElementByLocalName('EmptyTemplate', false, false);
			}
			return $this->emptyTemplate;
		}

		/**
		 * @param HtmlTag $newEmptyTemplate
		 */
		public function setEmptyTemplate(HtmlTag $newEmptyTemplate)
		{
			$this->emptyTemplate = $newEmptyTemplate;
		}
		// </editor-fold>

		
		// <editor-fold defaultstate="collapsed" desc="Datasource delegate">
		/**
		 * @var IRepeaterDatasourceDelegate
		 */
		protected $delegate = null;
		
		/**
		 * Get the delegate responsible for this controls data
		 * 
		 * @return type IRepeaterDatasourceDelegate
		 */
		protected function getDelegate()
		{
			if ($this->delegate === null)
			{
				$this->delegate = $this->findFirst(function($o){return $o instanceof IRepeaterDatasourceDelegate;});
			}
			if ($this->delegate === null)
			{
				static::fail('No IRepeaterDatasourceDelegate found');
			}
			return $this->delegate;
		}
		// </editor-fold>

		/**
		 * IBindable::canBindTo
		 * 
		 * @param mixed $dataItem
		 * @return boolean
		 */
		public function canBindTo($dataItem)
		{
			return $dataItem instanceof IRepeaterDatasourceDelegate;
		}
		
		/**
		 * IBindable::bind
		 * 
		 * @param IRepeaterDatasourceDelegate $dataItem 
		 */
		public function bind($dataItem)
		{
			$this->canBindTo($dataItem) or $this->fail('Cannot bind to %s', typeid($dataItem));
			$this->setDelegate($dataItem);
			$this->buildControl();
			$this->isBound = true;
		}
		
		/**
		 * internal flag that indicates wether this control has been databound
		 * @var type 
		 */
		protected $isBound = false;
		
		/**
		 * see if this control has been databound
		 * @return type 
		 */
		public function isBound()
		{
			return $this->isBound;
		}

		/**
		 * build this controls inner tree 
		 */
		protected function buildControl()
		{
			$delegate = $this->getDelegate();
			$header = $this->getHeaderTemplate();
			$footer = $this->getFooterTemplate();
			$empty = $this->getEmptyTemplate();
			$template = $this->getItemTemplate();
			
			$this->clear();

			if ($header && $header->hasChildren())
			{
				foreach($header->getChildNodes() as $child)
				{
					$this->appendChild($child);
				}
			}

			$this->buildItems($template, $empty);

			if ($footer && $footer->hasChildren())
			{
				foreach($footer->getChildNodes() as $child)
				{
					$this->appendChild($child);
				}
			}
			
			$this->isBuilt = true;
		}
		
		/**
		 * build this controls items
		 *
		 * @param HtmlTag $template
		 * @param HtmlTag $emptyTemplate 
		 */
		protected function buildItems(HtmlTag $template, HtmlTag $emptyTemplate=null)
		{
			$delegate = $this->getDelegate();
			$numberOfItems = $delegate->numberOfRowsInRepeater($this);
			
			$list = $this->appendchild(new HtmlUnorderedList());
			
			for ($ix = 0; $ix < $numberOfItems; $ix ++)
			{
				$dataItem = $delegate->objectValueForRepeaterAtRowIndex($this, $ix);
				
				if ($dataItem === null)
				{
					if ($emptyTemplate !== null)
					{
						$item = $this->expandTemplateIntoListItem($emptyTemplate, $dataItem);
					}
				}
				else
				{
					$item = $this->expandTemplateIntoListItem($template, $dataItem);
				}
				$list->appendChild($item);
				if ($this->getAutoPostback())
				{
					$item->setAttribute('onclick', 
							$this->createClientEventTrigger(
								self::EV_ITEM_CLICKED, $ix));
				}
				if ($this->getSelectedIndex() !== null && $ix == $this->getSelectedIndex())
				{
					$item->addCssClass('selected');
				}
			}
		}

		/**
		 * expand a template into final nodes, performs databinding on a copy of
		 * the template
		 *
		 * @param HtmlTag $template
		 * @param type $dataItem
		 * @return HtmlListItem 
		 */
		protected function expandTemplateIntoListItem(HtmlTag $template, $dataItem)
		{
			$binder = new DataBinder($dataItem);
			$item = new HtmlListItem();
			
			foreach($template->getChildNodes() as $child)
			{
				$item->appendChild($child->copy());
			}
			$binder->bind($item);
			return $item;
		}
		
		/**
		 * respond to the postback event EV_ITEM_CLICKED
		 * 
		 * @param type $rowIndex 
		 */
		protected function itemClicked($rowIndex)
		{
			$valueObject = $this->getDelegate()->objectValueForRepeaterAtRowIndex($this, $rowIndex);
			
			$this->setSelectedIndex($rowIndex);
			
			$this->notifyListenersOfEvent(self::EV_ITEM_CLICKED, 
					new ItemClickedEventArgument($rowIndex, $valueObject));
		}
		
		/**
		 * respond to postback events
		 * 
		 * @param type $eventName
		 * @param type $eventArgument 
		 */
		protected function notePostbackEvent($eventName, $eventArgument)
		{
			switch($eventName)
			{
				case self::EV_ITEM_CLICKED :
					$this->itemClicked((integer)$eventArgument);
					break;

				default:
					parent::notePostbackEvent($eventName, $eventArgument);
					break;
			}
		}
		
		/**
		 * internal flag to indicate wether this control has been built
		 * 
		 * @var boolean
		 */
		protected $isBuilt = false;
		
		/**
		 * see if this control has been built. 
		 */
		public function preRender()
		{
			if (! $this->isBuilt)
			{
				$this->buildControl();
			}
			parent::preRender();
		}
	}
}