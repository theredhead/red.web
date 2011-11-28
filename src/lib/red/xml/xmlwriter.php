<?php
namespace red\xml
{
	use red\MBString;
	use red\MBStringBuilder;
	
	class XMLWriter extends MBStringBuilder
	{
		// <editor-fold defaultstate="collapsed" desc="Multybyte sequenses with special meanings">
		/**
		 * @var MBString '<'
		 */
		protected $tagLeftChar;
		/**
		 * @var MBString '>'
		 */
		protected $tagRightChar;
		/**
		 * @var MBString '/'
		 */
		protected $tagEndChar;
		/**
		 * @var MBString '\n'
		 */
		protected $newlineChar;
		/**
		 * @var MBString ' '
		 */
		protected $spaceChar; 
		/**
		 * @var MBString
		 */
		protected $tabChar;
		/**
		 * @var MBString '\t'
		 */
		protected $equalsChar;
		/**
		 * @var MBString '='
		 */
		protected $quoteChar;
		/**
		 * @var MBString '"'
		 */
		protected $commentStart;
		/**
		 * @var MBString '<!-- '
		 */
		protected $commentEnd;
		/**
		 * @var MBString ' -->'
		 */
		protected $cdataStart;
		/**
		 * @var MBString '<![CDATA[ '
		 */
		protected $cdataEnd;
		/**
		 * @var MBString ' ]]>'
		 */
		protected $piStart;
		/**
		 * @var MBString '?>'
		 */
		protected $piEnd;
		
		/**
		 * @var integer
		 */
		protected $indent = 0;
		// </editor-fold>
		
		// <editor-fold defaultstate="collapsed" desc="Property boolean SkipXmlDeclaration">
		private $skipXmlDeclaration = false;

		/**
		 * @return boolean
		 */
		public function getSkipXmlDeclaration()
		{
			return $this->skipXmlDeclaration;
		}

		/**
		 * @param boolean $newSkipXmlDeclaration
		 */
		public function setSkipXmlDeclaration($newSkipXmlDeclaration)
		{
			$this->skipXmlDeclaration = $newSkipXmlDeclaration == true;
		}
		// </editor-fold>
		// <editor-fold defaultstate="collapsed" desc="Property boolean SkipWhitespaceNodes">
		private $skipWhitespaceNodes = false;

		/**
		 * @return boolean
		 */
		public function getSkipWhitespaceNodes()
		{
			return $this->skipWhitespaceNodes;
		}

		/**
		 * @param boolean $newSkipWhitespaceNodes
		 */
		public function setSkipWhitespaceNodes($newSkipWhitespaceNodes)
		{
			$this->skipWhitespaceNodes = $newSkipWhitespaceNodes;
		}

		// </editor-fold>

		public function __construct($encoding=MBString::ENCODING_UTF8)
		{
			parent::__construct();
			$this->tagLeftChar  = MBString::withString('<', $encoding);
			$this->tagRightChar = MBString::withString('>', $encoding);
			$this->tagCloseChar = MBString::withString('/', $encoding);
			$this->newlineChar  = MBString::withString("\n", $encoding);
			$this->equalsChar	= MBString::withString('=', $encoding);
			$this->spaceChar	= MBString::withString(' ', $encoding);
			$this->tabChar		= MBString::withString("\t", $encoding);
			$this->quoteChar	= MBString::withString('"', $encoding);

			$this->commentStart	= MBString::withString('<!-- ', $encoding);
			$this->commentEnd	= MBString::withString(' -->', $encoding);
			$this->cdataStart	= MBString::withString('<![CDATA[ ', $encoding);
			$this->cdataEnd		= MBString::withString(' ]]>', $encoding);
			$this->piStart		= MBString::withString('<?', $encoding);
			$this->piEnd		= MBString::withString('?>', $encoding);
		}
		
		/**
		 * Determine if the content of an element is on node long and that node 
		 * is of the type XMLText
		 *
		 * @param XMLElement $element
		 * @return boolean
		 */
		protected function elementContainsExactlyOneTextNode(XMLElement $element)
		{
			$result = false;
			if ($element->hasChildren() && $element->getChildNodes()->count() == 1)
			{
				$result = typeid($element->getFirstChild()) == 'red.xml.XMLText';
			}
			return $result;
		}

		/**
		 * Write an XMLElement, its attributes and children to the output buffer
		 * 
		 * @param XMLElement $element 
		 */
		protected function writeElement(XMLElement $element)
		{
			if ($element->isVisible())
			{
				if ($this->elementContainsExactlyOneTextNode($element))
				{
					$this->writeOpenTag($element);
//					$firstChild = $element->getFirstChild();
//					// this sucks...
//					if ($firstChild instanceof XMLLiteral)
//					{
//						$this->writeLiteral($firstChild);
//					}
//					else if ($firstChild instanceof XMLText)
//					{
//						$this->writeText($firstChild);					
//					}
					$this->writeElementContent($element);
					$this->writeCloseTag($element);
	// 				$this->append($this->newlineChar);
				}
				else if ($element->hasChildren())
				{
					$this->writeOpenTag($element);
					$this->append($this->newlineChar);
					$this->writeElementContent($element);
					$this->indent();
					$this->writeCloseTag($element);
	//				$this->append($this->newlineChar);
				}
				else
				{
					$this->append($this->tagLeftChar);
					$this->append($element->getTagName());

					foreach($element->getAttributes() as $attribute)
					{
						$this->writeAttribute($attribute->getName(), $attribute->getValue());
					}

					$this->append($this->tagCloseChar);
					$this->append($this->tagRightChar);
	//				$this->append($this->newlineChar);
				}
			}
		}

