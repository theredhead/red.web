<?php

namespace red\web\ui\controls
{
	use \red\MBString;
	use \red\web\ui\html\HtmlText;
	
	class StaticText extends BindableControl
	{
		public function __construct($text = '')
		{
			parent::__construct('span');
			$this->text = MBString::withString($text);
		}
		
		// <editor-fold defaultstate="collapsed" desc="Property MBString Text">
		private $text = null;

		/**
		 * @return MBString
		 */
		public function getText()
		{
			return $this->text;
		}

		/**
		 * @param MBString $newText
		 */
		public function setText($newText)
		{
			$newText instanceof MBStrong or $newText = MBString::withString(''.$newText);
			
			if ($newText->length() == 0 && $this->text->length() > 0)
			{
				static::fail('Empty text set!');
			}
			
//			printf("[%s setText: '%s']<br />\n", $this, $newText);
			$this->text = $newText;
		}
		// </editor-fold>
		// <editor-fold defaultstate="collapsed" desc="Property string BindTo">
		private $string = null;

		/**
		 * @return string
		 */
		public function getBindTo()
		{
			return $this->string;
		}

		/**
		 * @param string $newBindTo
		 */
		public function setBindTo($newBindTo)
		{
			$this->string = $newBindTo;
		}
		// </editor-fold>

		/**
		 * Make statictext not crash on rendering if it wasn't bound to data.
		 * 
		 * @return boolean 
		 */
		public function getRequiresDatabinding()
		{
			return false;
		}
		
		/**
		 * IBindable::canBindTo
		 *
		 * @param mixed $dataItem
		 * @return boolean 
		 */
		public function canBindTo($dataItem)
		{
			$result = false;
			if ($this->getBindTo() == null)
			{
				$result = false;
			}
			else if (is_array($dataItem))
			{
				$result = array_key_exists($this->getBindTo(), $dataItem);
			}
			else if (is_object($dataItem))
			{
				$result = method_exists($dataItem, $this->getBindTo())
					or property_exists($dataItem, $this->getBindTo());
			}

//			printf("[%s %s bind to %s using %s]\n<br />", $this, $result ? 'can' : 'cannot', typeid($dataItem), $this->getBindTo());
			return $result;
		}
		
		/**
		 * Bind this static text to a data item
		 * 
		 * @param type $dataItem 
		 */
		public function bind($dataItem)
		{
//			printf("[binding %s to %s using %s]\n<br />", $this, typeid($dataItem), $this->getBindTo());
			parent::bind($dataItem);
		}
		
		/**
		 * build this control during databinding 
		 */
		protected function buildControl()
		{
			if ($this->getBindTo() !== null)
			{
				$property = $this->getBindTo();
				$dataItem = $this->getDatasource();
				
				$text = ''.$this;
				
				if (is_array($dataItem))
				{
					$text = $dataItem[$property];
				}
				else if (is_object($dataItem))
				{
					if(method_exists($dataItem, $property))
					{
						$text = $dataItem->$property();
					}
					else if(method_exists($dataItem, $property))
					{
						$text = $dataItem->$property;;
					}
				}
//				printf("[%s bound to %s using %s resulted in '%s']\n<br />", $this, typeid($dataItem), $this->getBindTo(), $text);
				$this->setText($text);
			}
		}
		
		/**
		 * override parent behaviour to set logical properties from template
		 * 
		 * @param type $name
		 * @param type $value 
		 */
		public function setAttribute($name, $value)
		{
			switch(strtolower($name))
			{
				case 'bindto':
					$this->setBindTo($value);
					break;
				case 'text':
					$this->setText($value);
					break;
				default:
					parent::setAttribute($name, $value);
					break;
			}
		}
		
		/**
		 * normalize this element 
		 */
		public function normalize()
		{
			$this->clear();
			parent::normalize();
			$this->appendChild(new HtmlText($this->text));
		}
	}
}

#EOF