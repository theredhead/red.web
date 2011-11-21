<?php

namespace red\xml
{
	use red\MBString;
	
	class XMLDocument extends XMLElement
	{	
		/**
		 * The encoding used for operations in this document
		 *
		 * @var string
		 */
		protected $encoding = MBString::ENCODING_UTF8;

		/**
		 * The DOCTYPE for this document
		 *
		 * @var MBString
		 */
		protected $documentType = null;

		/**
		 * Get the DOCTYPE for this document.
		 *
		 * @return MBString
		 */
		public function getDocumentType()
		{
			return $this->documentType;
		}
		
		/**
		 * Set the DOCTYPE for this document.
		 *
		 * @return MBString
		 */
		protected function setDocumentType(MBString $docType)
		{
			$this->documentType = $docType;
		}
		
		/**
		 * Get the name of the encoding used in this document
		 *
		 * @return string
		 */
		public function getEncoding()
		{
			return $this->encoding;
		}
		
		/**
		 * Create a new element with a specified $tagName, suitable for use in this document.
		 *
		 * @return XMLElement
		 */
		public function createElement($tagName)
		{
			return new XMLElement($tagName);
		}
		
		/**
		 * Create a textnode suitable for use in this document
		 *
		 * @param string $textContent 
		 * @return XMLText
		 */
		public function createText($textContent)
		{
			return new XMLText($textContent);
		}

		/**
		 * Create an XMLCDataSection with content
		 *
		 * @param $textContent
		 * @return XMLCDataSection
		 */
		public function createCDataSection($textContent)
		{
			return new XMLCDataSection($textContent);
		}

		/**
		 * Create a literal node suitable for use in this document
		 *
		 * @param string $literalContent
		 * @return XMLLiteral 
		 */
		public function createLiteral($literalContent)
		{
			return new XMLLiteral($literalContent);
		}
		
		/**
		 * Create a comment node suitable for use in this document
		 *
		 * @param string $textContent 
		 * @return XMLComment
		 */
		public function createComment($textContent)
		{
			return new XMLComment($textContent);
		}
	}
}