		/**
		 * Write an attribute and its value to the output buffer
		 * 
		 * @param type $name
		 * @param type $value 
		 */
		protected function writeAttribute($name, $value)
		{
			$this->append($this->spaceChar);
			$this->append($name->getHtmlEntities());
			$this->append($this->equalsChar);
			$this->append($this->quoteChar);
			$this->append($value->getHtmlEntities());
			$this->append($this->quoteChar);
		}
		
		/**
		 * Write a literal node to the output buffer
		 * 
		 * @param XMLLiteral $literal 
		 */
		protected function writeLiteral(XMLLiteral $literal)
		{
			$this->append($literal->getTextContent());
		}
		
		/**
		 * Write a text node to the output buffer
		 * 
		 * @param XMLText $text 
		 */
		protected function writeText(XMLText $text)
		{
			if ($this->getSkipWhitespaceNodes() && $text->isWhitespace())
			{
			}
			else
			{
				$this->append($text->getTextContent()->getHtmlEntities());
			}
		}

		/**
		 * write a comment to the output buffer
		 * 
		 * @param XMLComment $comment 
		 */
		protected function writeComment(XMLComment $comment)
		{
			$this->append($this->commentStart);
			$this->append($comment->getTextContent()->getHtmlEntities());
			$this->append($this->commentEnd);
		}
		
		/**
		 * write a cdata section to the output buffer
		 *
		 * @param XMLCDataSection $cdata 
		 */
		protected function writeCDataSection(XMLCDataSection $cdata)
		{
			$this->append($this->cdataStart);
			$this->append($cdata->getTextContent());
			$this->append($this->cdataEnd);
		}
		
		/**
		 * get the whitespace prefix for the current indentation level
		 * 
		 * @return type 
		 */
		protected function indent()
		{
			return;

			for($i = 0; $i < $this->indent; $i ++)
			{
				$this->append($this->tabChar);
			}
		}

		/**
		 * write an open tag and its attributes, if any. this does not include
		 * self-closing tags.
		 * 
		 * @param XMLElement $element 
		 */
		protected function writeOpenTag(XMLElement $element)
		{
			$this->append($this->tagLeftChar);
			$this->append($element->getTagName());
			
			foreach($element->getAttributes() as $attribute)
			{
				$this->writeAttribute($attribute->getName(), $attribute->getValue());
			}

			$this->append($this->tagRightChar);
		}
		
		/**
		 * write a close tag
		 *
		 * @param XMLElement $element 
		 */
		protected function writeCloseTag(XMLElement $element)
		{
			$this->append($this->tagLeftChar);
			$this->append($this->tagCloseChar);
			$this->append($element->getTagName());
			$this->append($this->tagRightChar);
		}
		
		/**
		 * write out the contents of an xml element
		 * 
		 * @param XMLElement $element 
		 */
		protected function writeElementContent(XMLElement $element)
		{
			$this->indent ++;
			foreach($element->getChildNodes() as $child)
			{
				$this->writeNode($child);
			}
			$this->indent --;
		}

		/**
		 * Write out any supported type of node.
		 * 
		 * @param XMLNode $node
		 * @return void
		 */
		protected function writeNode(XMLNode $node)
		{
			$this->indent();
			$node->normalize($this);

			if ($node instanceof XMLElement)
			{
				if ($node->isVisible())
				{
					$this->writeElement($node);
				}
			}
			else if ($node instanceof XMLCDataSection)
			{
				$this->writeCDataSection($node);
			}
			else if ($node instanceof XMLLiteral)
			{
				$this->writeLiteral($node);
			}
			else if ($node instanceof XMLText)
			{
				$this->writeText($node);
			}
			else if ($node instanceof XMLComment)
			{
				$this->writeComment($node);
			}
			else if ($node instanceof XMLCDataSection)
			{
				$this->writeCDataSection($node);
			}
			else
			{
				static::fail('Unsupported node type encountered: "%s"', typeid($node));
			}

		}

		/**
		 * write the XML declarations (processing instruction '<?xml version="1.0" ...)
		 *
		 * @param XMLDocument $document 
		 */
		protected function writeXmlDeclaration(XMLDocument $document)
		{
			$this->append($this->piStart);
			$this->append(MBString::withString('xml version="1.0" encoding="%s"')->format($document->getEncoding()));
			$this->append($this->piEnd);
			$this->append($this->newlineChar);
		}
		
		/**
		 * write the doctype of a document to the buffer
		 * 
		 * @param XMLDocument $document 
		 */
		protected function writeDoctype(XMLDocument $document)
		{
			if ($document->getDocumentType() != null)
			{
				$this->append($document->getDocumentType());
				$this->append($this->newlineChar);
			}
		}
		
		/**
		 * Write an xmlelement (or document) and return the completed buffer
		 * 
		 * @param XMLElement $root
		 * @return type 
		 */
		public function write(XMLNode $root)
		{
			$this->clear();
			$root->normalize($this);
			if ($root instanceof XMLDocument)
			{
				if (!$this->getSkipXmlDeclaration())
				{
					$this->writeXmlDeclaration($root);
				}
				$this->writeDoctype($root);
			}

			$this->writeNode($root);

			return $this->getString();
		}
	}
}