<?php

namespace red\web\ui\controls
{
	use \red\EventArgument;
	use \red\web\ui\IThemable;

	class ActiveTabChangedEventArgument extends EventArgument
	{
		
	}

	class TabContainer extends BaseControl implements IThemable
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

		/**
		 * Get the TabPage in the $index-th position in this TabContainer
		 *
		 * @param integer $index
		 * @return TabPage
		 */
		protected function findTabByIndex($index)
		{
			$tabs = $this->getTabs();
			return isset($tabs[(integer)$index]) ? $tabs[(integer)$index]
					: static::fail('Not found');
		}

		/**
		 * Get a TabPages' position in this TabContainer
		 *
		 * @param TabPage $tab
		 * @return integer 
		 */
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

		/**
		 * gets called by the framework when a postback event is recieved that
		 * references this control.
		 *
		 * @param type $eventName
		 * @param type $argument 
		 */
		protected function notePostbackEvent($eventName, $argument)
		{
			if ($eventName == self::EV_TABBUTTON_CLICKED)
			{
				$tab = $this->findTabByIndex((integer)$argument);
				$this->setActiveTab($tab);
			}
		}
		
		/**
		 * called by the page to notfy this control that it is about to be 
		 * rendered, so it can finish up its internal markup
		 */
		public function preRender()
		{
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

		/**
		 * get an array of resource types to try and register.
		 *
		 * @return array
		 */
		static public function getThemeResourceTypes()
		{
			return array('css');
		}
	}
}