<?php

namespace red\web\ui
{
	class CssManager extends \red\Obj
	{
		/**
		 * holds the css meant to be inserted in the head section as inline sheets
		 * 
		 * @var array
		 */
		protected $inlineStyles = array();
		
		/**
		 * holds references to stylesheets to be inserted as link elements into 
		 * the head section
		 *
		 * @var array
		 */
		protected $linkedStyles = array();

		/**
		 * Register a piece of sheet to be run when the document is loaded.
		 * 
		 * @param string $cssLiteral
		 * @param string $key
		 * @return string The key used to register the sheet
		 */
		public function registerInlineStyle($cssLiteral, $key=null)
		{
			if ($key === null)
			{
				$key = md5($cssLiteral);
			}
			$this->inlineStyles[$key] = $cssLiteral;
			return $key;
		}
		
		/**
		 * Unregister a previously registered sheet
		 *
		 * @param string $key 
		 */
		public function unregisterInlineStyle($key)
		{
			unset($this->inlineStyles[$key]);
		}

		
		/**
		 * Register a linked sheet
		 * 
		 * @param string $pathToScriptFile
		 * @param string $key
		 * @return string The key used to register the sheet
		 */
		public function registerStylesheet($pathToScriptFile, $key=null)
		{
			if ($key === null)
			{
				$key = md5($pathToScriptFile);
			}
			$this->linkedStyles[$key] = $pathToScriptFile;
			return $key;
		}
		
		/**
		 * Unregister a previously registered sheet
		 *
		 * @param string $key 
		 */
		public function unregisterStylesheet($key)
		{
			unset($this->linkedStyles[$key]);
		}
		
		/**
		 * Inject managed sheets into given document
		 * 
		 * @param \red\web\ui\html\XHTMLDocument $document 
		 */
		public function injectStyleIntoDocument(\red\web\ui\html\XHTMLDocument $document)
		{
			$inlineCount = count($this->inlineStyles);
			$externalCount = count($this->linkedStyles);

			if ($inlineCount + $externalCount > 0)
			{
				$head = $document->findFirst(
						function($o) 
						{
							return $o instanceof \red\xml\XMLElement && $o->getTagName() == 'head';
						});

				if ($head !== null)
				{
					foreach($this->linkedStyles as $key => $pathToCssFile)
					{
						$sheetTag = $document->createElement('link');
						$sheetTag->setAttribute('rel', 'stylesheet');
						$sheetTag->setAttribute('type', 'text/css');
						$sheetTag->setAttribute('href', $pathToCssFile);

						$head->appendChild($sheetTag);
					}

					if ($inlineCount > 0)
					{
						$sheetTag = $document->createElement('style');
						$sheetTag->setAttribute('type', 'text/css');

						$content = '';
						foreach($this->inlineStyles as $key => $sheetLiteral)
						{
							$content .= sprintf("%s\n", $sheetLiteral);
						}

						$sheetTag->appendChild($document->createText($content));
						$head->appendChild($sheetTag);
					}
				}
			}
		}
	}
}

#EOF