<?php

namespace red\web\ui\controls
{
	class TabPage extends BaseControl
	{
		public function __construct()
		{
			parent::__construct('li');
		}
		
		// <editor-fold defaultstate="collapsed" desc="Property string LabelText">
		private $labelText = 'Unnamed Tab';

		/**
		 * @return string
		 */
		public function getLabelText()
		{
			return $this->labelText;
		}

		/**
		 * @param string $newLabelText
		 */
		public function setLabelText($newLabelText)
		{
			$this->labelText = $newLabelText;
		}
		// </editor-fold>
		
		public function createTabButton(\red\web\ui\html\HtmlListItem $li)
		{
			$li->appendChild(new \red\web\ui\html\HtmlText($this->getLAbelText()));
		}
		
		public function setAttribute($name, $value)
		{
			switch(strtolower($name))
			{
				case 'caption' :
				case 'labeltext' :
					$this->setLabelText($value);
					break;
				default:
					parent::setAttribute($name, $value);
					break;
			}
		}
		
		public function preRender()
		{
			parent::preRender();
			
			$this->addCssClass('TabPage');
		}
	}
}