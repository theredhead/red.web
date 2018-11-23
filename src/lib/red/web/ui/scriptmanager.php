<?php

namespace red\web\ui
{
	class ScriptManager extends \red\Obj
	{
		/**
		 * minified jQuery from googleapis.com
		 */
		const CDN_URL_JQUERY		= 'https://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js';
		/**
		 * minified jQueryUI from googleapis.com 
		 */
		const CDN_URL_JQUERYUI		= 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js';
		/**
		 * minified SwfObject from googleapis.com 
		 */
		const CDN_URL_SWFOBJECT		= 'https://ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js';
		/**
		 * minified ext-core from googleapis.com 
		 */
		const CDN_URL_EXTCORE		= 'https://ajax.googleapis.com/ajax/libs/ext-core/3.1.0/ext-core.js';
		/**
		 * minified Dojo from googleapis.com 
		 */
		const CDN_URL_DOJO			= 'https://ajax.googleapis.com/ajax/libs/dojo/1.6.1/dojo/dojo.xd.js';
		/**
		 * minified mootools from googleapis.com 
		 */
		const CDN_URL_MOOTOOLS		= 'https://ajax.googleapis.com/ajax/libs/mootools/1.4.1/mootools-yui-compressed.js';
		/**
		 * minified prototype from googleapis.com 
		 */
		const CDN_URL_PROTOTYPE		= 'https://ajax.googleapis.com/ajax/libs/prototype/1.7.0.0/prototype.js' ;
		/**
		 * minified scriptaculous from googleapis.com 
		 */
		const CDN_URL_SCRIPTACULOUS	= 'https://ajax.googleapis.com/ajax/libs/scriptaculous/1.9.0/scriptaculous.js';
		
		/**
		 * holds the scripts (in literal form) meant to run on document load
		 * 
		 * @var array
		 */
		protected $startupScripts = array();
		
		/**
		 * holds references to external scripts to be injected in a webpage's
		 * head section
		 * 
		 * @var array
		 */
		protected $externalScripts = array();

		/**
		 * Register a piece of javascript to be run when the document is loaded.
		 * 
		 * @param string $scriptLiteral
		 * @param string $key
		 * @return string The key used to register the script
		 */
		public function registerStartupScript($scriptLiteral, $key=null)
		{
			if ($key === null)
			{
				$key = md5($scriptLiteral);
			}
			$this->startupScripts[$key] = $scriptLiteral;

			return $key;
		}
		
		/**
		 * Unregister a previously registered script
		 *
		 * @param string $key 
		 */
		public function unregisterStartupScript($key)
		{
			unset($this->startupScripts[$key]);
		}

		
		/**
		 * Register a linked javascript
		 * 
		 * @param string $pathToScriptFile
		 * @param string $key
		 * @return string The key used to register the script
		 */
		public function registerClientScript($pathToScriptFile, $key=null)
		{
			if ($key === null)
			{
				$key = md5($pathToScriptFile);
			}
			$this->externalScripts[$key] = $pathToScriptFile;
			return $key;
		}
		
		/**
		 * Unregister a previously registered script
		 *
		 * @param string $key 
		 */
		public function unregisterClientScript($key)
		{
			unset($this->externalScripts[$key]);
		}
				
		/**
		 * Inject managed scripts into given document
		 * 
		 * @param \red\web\ui\html\XHTMLDocument $document 
		 */
		public function injectScriptsIntoDocument(\red\web\ui\html\XHTMLDocument $document)
		{
			$inlineCount = count($this->startupScripts);
			$externalCount = count($this->externalScripts);

			if ($inlineCount + $externalCount > 0)
			{
				$head = $document->findFirst(
						function($o) 
						{
							return $o instanceof \red\xml\XMLElement && $o->getTagName() == 'head';
						});

				if ($head !== null)
				{
					foreach($this->externalScripts as $key => $pathToScriptFile)
					{
						$scriptTag = $document->createElement('script');
						$scriptTag->setAttribute('type', 'text/javascript');
						$scriptTag->setAttribute('src', $pathToScriptFile);
						$scriptTag->appendChild($document->createLiteral(''));

						$head->appendChild($scriptTag);
					}

					if ($inlineCount > 0)
					{
						$hasJquery = in_array(self::CDN_URL_JQUERY, $this->externalScripts);

						$scriptTag = $document->createElement('script');
						$scriptTag->setAttribute('type', 'text/javascript');

						if($hasJquery)
						{
							$scriptTag->appendChild($document->createLiteral(
									"jQuery(document).ready(function(){"));
						}
						$content = '';
						foreach($this->startupScripts as $key => $scriptLiteral)
						{
							$content .= sprintf("\n\t\t\t\t// %s", $key);
							$content .= sprintf("\n\t\t\t\t%s\n", $scriptLiteral);
						}

						$literal = $scriptTag->appendChild($document->createLiteral($content));
						
//						die(typeid($literal));
						
						if($hasJquery)
						{
							$scriptTag->appendChild($document->createLiteral(
									"}); // jQuery(document).ready\n"));
						}

						$head->appendChild($scriptTag);
					}
				}
			}
		}
	}
}

#EOF