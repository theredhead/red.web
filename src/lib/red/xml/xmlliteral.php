<?php

namespace red\xml
{
	class XMLLiteral extends XMLText
	{

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