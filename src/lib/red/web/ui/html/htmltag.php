<?php

namespace red\web\ui\html
{
	use red\MBString;
	use red\xml\XMLText;
	
	class HtmlTag extends XHTMLElement
	{
		public function __construct($tagName)
		{
			parent::__construct($tagName);
		}
		
		/**
		 * find the first element in the node tree (starting here) that has a 
		 * localname $localName.
		 * 
		 * by default is case sensitive and will follow the tree down if a match
		 * is not found in this elements immediate children
		 *
		 * @param MBString $localName
		 * @param boolean $caseSensitive defaults to true
		 * @param boolean $deep defaults to true
		 * 
		 * @return XMLElement
		 */
		public function findFirstElementByLocalName($localName, $caseSensitive=true, $deep=true)
		{
			$localName instanceof MBString or $localName = MBString::withString($localName);
			$result = null;
			if ($caseSensitive)
			{
				$result = $this->findFirst(
						function($node) use ($localName)
						{
							return $node instanceof \red\xml\XMLElement 
									&& $node->getLocalName() == $localName;
						}, $deep);
			}
			else
			{
				$lowerLocalName = $localName->toLower();
				$result = $this->findFirst(
						function($node) use ($localName, $lowerLocalName)
						{
							return $node instanceof \red\xml\XMLElement 
									&& ''.$node->getLocalName()->toLower() == ''.$lowerLocalName;
						}, $deep);
			}
			return $result;
		}
	}
}

#EOF