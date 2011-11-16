<?php

namespace red\web\ui\controls
{
	use red\MBString;
	
	require_once 'red/web/ui/html/elements.php';
	
	abstract class BaseControl extends \red\web\ui\html\HtmlTag implements IControl, IStateful
	{
		protected $state = null;

		/**
		 * @return PropertyBag
		 */
		final public function getState()
		{
			return $this->state;
		}
		final public function setState(PropertyBag $state)
		{
			$this->state = $state;
		}
		
		// <editor-fold defaultstate="collapsed" desc="Property boolean Visible">
		/**
		 * @return boolean
		 */
		public function isVisible()
		{
			return $this->state['visible'] == true;
		}

		/**
		 * @param boolean $newVisible
		 */
		public function setVisible($newVisible)
		{
			$this->state['visible'] = $newVisible == true;
		}
		// </editor-fold>
		// <editor-fold defaultstate="collapsed" desc="Property boolean Enabled">
		/**
		 * @return boolean
		 */
		public function isEnabled()
		{
			return $this->state['enabled'] == true;
		}

		/**
		 * @param boolean $newEnabled
		 */
		public function setEnabled($newEnabled)
		{
			$this->state['enabled'] = $newEnabled == true;
		}
		// </editor-fold>

		public function __construct($tagName='div')
		{
			parent::__construct($tagName);
			$this->state = new PropertyBag();
			$this->addCssClass($this->getReflector()->getShortName());
			
			$this->state['visible'] = true;
			$this->state['enabled'] = true;
		}

		protected function createClientEventTrigger($eventName, $eventArgument)
		{
			$this->needsClientSideID = true;
			return sprintf("triggerEvent(document.getElementById('%s'), '%s', '%s');", $this->getClientId(), $eventName, $eventArgument);
		}

		/**
		 * @return WebPage
		 */
		public function getPage()
		{
			return $this->getOwnerDocument();
		}

		private $needsClientSideID = true;
		protected function needsClientSideId()
		{
			return $this->needsClientSideID;
		}
		
		public function preRender()
		{
			if ($this->needsClientSideId())
			{
				$this->setAttribute('id', $this->getClientId());
			}

			foreach($this->getChildNodes() as $child)
			{
				$child instanceof controls\IControl && $child->preRender();
			}
		}

		protected function createEventArgument($eventName, $eventArgument)
		{
			return new \red\EventArgument($eventArgument);
		}

		protected function notePostbackEvent($eventName, $eventArgument)
		{
			$this->getPage()->log('[%s notePostbackEvent: "%s" withArgument: %s]'
					, typeid($this), $eventName, $eventArgument != null ? $eventArgument : '(null)');

			$this->notifyListenersOfEvent($eventName
					, $this->createEventArgument($eventName, $eventArgument));
		}

		public function notePostback(\red\web\http\HttpRequest $request)
		{
			if (false !== ($eventName = $request->getFormField($this->getClientId().'_ev', false)))
			{
				$eventArgument = $request->getFormField($this->getClientId().'_arg', null);
				$this->notePostbackEvent($eventName, $eventArgument);
			}
		}
		
		public function setAttribute($name, $value)
		{
			if (in_array(strtolower($name), array(
				'onclick', 'ondblclick'
			)))
			{
				$this->needsClientSideID = true;
			}
			// this is not an accident.
			parent::setAttribute($name, $value);
		}
	}
}