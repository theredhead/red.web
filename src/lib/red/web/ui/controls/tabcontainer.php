<?php

namespace red\web\ui\controls
{
	use \red\EventArgument;
	use \red\web\ui\IThemable;
	use \red\web\ui\ScriptManager;

	class ActiveTabChangedEventArgument extends EventArgument
	{
		
	}

	/**
	 * Implements a container for TabPages.
	 *
	 * @todo: persist active tab through postbacks when jquery is used to switch tabs
	 * @todo: change hash based on active tab and vice versa (use friendly names and path based hash to allow multiple instances)
	 */
	class TabContainer extends BaseControl implements IThemable
	{
		const EV_TABBUTTON_CLICKED = 'TabButtonClicked';

		// <editor-fold defaultstate="collapsed" desc="Property boolean RequirePostbackForTabChange">
		private $requirePostbackForTabChange = true;

		/**
		 * Get a flag indicating whether it is okay to use jquery to switch tabs
		 * (in which case the current tab is not (yet) persisted in state) or not.
		 *
		 * @return boolean
		 */
		public function doesRequirePostbackForTabChange()
		{
			return $this->hasEventListeners(self::EV_TABBUTTON_CLICKED) or
					$this->requirePostbackForTabChange == true;
		}

		/**
		 * Set wether a tab change should trigger a postback.
		 *
		 * Setting this to false has no effect if a server side event listener is registered
		 *
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

		public function setAttribute($name, $value)
		{
			switch(strtolower($name))
			{
				case 'autopostback':
					$this->setRequiresPostbackForTabChange(\red\Convert::toBoolean($value));
					break;
				default:
					parent::setAttribute($name, $value);
					break;
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

			$useJQ = !$this->doesRequirePostbackForTabChange();

			if ($useJQ)
			{
				$this->getPage()->registerClientScript(ScriptManager::CDN_URL_JQUERY);
			}

			foreach($tabs as $tab)
			{
				$btnLi = new \red\web\ui\html\HtmlListItem();
				$btnLi->setAttribute('rel', '#'.$tab->getClientId());
				$tab->createTabButton($btnLi);
				
				if (!$useJQ)
				{
					$btnLi->setAttribute('onclick',
						$this->createClientEventTrigger(
							self::EV_TABBUTTON_CLICKED, $this->findIndexByTab($tab)));
				}
				else
				{
					$btnLi->setAttribute('onclick', implode(' ', array(
							  "(function(o){"
							, "console.log('You clicked: ', o);"                            // log what got clicked
							, "o.parent('ul').children('li').removeClass('ActiveTab');"     // remove previous ActiveTab
							, "o.addClass('ActiveTab');"                                    // add new ActiveTab
							, "$(o.attr('rel')).fadeIn(100).siblings(':visible:not(:first)').fadeOut(100);"   // fadIn new and fadeOut old
							, "})($(this));"
					)));
				}
				if ($activeTab->isSameInstance($tab))
				{
					$btnLi->addCssClass('ActiveTab');
					$tab->addCssClass('ActiveTab');
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