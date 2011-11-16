<?php

namespace red\xml
{
	use red\MBString;

	class XMLText extends XMLNode
	{
		/**
		 * @var \red\MBString
		 */
		protected $textContent;
		
		/**
		 * @return MBString
		 */
		public function getTextContent()
		{
			if ($this->textContent === null)
			{
				$this->textContent = MBString::defaultValue();
			}
			return $this->textContent;
		}
		/**
		 *
		 * @param MBString $textContent 
		 */
		public function setTextContent(MBString $textContent)
		{
			$this->textContent = $textContent;
		}
		
		/**
		 * Get a flag indicating if the only characters in this text are whitespace characters
		 *
		 * @return boolean
		 */
		public function isWhitespace()
		{
			return strlen(str_replace("\n", '', trim(''.$this->textContent))) == 0;
		}
		
		/**
		 * @param MBString $tagName
		 * @return XMLElement 
		 */
		public function __construct($textContent=null)
		{
			parent::__construct();

			if ($textContent !== null)
			{
				$textContent instanceof MBString or $textContent = MBString::withString($textContent);
				$this->setTextContent($textContent);
			}
			return $this;
		}

		/**
		 * Get a copy of this instance
		 */
		public function copy()
		{
			$copy = new static($this->getTextContent());
			return $copy;
		}
	}
}

#EOF