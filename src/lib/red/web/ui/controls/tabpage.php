<?php

namespace red\web\ui\controls
{
	class TabPage extends BaseControl
	{
		public function __construct()
		{
			parent::__construct('li');
			$this->addCssClass('TabPage');

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
		
		/**
		 * called by the tab container to allow the TabPage to create its own
		 * button on the tabbar.
		 *
		 * @param \red\web\ui\html\HtmlListItem $li 
		 */
		public function createTabButton(\red\web\ui\html\HtmlListItem $li)
		{
			$li->appendChild(new \red\web\ui\html\HtmlText($this->getLAbelText()));
		}
		
		/**
		 * expand logical properties from the template
		 *
		 * @param type $name
		 * @param type $value 
		 */
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
	}
}