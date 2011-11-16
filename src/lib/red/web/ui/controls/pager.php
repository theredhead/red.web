<?php

namespace red\web\ui\controls
{
	use \red\Object;
	use \red\MBString;
	use \red\EventArgument;
	use red\web\ui\html\HtmlText;

	class PagerEventArgument extends EventArgument
	{
		// <editor-fold defaultstate="collapsed" desc="Property integer NewPageIndex">
		private $newPageIndex = 0;

		/**
		 * @return integer
		 */
		public function getNewPageIndex()
		{
			return $this->newPageIndex;
		}

		/**
		 * @param integer $newNewPageIndex
		 */
		public function setNewPageIndex($newNewPageIndex)
		{
			$this->newPageIndex = $newNewPageIndex;
		}
		// </editor-fold>

		public function __construct($pageIndex)
		{
			parent::__construct();
			$this->setNewPageIndex($pageIndex);
		}
	}
	
	/**
	 * Pager represents a simple control that can manage the interaction
	 * between an IPagable control and its viewport based on user interaction
	 */
	class Pager extends BaseControl
	{
		/**
		 * the event this control exposes
		 */
		const EV_PAGE_TO = 'PageTo';

		// <editor-fold defaultstate="collapsed" desc="Property integer MaximumNumberOfPageButtons">
		private $maxLinks = 10;

		/**
		 * @return integer
		 */
		public function getMaximumNumberOfPageButtons()
		{
			return $this->maxLinks;
		}

		/**
		 * @param integer $newMaximumNumberOfPageButtons
		 */
		public function setMaximumNumberOfPageButtons($newMaximumNumberOfPageButtons)
		{
			$this->maxLinks = (int)$newMaximumNumberOfPageButtons;
		}
		// </editor-fold>
		// <editor-fold defaultstate="collapsed" desc="Property string For">
		private $for = null;
		private $forControl = null;
		/**
		 * @return string
		 */
		public function getFor()
		{
			return $this->for;
		}

		/**
		 * Get the control (IPagable) that this pager operates on
		 *
		 * @return type 
		 */
		public function getForControl()
		{
			if ($this->forControl == null)
			{
				// $this->forControl = $this->getPage()->getElementById($this->getFor());
				$this->forControl = $this->getPage()->getChildControl($this->getFor());
			}
			return $this->forControl;
		}

		/**
		 * @param string $newFor
		 */
		public function setFor($newFor)
		{
			$this->for = $newFor;
			$this->forControl = null;
		}

		/**
		 * @param string $newFor
		 */
		public function setForControl(IPageable $newForControl)
		{
			$this->for = $newForControl->hasAttribute('id')
					? $newForControl->getAttribute('id')
					: null;

			$this->forControl = $newForControl;
		}
		// </editor-fold>

		public function __construct()
		{
			parent::__construct();
			$this->registerEventListener('PagerButtonClicked', 'onPagerButtonClicked', $this);
		}
		
		/**
		 * expand logical properties from the template.
		 *
		 * @param string $name
		 * @param string $value 
		 */
		public function setAttribute($name, $value)
		{
			switch(strtolower($name))
			{
				case 'for' :
					$this->setFor($value);
					break;
				default:
					parent::setAttribute($name, $value);
					break;
			}
		}

		/**
		 * Gets called by the page to allow this control to fixup its internal
		 * markup.
		 */
		public function preRender()
		{
			parent::preRender();
			$control = $this->getForControl();
			if ($control === null)
			{
				static::fail('Pageable control "%s" not found!', $this->getFor());
			}
			
			// get some numbers handy we'll be working with.
			$maxButtons		= $this->getMaximumNumberOfPageButtons();
			$range			= floor($maxButtons / 2);
			$numPages		= ceil($control->getNumberOfItems() / $control->getPageSize());
			$startIx		= max($control->getCurrentPageIndex() - $range, 0);
			$stopIx			= min($control->getCurrentPageIndex() + $range, $numPages);
			$currentPageIx  = $control->getCurrentPageIndex();
			$bigStep		= $maxButtons;
			
			// make sure we'll display as much buttons as we're allowed to
			// and that all targets are valid.
			// expand to the left first...
			while(($stopIx - $startIx) < $maxButtons && $startIx > 0)
			{
				$startIx --;
			}
			// then to the right if needed.
			while(($stopIx - $startIx) < $maxButtons && $stopIx < $numPages -1)
			{
				$stopIx ++;
			}

			// buttons for previous and next page
			$btnPrev	 = $this->createTriggerToButton('←', max($currentPageIx-1, 0));
			$btnNext	 = $this->createTriggerToButton('→', min($currentPageIx+1, $numPages-1));

			// buttons for first and last page
			$btnFirst	 = $this->createTriggerToButton('↖', 0);
			$btnLast	 = $this->createTriggerToButton('↘', $numPages-1);

			// if there are more pages to see than buttons on screen, create the bigstep buttons
			if ($bigStep < $numPages)
			{
				$btnBigRev	 = $this->createTriggerToButton('⇞', max($currentPageIx - $bigStep, 0));
				$btnBigFwd	 = $this->createTriggerToButton('⇟', min($currentPageIx + $bigStep, $numPages-1));
			}
			
			$this->appendChild($btnFirst);
			isset($btnBigRev) and $this->appendChild($btnBigRev);
			$this->appendChild($btnPrev);
			// add the refular buttons
			for ($pageIx = $startIx; $pageIx < $stopIx; $pageIx ++)
			{
				$button = $this->createTriggerToButton(''.($pageIx+1), $pageIx);
				$this->appendChild($button);
			}
			$this->appendChild($btnNext);
			isset($btnBigFwd) and $this->appendChild($btnBigFwd);
			$this->appendChild($btnLast);
		}
		
		/**
		 * Create a button used inside this pager that will trigger the 
		 * EV_PAGE_TO event on the server when pushed in the client.
		 *
		 * @param MBString $label
		 * @param integer $toPageIndex
		 * @return Button 
		 */
		protected function createTriggerToButton($label, $toPageIndex)
		{
			$label instanceof MBString or $label = MBString::withString($label);

			$button = new Button();
			$button->setAttribute('onclick', 
					sprintf("triggerEvent(document.getElementById('%s'), 'to', %d)", 
							$this->getClientId(), $toPageIndex));

			$button->appendChild(new HtmlText($label));

			if ($toPageIndex == $this->getForControl()->getCurrentPageIndex())
			{
				$button->setAttribute('disabled', 'disabled');
			}
			return $button;
		}
		
		/**
		 * gets called whenever there is a postback event for this control
		 *
		 * @param type $eventName
		 * @param type $eventArgument 
		 */
		public function notePostbackEvent($eventName, $eventArgument)
		{
			switch($eventName)
			{
				case 'to' : 
					$this->getForControl()->setCurrentPageIndex((int)$eventArgument);
				break;
				default:
					parent::notePostbackEvent($eventName, $eventArgument);
			}
		}
	}
}

#EOF