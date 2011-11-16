<?php

namespace red\web\ui\controls
{
	use \red\EventArgument;

	class ActiveTabChangedEventArgument extends EventArgument
	{
		
	}

	class TabContainer extends BaseControl
	{
		const EV_TABBUTTON_CLICKED = 'TabButtonClicked';
		
		// <editor-fold defaultstate="collapsed" desc="Property boolean RequirePostbackForTabChange">
		private $requirePostbackForTabChange = true;

		/**
		 * @return boolean
		 */
		public function doesRequirePostbackForTabChange()
		{
			return $this->requirePostbackForTabChange == true;
		}

		/**
		 * @param boolean $newRequirePostbackForTabChange
		 */
		public function setRequiresPostbackForTabChange($newRequirePostbackForTabChange)
		{
			$this->requirePostbackForTabChange = $newRequirePostbackForTabChange == true;
		}

		// </editor-fold>
				
		// <editor-fold defaultstate="collapsed" desc="State Properties">
		/**
		 * @return TableColumn
		 */
		public function getActiveTab()
		{
			return $this->findTabByIndex((integer)$this->state['active']);
		}
		/**
		 * @param TabPage $tab 
		 */
		public function setActiveTab(TabPage $tab)
		{
			$this->state['active'] = $this->findIndexByTab($tab);
		}

		protected function findTabByIndex($index)
		{
			$tabs = $this->getTabs();
			return isset($tabs[(integer)$index]) ? $tabs[(integer)$index]
					: static::fail('Not found');
		}

		protected function findIndexByTab(TabPage $tab)
		{
			$tabs = $this->getTabs();
			
			// cannot foreach this because it resets the pointer causing outside searcg
			// never to finish because of inside searches while building headers.
			for($ix = 0; $ix < count($tabs); $ix ++)
			{
				if ($tabs[$ix]->isSameInstance($tab))
				{
					return $ix;
				}
			}
			
			static::fail('That\'s not one of mine!');
		}
		// </editor-fold>

		
		// </editor-fold>
		// <editor-fold defaultstate="collapsed" desc="Property array Tabs">
		private $tabs = null;

		/**
		 * @return array
		 */
		public function getTabs()
		{
			if ($this->tabs === null)
			{
				$this->tabs = array();
				foreach($this->getChildNodes() as $child)
				{
					if ($child instanceof TabPage)
					{
						array_push($this->tabs, $child);
					}
				}
			}
			return $this->tabs;
		}

		/**
		 * @param array $newPages
		 */
		public function setTabs(array $tabs)
		{
			$this->tabs = $tabs;
		}
		// </editor-fold>

		public function __construct()
		{
			parent::__construct('ul');
		}
		
		protected function notePostbackEvent($eventName, $argument)
		{
			if ($eventName == self::EV_TABBUTTON_CLICKED)
			{
				$tab = $this->findTabByIndex((integer)$argument);
				$this->setActiveTab($tab);
			}
		}
		
		public function preRender()
		{
			$this->getPage()->registerStyleSheet('/css/tabs.css');
			
			$this->addCssClass('TabContainer');

			$tabs = $this->getTabs();
			$this->clear();
			
			$first = $this->appendChild(new \red\web\ui\html\HtmlListItem());
			$tabList = $first->appendChild(new \red\web\ui\html\HtmlUnorderedList());
			$contentList = $this;

			$activeTab = $this->getActiveTab();

			$tabList->addCssClass('TabButtonBar');
			$contentList->addCssClass('TabPages');
			
			foreach($tabs as $tab)
			{
				$btnLi = new \red\web\ui\html\HtmlListItem();
				$tab->createTabButton($btnLi);
				
				if ($activeTab->isSameInstance($tab))
				{
					$btnLi->addCssClass('ActiveTab');
					$tab->addCssClass('ActiveTab');
				}
				else
				{
					if ($this->doesRequirePostbackForTabChange())
					{
						$btnLi->setAttribute('onclick', 
							$this->createClientEventTrigger(
								self::EV_TABBUTTON_CLICKED, $this->findIndexByTab($tab)));
					}
					else
					{
						
					}
				}
				
				$tabList->appendChild($btnLi);
				$contentList->appendChild($tab);
			}
			
			parent::preRender();
		}
	}
}