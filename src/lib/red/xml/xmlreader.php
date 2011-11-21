<?php
namespace red\xml
{
	use red\MBString;
	use red\MBStringBuilder;
	use \DOMDocument;
	use \DOMNode;
	use \DOMElement;
	use \DOMText;
	use \DOMComment;
	use \DOMCData;
	use \DOMCdataSection;
	
	class XMLReader extends \red\Object
	{
		/**
		 * @return XMLElement
		 */
		public function read($xmlString, XMLElement $inElement=null)
		{
			if ($xmlString == '')
			{
				static::fail('xmlString cannot be empty.');
			}
			libxml_use_internal_errors(true);

			$ownerDocument = $inElement->getOwnerDocument();
			$ownerDocument instanceof XMLElement or $ownerDocument = new XMLDocument();
			
			$doc = new DOMDocument('1.0', MBString::ENCODING_UTF8);
			$doc->loadXML($xmlString);
			
			if ($doc->documentElement == null)
			{
				static::fail('There is no root element: <pre>%s</pre>', htmlentities($xmlString));
			}

			$root = $this->parseElement($doc->documentElement, $ownerDocument, $inElement);
			
			return $inElement;
		}
		
		protected function createElement($tagName, DOMElement $originalElement)
		{
			return new XMLElement($tagName);
		}
		
		protected function parse(DOMNode $node, XMLDocument $ownerDocument)
		{
			$result = null;

			if ($node instanceof DOMElement)
			{
				$result = $this->parseElement($node, $ownerDocument);
			}
			else if ($node instanceof DOMCdataSection)
			{
				$result = $ownerDocument->createCDataSection($node->nodeValue);
			}
			else if ($node instanceof DOMText)
			{
				$result = $ownerDocument->createText($node->nodeValue);
			}

			return $result;
		}
		
		protected function parseElement(DOMElement $element, XMLDocument $ownerDocument=null, $result=null)
		{
			$result = $result instanceof XMLElement
					? $result->setTagName($element->tagName)
					: $this->createElement($element->tagName, $element);
			
			if ($element->hasAttributes())
			{
				foreach($element->attributes as $attr)
				{
					$result->setAttribute($attr->name, $attr->value);
				}
			}

			if ($element->hasChildNodes())
			{
				foreach($element->childNodes as $child)
				{
					$parsedChild = $this->parse($child, $ownerDocument);
					if ($parsedChild !== null)
					{
						$result->appendChild($parsedChild);
					}
				}
			}

			return $result;
		}
		
		protected function parseDocument(DOMElement $element, XMLDocument $ownerDocument=null)
		{
			$ownerDocument = $ownerDocument instanceof XMLDocument
					? $ownerDocument->setTagName($element->tagName)
					: $this->createElement($element->tagName, $element);
			
			if ($element->hasAttributes())
			{
				foreach($element->attributes as $attr)
				{
					$ownerDocument->setAttribute($attr->name, $attr->value);
				}
			}

			if ($element->hasChildNodes())
			{
				foreach($element->childNodes as $child)
				{
					$parsedChild = $this->parse($child, $ownerDocument);
					if ($parsedChild !== null)
					{
						$ownerDocument->appendChild($parsedChild);
					}
				}
			}

			return $ownerDocument;
		}
	}
}
