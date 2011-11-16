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
	
	class Pager extends BaseControl
	{
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

		public function preRender()
		{
			parent::preRender();
			$control = $this->getForControl();
			if ($control === null)
			{
				static::fail('Pageable control "%s" not found!', $this->getFor());
			}
			
			$maxButtons		= $this->getMaximumNumberOfPageButtons();
			$range			= floor($maxButtons / 2);
			$numPages		= ceil($control->getNumberOfItems() / $control->getPageSize());
			$startIx		= max($control->getCurrentPageIndex() - $range, 0);
			$stopIx			= min($control->getCurrentPageIndex() + $range, $numPages);
			
			while(($stopIx - $startIx) < $maxButtons && $startIx > 0)
			{
				$startIx --;
			}
			while(($stopIx - $startIx) < $maxButtons && $stopIx < $numPages -1)
			{
				$stopIx ++;
			}
			
			$currentPageIx  = $control->getCurrentPageIndex();
			$bigStep		= $maxButtons;

			// @TODO: first, previous, next, last
			$btnPrev	 = $this->createTriggerToButton('←', max($currentPageIx-1, 0));
			$btnNext	 = $this->createTriggerToButton('→', min($currentPageIx+1, $numPages-1));

			$btnFirst	 = $this->createTriggerToButton('↖', 0);
			$btnLast	 = $this->createTriggerToButton('↘', $numPages-1);

			// if there are more pages to see than buttons on screen, create the big jump buttons
			if ($bigStep < $numPages)
			{
				$btnBigRev	 = $this->createTriggerToButton('⇞', max($currentPageIx - $bigStep, 0));
				$btnBigFwd	 = $this->createTriggerToButton('⇟', min($currentPageIx + $bigStep, $numPages-1));
			}
			
			$this->appendChild($btnFirst);
			isset($btnBigRev) and $this->appendChild($btnBigRev);
			$this->appendChild($btnPrev);
			for ($pageIx = $startIx; $pageIx < $stopIx; $pageIx ++)
			{
				$button = $this->createTriggerToButton(''.($pageIx+1), $pageIx);
				$this->appendChild($button);
			}
			$this->appendChild($btnNext);
			isset($btnBigFwd) and $this->appendChild($btnBigFwd);
			$this->appendChild($btnLast);
		}
		
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