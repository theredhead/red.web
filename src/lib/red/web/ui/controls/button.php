<?php

namespace red\web\ui\controls
{
	use \red\MBString;
	
	class ButtonClickedEventArgument extends \red\EventArgument
	{
		
	}

	class Button extends BaseControl implements IPublishEvents, \red\web\ui\IThemable
	{
		const EV_CLICKED = 'Clicked';
		
		/**
		 * IPublishEvents::getPublishedEvents
		 * 
		 * @return array
		 */
		public function getPublishedEvents()
		{
			return array(self::EV_CLICKED);
		}
		
		// <editor-fold defaultstate="collapsed" desc="Property string Caption">
		/**
		 * get this buttons' caption
		 * 
		 * @return MBString
		 */
		public function getCaption()
		{
			return MBString::withString($this->state['caption']);
		}

		/**
		 * set this buttons' caption
		 *
		 * @param MBString $newCaption 
		 */
		public function setCaption($newCaption)
		{
 			$this->state['caption']	= (string)$newCaption;
		}
		// </editor-fold>

		/**
		 * format clientside javascript that triggers a (postback) event
		 * 
		 * @param type $name
		 * @return type 
		 */
		protected function formatClientSideEventTrigger($name)
		{
			return sprintf("triggerEvent(this, '%s')", $name);
		}
		
		/**
		 * prepare this button for rendering
		 */
		public function preRender()
		{
			parent::preRender();

			$this->setAttribute('onclick', $this->formatClientSideEventTrigger(self::EV_CLICKED));
			if ($this->getCaption() != '')
			{
				$this->clear();
				$this->appendChild($this->getOwnerDocument()->createText($this->getCaption()));
			}
		}
		
		/**
		 * override parent implementation to set logical properties from template
		 * 
		 * @param type $name
		 * @param type $value 
		 */
		public function setAttribute($name, $value)
		{
			switch(mb_strtolower($name))
			{
				case 'caption' :
					$this->setCaption($value);
					break;
				
				default:
					parent::setAttribute($name, $value);
					break;
			}
		}
		
		/**
		 * override parent implementation to provide more accurate event arguments
		 * 
		 * @param string $eventName
		 * @param string $eventArgument
		 * @return ButtonClickedEventArgument 
		 */
		protected function createEventArgument($eventName, $eventArgument)
		{
			return new ButtonClickedEventArgument($eventArgument);
		}
		
		public function __construct()
		{
			parent::__construct('button');
		}

        /**
         * get an array of resource types to try and register.
         *
         * array should hold filename extensions to register as values
         *
         * example:
         *  return array('css', 'js');
         *
         *
         *
         * @return array
         */
        static public function getThemeResourceTypes()
        {
            return array('css');
        }
    }
}