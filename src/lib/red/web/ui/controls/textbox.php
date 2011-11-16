<?php

namespace red\web\ui\controls
{
	use red\web\ui\html\HtmlTag;
	use red\web\ui\html\HtmlText;

	class Textbox extends InputControl
	{
		// <editor-fold defaultstate="collapsed" desc="Property boolean MultipleLine">
		/**
		 * @var boolean
		 */
		protected $multipleLine = false;
		
		/**
		 * @return boolean
		 */
		public function isMultipleLine()
		{
			return $this->multipleLine == true;
		}
		
		/**
		 * @param boolean
		 */
		public function setIsMultipleLine($newMultipleLine)
		{
			$this->multipleLine = $newMultipleLine == true;
		}
		// </editor-fold>
		
		public function setAttribute($name, $value)
		{
			switch(strtolower($name))
			{
				case 'multipleline' :
					$this->setIsMultipleLine(in_array($value, array('1', 1, true, 'true', 'yes', 'on')));
					break;

				default:
					parent::setAttribute($name, $value);
					break;
			}
		}
		
		/**
		 * Normalize is called by XMLWriter, whereas preRender is called by the page.
		 */
		public function normalize()
		{
			parent::normalize();

			if ($this->isMultipleLine())
			{
				$this->setTagName('textarea');
				$this->unsetAttribute('value');
				$this->unsetAttribute('type');
				$this->appendChild(new HtmlText($this->getValue()));
			}
			else
			{
				$this->setTagName('input');
				$this->setAttribute('type', 'text');
			}
		}
		
		public function __construct()
		{
			parent::__construct('text');
		}
	}
}