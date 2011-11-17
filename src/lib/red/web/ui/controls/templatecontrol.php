<?php

namespace red\web\ui\controls
{
	require_once 'red/web/ui/html/elements.php';

	use red\xml\XMLReader;
	use \red\web\ui\html\HtmlTag;
	
	class TemplateControlReader extends XMLReader
	{
		/**
		 * Create an XMLlement from the $originalEment
		 *
		 * @param type $tagName
		 * @param \DOMElement $originalElement
		 * @return HtmlTag 
		 */
		protected function createElement($tagName, \DOMElement $originalElement)
		{
			$result = null;
			
			if (mb_strpos($tagName, ':') > 0)
			{
				list($prefix, $className) = explode(':', $tagName);
				$namespace = $originalElement->lookupNamespaceUri($prefix);
				$fullyQualifiedClassName = $namespace . NAMESPACE_SEPARATOR . $className;
				
				$result = new $fullyQualifiedClassName();
			}
			
			if ($result == null)
			{
				$result = new HtmlTag($tagName);
			}

			return $result;
		}
	}

	abstract class TemplateControl extends BaseControl
	{
		/**
		 * This herte is final in order to guarantee
		 * that all subclasses can be created without
		 * any arguments to the constructor.
		 */
		final public function __construct()
		{
			parent::__construct();
			$this->loadTemplate();
		}
		
		/**
		 * Keeps the childcontrols linked and findable, even when there is no property declared.
		 *
		 * @var array
		 */
		protected $childControls = array();
		
		/**
		 * Gets called after a template has been loaded and all childControls have
		 * been properly instantiated.
		 *
		 * @return void
		 */
		protected function templateLoaded()
		{
			$childControls = $this->findAll(function($obj){return $obj instanceof IControl && $obj->hasAttribute('id');});
			$reflector = $this->getReflector();
			foreach($childControls as $control)
			{
				$id = ''.$control->getAttribute('id');
				if ($reflector->hasProperty($id))
				{
					$this->$id = $control;
				}
				$this->childControls[$id] = $control;
			}
		}
		
		/**
		 * Get the path to the template file for a specific language.
		 * 
		 * @param string $language 
		 */
		protected function resolveTemplateInLanguage($language)
		{
			$dir = dirname($this->getReflector()->getFileName());

			$fileName	= strtolower($this->getReflector()->getShortName())
						. (strlen($language) > 0
								? '.' . $language . '.xml'
								: '.xml');
			
			return $dir . DIRECTORY_SEPARATOR . $fileName ;
		}
		
		/**
		 * Load this controls template.
		 *
		 * @param string $templatePath 
		 */
		protected function loadTemplate()
		{
			$languages = language();
			array_push($languages, null);

			$tried = array();
			foreach($languages as $language)
			{
				$template =	$this->resolveTemplateInLanguage($language);
				if (file_exists($template))
				{
					break;
				}
				array_push($tried, !empty($language) ? $language : '<default>');
			}
			if (!file_exists($template))
			{
				static::fail('Template file for "%s" not found in languages: %s', 
						get_called_class(), implode(', ', $tried));
			}

			$this->clear();
			$template = file_get_contents($template);
			$reader = new TemplateControlReader();
			$reader->read($template, $this);
			
			$this->templateLoaded();
		}

		/**
		 * Get a copy of this control
		 * 
		 * @return static 
		 */
		public function copy()
		{
			$copy = new static();
			
			trace('copy of %s results in %s', typeid($this), typeid($copy));
			
			foreach($this->attributes as $attribute)
			{
				$copy->setAttribute($attribute->getName(), $attribute->getValue());
			}

			// if the constructor causes elements to be created, they'd be 
			// duplicated by the next loop.
			return $copy;
		}
	}
}