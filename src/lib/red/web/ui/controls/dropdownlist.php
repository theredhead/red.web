<?php

namespace red\web\ui\controls
{
	use \red\EventArgument;
	use \red\web\ui\controls\IPublishEvents;

	class DropdownList extends BaseControl implements IPublishEvents
	{
		const EV_SELECTEDINDEX_CHANGED = 'SelectedIndexChanged';

		// <editor-fold defaultstate="collapsed" desc="Property boolean AutoPostback">
		private $autoPostback = true;

		/**
		 * @return boolean
		 */
		public function doesAutoPostback()
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

		/**
		 * Holds the items in this list.
		 * 
		 * @var \red\xml\XMLNodeList
		 */
		protected $items = null;

		/**
		 * Get the items in this dropdown list
		 * 
		 * @return \red\xml\XMLNodeList
		 */
		protected function getItems()
		{
			if($this->items === null)
			{
				$this->items = $this->findAll(function ($e) {return $e instanceof DropdownItem;});
			}
			return $this->items;
		}

		/**
		 * @return null
		 */
		public function getSelectedItem()
		{
			$items = $this->getItems();
			return isset($items[$this->getSelectedIndex()]) ? $items[$this->getSelectedIndex()] : null;
		}


		/**
		 * Set the index of the currently selected DropdownItem
		 *
		 * @return integer
		 */
		public function getSelectedIndex()
		{
			return isset($this->state['ix']) ? (integer)$this->state['ix'] : -1;
		}

		/**
		 * Set the index of the currently selected DropdownItem
		 *
		 * @param integer $newSelectedIndex
		 * @return void
		 */
		public function setSelectedIndex($newSelectedIndex, $bubbleEvent=true)
		{
			if ($newSelectedIndex !== $this->getSelectedIndex())
			{
				if ($newSelectedIndex === -1)
				{
					unset($this->state['ix']);
					$this->selectedIndexChanged($bubbleEvent);
				}
				else if ((integer)$newSelectedIndex == $newSelectedIndex)
				{
					$this->state['ix'] = (integer)$newSelectedIndex;
					$this->selectedIndexChanged($bubbleEvent);
				}
				else
				{
					static::fail('SelectedIndex must be an integral value, or null');
				}
			}
		}

		/**
		 * Set the selected index based on a value. this will lookup the first item has
		 * the value $newSelectedValue and select it by index.
		 *
		 * @param $newSelectedValue
		 * @param bool $bubbleEvent
		 * @return
		 */
		public function setSelectedValue($newSelectedValue, $bubbleEvent=true)
		{
			$items = $this->getItems();
			foreach($items as $ix => $item)
			{
				if ($item instanceof DropdownItem)
				{
					if ($item->getValue() === $newSelectedValue)
					{
						$this->setSelectedIndex($ix, $bubbleEvent);
						return;
					}
				}
			}
			self::fail('There is no item with the value "%s" to select.', $newSelectedValue);
		}



		/**
		 * Notifies listeners that the selected index of this dropdownlist has changed.
		 */
		protected function selectedIndexChanged($bubbleEvent=true)
		{
			if ($bubbleEvent)
			{
				$this->notifyListenersOfEvent(self::EV_SELECTEDINDEX_CHANGED, new EventArgument());
			}
		}

		public function __construct()
		{
			parent::__construct('select');
		}

		public function preRender()
		{
			parent::preRender();
			$this->unsetAttribute('value');
			if($this->doesAutoPostback())
			{
				$this->setAttribute('onchange',sprintf('triggerEvent(this, \'%s\', this.selectedIndex)', self::EV_SELECTEDINDEX_CHANGED));
			}
			$items = $this->getItems();
			$selectedIndex = $this->getSelectedIndex();
			foreach($items as $ix => $item)
			{
				if ($ix == $selectedIndex)
				{
					$item->setAttribute('selected', 'selected');
				}
				else
				{
					$item->unsetAttribute('selected');
				}
				$item->setAttribute('value', (string)$ix);
			}
		}

		protected function notePostbackEvent($eventName, $eventArgument)
		{
			switch($eventName)
			{
				case self::EV_SELECTEDINDEX_CHANGED:
					$this->setSelectedIndex((integer)$eventArgument);
					break;

				default:
					parent::notePostbackEvent($eventName, $eventArgument);
					break;
			}
		}

		/**
		 * @return array with event names
		 */
		public function getPublishedEvents()
		{
			return array(self::EV_SELECTEDINDEX_CHANGED);
		}
	}
}