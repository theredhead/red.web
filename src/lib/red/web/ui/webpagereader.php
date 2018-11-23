<?php

namespace red\web\ui
{
	use \red\MBString;
	use \red\xml\XMLReader;
	use \red\xml\XMLDocument;
	use \red\xml\XMLElement;
	use \red\web\ui\html\HtmlTag;
	
	
	require_once dirname(__FILE__) . '/html/elements.php';
	
	class WebPageReader extends XMLReader
	{
		/**
		 * Create an element suitable for the WebPage being read based on $originalElement
		 *
		 * @param string $tagName
		 * @param \DOMElement $originalElement;
		 */
		protected function createElement($tagName, \DOMElement $originalElement)
		{
			$result = null;
			
			if (\mb_strpos($tagName, ':') > 0)
			{
				list($prefix, $className) = explode(':', $tagName);
				$namespace = str_replace('.', NAMESPACE_SEPARATOR, $originalElement->lookupNamespaceUri($prefix));
				$fullyQualifiedClassName = $namespace . NAMESPACE_SEPARATOR . $className;
				
				$result = new $fullyQualifiedClassName();
			}
			
			if ($result == null)
			{
				$result = new HtmlTag($tagName);
			}

			return $result;
		}

		/**
		 * Read a WebPage from the template in $xmlString
		 *
		 * @param string $tagName
		 * @param \DOMElement $originalElement;
		 * @return WebPage
		 */
		public function read($xmlString, XMLElement $ownerDocument=null)
		{
			if ($xmlString == '')
			{
				static::fail('xmlString cannot be empty.');
			}
			libxml_use_internal_errors(true);

			$ownerDocument instanceof XMLDocument or $ownerDocument = new WebPage();
			
			$doc = new \DOMDocument('1.0', MBString::ENCODING_UTF8);
			$doc->loadXML($xmlString);
			
			if ($doc->documentElement == null)
			{
				static::fail('There is no root element: <pre>%s</pre>', htmlentities($xmlString));
			}

			$root = $this->parseElement($doc->documentElement, $ownerDocument, $ownerDocument);
			
			return $ownerDocument;
		}
	}
}