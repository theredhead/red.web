<?php

namespace red\web\ui
{
	use red\MBString;
	use red\web\ui\html\XHTMLDocument;
	use red\web\ui\html\XHTMLElement;
	use red\web\ui\html\HtmlHead;
	use red\web\ui\html\HtmlBody;
	use red\web\ui\html\HtmlTitle;
	use red\web\ui\WebPageReader;

	require_once dirname(__FILE__) . '/html/elements.php';
	
	abstract class WebPage extends XHTMLDocument
	{
		const LIFECYCLE_STATE_NONE = 0;
		const LIFECYCLE_STATE_INIT = 1;
		const LIFECYCLE_STATE_POSTBACK = 2;
		const LIFECYCLE_STATE_LOAD = 3;
		const LIFECYCLE_STATE_RENDER = 4;

		/**
		 * @var \red\web\http\HttpRequest
		 */
		protected $currentRequest;

		/**
		 * @return \red\web\http\HttpRequest
		 */
		protected function getCurrentRequest()
		{
			return $this->currentRequest;
		}

		/**
		 * @var \red\web\http\HttpResponse
		 */
		protected $currentResponse;

		/**
		 * @return \red\web\http\HttpResponse
		 */
		protected function getCurrentResponse()
		{
			return $this->currentResponse;
		}



		private $currentLifecycleState = self::LIFECYCLE_STATE_NONE;
		protected function currentLifecycleState()
		{
			return $this->currentLifecycleState;
		}
		
		/**
		 * Represents the event where the page was initialized 
		 * and all controls are created. At this point state from 
		 * postback is not available yet. (so you can still 
		 * programmatically add controls to the tree.)
		 */
		const EV_PAGE_INIT = 'PageInit';

		/**
		 * Represents the event where the page is "completed".
		 * Meaning all controls are present and state (if any)
		 * is restored from postback.
		 */
		const EV_PAGE_LOAD = 'PageLoad';

		/**
		 * @var \red\web\http\HttpApplication
		 */
		private $application;

		/**
		 * @return \red\web\http\HttpApplication
		 */
		public function getApplication()
		{
			return $this->application;
		}

		/**
		 * @param \red\web\http\HttpApplication $application
		 */
		protected function setApplication($application)
		{
			$this->application = $application;
		}

		/**
		 * Keeps the childcontrols linked and findable, even when there is no property declared.
		 * (used for auto event wiring)
		 *
		 * @var array
		 */
		protected $childControls = array();

		/**
		 * Get the controls that were attached to this page while the template was loading.
		 * 
		 */
		public function getChildControls()
		{
			return $this->childControls;
		}
		
		
		// <editor-fold defaultstate="collapsed" desc="Important elements">
		protected $body;		
		/**
		 * @return HtmlHead
		 */
		public function getHeadElement()
		{
			if ($this->head == null)
			{
				$this->head = $this->findFirst(function($o) {return $o instanceof \red\xml\XMLElement and $o->getTagName() == 'head';});
			}
			return $this->head;
		}
		protected $head;

		/**
		 * @return HtmlBody
		 */
		public function getBodyElement()
		{
			if ($this->body == null)
			{
				$this->body = $this->findFirst(function($o) {return $o instanceof \red\xml\XMLElement and $o->getTagName() == 'body';});
			}
			return $this->body;
		}
		// </editor-fold>
		// <editor-fold defaultstate="collapsed" desc="Title (string as opposed to element)">
		/**
		 * @return MBString
		 */
		public function getTitle()
		{
			return $this->head->getTitleElement()->getText();
		}
		public function setTitle(MBString $title)
		{
			$this->head->getTitleElement()->setText($title);
		}
		// </editor-fold>

		/**
		 * Register external theme resources in the current theme for a key. a key will usually be
		 * a typeId (fully qualified class name with periods instead the usual NAMESPACE_SEPARATOR
		 * 
		 * @param $typeId
		 * @return void
		 */
		protected function registerThemeResources($typeId)
		{
			$reflector = new \ReflectionClass(str_replace('.', NAMESPACE_SEPARATOR, $typeId));
			if ($reflector->implementsInterface('\\red\\web\\ui\\IThemable'))
			{
				$method = $reflector->getMethod('getThemeResourceTypes');
				if ($method->getDeclaringClass()->getName() == $reflector->getName())
				{
					$resourceTypes = $method->invoke(null);
					foreach($resourceTypes as $resourceType)
					{
						$this->registerThemeResource($typeId, $resourceType);
					}
				}

				$this->registerThemeResources(str_replace(NAMESPACE_SEPARATOR, '.', $reflector->getParentClass()->getName()));
			}
			else
			{
				// @todo: decide whether to crash and burn or try adding just css or...
			}
		}

		/**
		 * Holds a register of threme resource types with typeIds that want them registered.
		 *
		 * @var array
		 */
		private $themeResources = array();

		/**
		 * @param $typeId
		 * @param $resourceType
		 * @return void
		 */
		protected function themeResourceExists($typeId, $resourceType)
		{
			return null !== $this->getApplication()->getThemeResourcePath($typeId . '.' . $resourceType);
		}

		/**
		 * @param $typeId
		 * @param $resourceType
		 * @return void
		 */
		protected function registerThemeResource($typeId, $resourceType)
		{
//			$resourceFullName = $typeId . '.' . $resourceType;
//			$theme = $this->getApplication()->getTheme();
//
//			switch(strtolower($resourceType))
//			{
//				case 'css' :
//					$this->registerStylesheet(
//							 '/!theme-css/'.$resourceFullName . '?theme=' . $theme
//							,$resourceFullName);
//					break;
//				case 'js' :
//					$this->registerClientScript(
//							 '/!theme-js/'.$resourceFullName . '?theme=' . $theme
//							,$resourceFullName);
//					break;
//			}

			if (!isset($this->themeResources[$resourceType]))
			{
				$this->themeResources[$resourceType] = array();
			}
			array_push($this->themeResources[$resourceType], $typeId);
		}

		// <editor-fold defaultstate="collapsed" desc="ClientScript management">
		/**
		 * @var ScriptManager
		 */
		protected $scriptManager;
		
		/**
		 * Register a piece of javascript to be run when the document is loaded.
		 * 
		 * @param string $scriptLiteral
		 * @param string $key
		 * @return string The key used to register the script
		 */
		public function registerStartupScript($scriptLiteral, $key=null)
		{
			return $this->scriptManager->registerStartupScript($scriptLiteral, $key);
		}
		
		/**
		 * Unregister a previously registered script
		 *
		 * @param string $key 
		 */
		public function unregisterStartupScript($key)
		{
			return $this->scriptManager->unregisterStartupScript($key);
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
			return $this->scriptManager->registerClientScript($pathToScriptFile, $key);
		}
		
		/**
		 * Unregister a previously registered script
		 *
		 * @param string $key 
		 */
		public function unregisterClientScript($key)
		{
			return $this->scriptManager->unregisterClientScript($key);
		}

		/**
		 * Ask the ScriptManager to inject all registered entities into this document.
		 */
		protected function injectManagedScripts()
		{
			$this->scriptManager->injectScriptsIntoDocument($this);
		}
		// </editor-fold>
		// <editor-fold defaultstate="collapsed" desc="ClientScript management">

		/**
		 * @var CssManager
		 */
		protected $cssManager;
		
		/**
		 * Register a piece of sheet to be run when the document is loaded.
		 * 
		 * @param string $cssLiteral
		 * @param string $key
		 * @return string The key used to register the sheet
		 */
		public function registerInlineStyle($cssLiteral, $key=null)
		{
			return $this->cssManager->registerInlineStyle($cssLiteral, $key);
		}
		
		/**
		 * Unregister a previously registered sheet
		 *
		 * @param string $key 
		 */
		public function unregisterInlineStyle($key)
		{
			return $this->cssManager->unregisterInlineStyle($key);
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
			return $this->cssManager->registerStylesheet($pathToScriptFile, $key);
		}
		
		/**
		 * Unregister a previously registered sheet
		 *
		 * @param string $key 
		 */
		public function unregisterStylesheet($key)
		{
			return $this->cssManager->unregisterStylesheet($key);
		}

		
		protected function injectManagedCss()
		{
			return $this->cssManager->injectStyleIntoDocument($this);	
		}
		// </editor-fold>
		
		protected function isTraceEnabled()
		{
			return isset($_REQUEST['trace']);
		}
		
		/**
		 * Clear this page.
		 * 
		 * Returns this instance to an empty state. also decouples childControls
		 */
		public function clear()
		{
			parent::clear();
			$this->head = null;
			$this->body = null;
			$this->childControls = array();
		}
		
		/**
		 * Collect all controls on the page. including the controls that are 
		 * nested inside other controls. If you only need the controls as they
		 * were attached to the page, use ->getChildControls() instead.
		 *
		 * @return XMLNodeList with only BaseControl subclasses
		 */
		protected function getAllControls()
		{
			$controls =  $this->findAll(function($e) {
				return $e instanceof \red\web\ui\controls\IControl;
			});
			return $controls;
		}

		// <editor-fold defaultstate="collapsed" desc="Managing statefulness">
		/**
		 * Gets all elements inside the control tree that implement IStateful
		 * 
		 * @return \red\xml\XMLNodeList
		 */
		protected function getAllStatefulElements() 
		{
			$elements =  $this->findAll(function($e) {
				return $e instanceof \red\web\ui\controls\IStateful;
			});
			$result = array();
			foreach($elements as $element)
			{
				$result[$element->getPath()] = $element;
			}
			return $result;
		}
		
		/**
		 * Get the field name used to post state information
		 * 
		 * @return string
		 */
		private function stateFieldName()
		{
			return str_replace('.', '_', typeid($this)).'_state';
		}
		
		/**
		 * Adds the state to all forms in this page
		 */
		private function addStateFieldToForms()
		{
			$state = array();
			foreach($this->getAllStatefulElements() as $ix => $control)
			{
				$state[$ix] = $control->getState()->toArray();
			}
			$serializedState = base64_encode(serialize($state));

			$forms = $this->getElementsByTagName('form');
			foreach($forms as $form)
			{
				// only add state to post requests
				if ($form->getAttribute('method') == 'post')
				{
					$input = $form->appendChild($this->createElement('input'));
					$input->setAttribute('type', 'hidden');
					$input->setAttribute('name', $this->stateFieldName());
					$input->setAttribute('value', $serializedState);
				}
			}
		}
		
		/**
		 * Restores state after a postback.
		 * 
		 * @param \red\web\http\HttpRequest $request 
		 */
		private function restoreStateToControlsAfterPostback(\red\web\http\HttpRequest $request)
		{
			$serializedState = $request->getFormField($this->stateFieldName(), null);

			if ($serializedState !== null)
			{
				$unserializedState = unserialize(base64_decode($serializedState));
//				var_dump(array_keys($unserializedState));					
				foreach($this->getAllStatefulElements() as $ix => $control)
				{
					if (isset($unserializedState[$ix]))
					{
						$state = $unserializedState[$ix];
						$control->setState(new controls\PropertyBag($state));
					}
					else
					{
//						static::fail('The control tree changed between request and postback. (%s)', $ix);
					}
				}	
			}
		}
		// </editor-fold>
		
		/**
		 * Gets ecxecuted after EV_PAGE_INIT if a postback occured
		 *
		 * @param \red\web\http\HttpRequest $request 
		 */
		protected function notePostback(\red\web\http\HttpRequest $request)
		{
			$this->restoreStateToControlsAfterPostback($request);
			foreach($this->getAllControls() as $control)
			{
				$control->notePostback($request);
			}
		}
		

		/**
		 * Get a child control by id (the id attribute from the template)
		 *
		 * @param string $controlId 
		 * @return IControl
		 */
		public function getChildControl($controlId)
		{
			return isset($this->childControls[$controlId])
				? $this->childControls[$controlId]
				: null;
		}
		
		/**
		 * triggers the WebPage::EV_PAGE_INIT event 
		 */
		protected function init(\red\web\http\HttpRequest $request, \red\web\http\HttpResponse $response)
		{
			// @todo: move this logic to the template loading inside WebPageReader
			$reflector = new \ReflectionObject($this);
			foreach($this->getAllControls() as $control)
			{
				if ($control->hasAttribute('id'))
				{
					$id = ''.$control->getAttribute('id');
					if ($reflector->hasProperty($id))
					{
						$property = $reflector->getProperty($id);
						if ($property instanceof \ReflectionProperty)
						{
							$property->setAccessible(true);
							$property->setValue($this, $control);
						}
					}
					$this->childControls[$id] = $control;
				}
			}
			$this->notifyListenersOfEvent(self::EV_PAGE_INIT, new \red\EventArgument());
		}

		/**
		 * triggers the WebPage::EV_PAGE_LOAD event 
		 */
		protected function load(\red\web\http\HttpRequest $request, \red\web\http\HttpResponse $response)
		{
			$this->notifyListenersOfEvent(self::EV_PAGE_LOAD, new \red\	EventArgument());
		}

		/**
		 * Process the current request;
		 */
		final public function processRequest(\red\web\http\HttpRequest $request, \red\web\http\HttpResponse $response)
		{
			$this->currentRequest = $request;
			$this->currentResponse = $response;

			$this->log('Entering INIT state') . PHP_EOL;
			$this->currentLifecycleState = self::LIFECYCLE_STATE_INIT;
			$this->init($request, $response);
			if ($request->isPostback())
			{
				$this->log('Entering POSTBAKC state');
				$this->currentLifecycleState = self::LIFECYCLE_STATE_POSTBACK;
				$this->notePostback($request);
			}
			$this->log('Entering LOAD state');
			$this->currentLifecycleState = self::LIFECYCLE_STATE_LOAD;
			$this->load($request, $response);

			$this->log('Entering RENDER state');
			$this->currentLifecycleState = self::LIFECYCLE_STATE_RENDER;
			$this->preRender();

			$this->addStateFieldToForms();
			$this->injectManagedScripts();
			$this->injectManagedCss();
			
			$writer = new \red\xml\XMLWriter();
			$writer->setSkipXmlDeclaration(true);
			$writer->write($this);

			$response->setHeader('Content-type', sprintf('text/html; charset=%s', $this->getEncoding()));
			$response->write($writer->getString());
		}

		/**
		 * Log a message
		 * 
		 * All arguments are passed through sprintf before the final message is 
		 * logged to the javascript console (window.console.log)
		 * 
		 * @param mixed $msg 
		 */
		public function log($msg)
		{
			$msg = func_num_args() == 1
				? (string)$msg
				: call_user_func_array('sprintf', func_get_args());

			$this->registerStartupScript(sprintf('if(typeof console != "undefined"){console.log("%s");};', addslashes($msg)));
			
			if($this->isTraceEnabled())
			{
				trace($msg);
			}
		}

		/**
		 * Alert a message
		 * 
		 * All arguments are passed through sprintf before the final message is 
		 * logged to javascript alert (window.alert)
		 * 
		 * @param mixed $msg 
		 */
		public function alert($msg)
		{
			$msg = func_num_args() == 1
				? (string)$msg
				: call_user_func_array('sprintf', func_get_args());

			$this->registerStartupScript(sprintf('alert("%s");', htmlspecialchars($msg)));
		}
		
		public function __construct(\red\web\http\HttpApplication $application)
		{
			parent::__construct(MBString::withString('html'));

			$this->setApplication($application);

			$this->setDocumentType(MBString::withString('<!DOCTYPE html>'));
			$this->head = $this->appendChild(new HtmlHead());
			$this->body = $this->appendChild(new HtmlBody());
			$this->setTitle(MBString::withString('Unnamed page'));
			
			$this->scriptManager = new \red\web\ui\ScriptManager();
			$this->cssManager = new \red\web\ui\CssManager();
		}
		
		/**
		 * Gets called when the template finished loading to give the instance 
		 * a chance to normalize itselg before events are fired.
		 * 
		 * @return type 
		 */
		protected function templateLoaded()
		{
			// this is not the same thing $this->getAllControls() does, 
			// nor should do. getAllControls() should not be interested in 
			// id attributes
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
			$fileNameParts = array(basename($this->getReflector()->getFileName(), '.php'), $language ,'xml');
			$fileName = implode('.', array_filter($fileNameParts, function($val) {return $val !== null;}));
			return $dir . DIRECTORY_SEPARATOR . $fileName;
		}

		/**
		 * Create an instance of the control this was called on, pre loading it from its associated
		 * template.
		 * 
		 * This method accepts a variable number of languages in the order preferred, 
		 * or an array in the first position containing the language codes in order
		 * of preference.
		 * 
		 * @param array|va_arg[string] $languagePref
		 * @return TemplateControl
		 */
		static public function withTemplate()
		{
			$reflector = new \ReflectionClass(get_called_class());
			$instance = $reflector->newInstance();
			$instance->loadTemplate();
			return $instance;
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
			foreach($languages as $language)
			{
				$template =	$this->resolveTemplateInLanguage($language);
				if (file_exists($template))
				{
					break;
				}
			}
			if (!file_exists($template))
			{
				static::fail('Template file for "%s" not found in languages: %s', get_called_class(), implode('', language()));
			}

			$this->clear();
			$template = file_get_contents($template);
			$reader = new WebPageReader();
			$reader->read($template, $this);
			
			$this->templateLoaded();
		}

		/**
		 * Attempt to bind as many events as possible to their respective handlers.
		 *
		 * @return void
		 */
		protected function autoWireEvents()
		{
			$controls = $this->findAll(function($e){return $e instanceof \red\web\ui\controls\IPublishEvents;});
			foreach($controls as $control)
			{
				$handlerMethodName = null;
				foreach($control->getPublishedEvents() as $eventName)
				{
					if ($this->hasDefaultEventHandlerForNamedEvent($control, $eventName, $handlerMethodName))
					{
						$control->registerEventListener($eventName, $handlerMethodName, $this);
					}
				}
			}
		}
		
		/**
		 * Check if a default event handler method is declared for a 
		 * certain event.
		 * 
		 * Default event handlers follow the following conventions:
		 *  - name is constructed as 'on<ID-OF-CONTROL>_<NAME-OF-EVENT>'
		 *	- it accepts two, non nullable arguments:
		 *		1.	a \red\Object (the sender of the event
		 *		2.	a \red\EventArgument (a data container that could hold extra
		 *			information on the event, such as Data involved)
		 * 
		 * Event handling methods may be private.
		 *
		 * @param \red\web\ui\controls\IControl $sender
		 * @param string $eventName
		 * @param string $handlerMethodName
		 * @return boolean
		 */
		protected function hasDefaultEventHandlerForNamedEvent(\red\web\ui\controls\IControl $sender, $eventName, & $handlerMethodName)
		{
			$parts = array();
			if (method_exists($sender, 'getName'))
			{
				array_push($parts, $sender->getName());
			}
			else if ($sender->hasAttribute('id'))
			{
				array_push($parts, $sender->getAttribute('id'));
			}
			array_push($parts, $eventName);
			$handlerMethodName = 'on'.ucfirst(implode('_', $parts));
			
			$reflector = new \ReflectionObject($this);
			if ($reflector->hasMethod($handlerMethodName))
			{
				$handlerMethod = $reflector->getMethod($handlerMethodName);
				if (\red\EventInfo::isEventHandler($handlerMethod))
				{
					return true;
				}
			}
			return false;
		}
		
		/**
		 * Get this instance ready for output to the client user agent 
		 */
		public function preRender()
		{
			$types = array();
			foreach($this->getAllControls() as $control)
			{
				$typeId = typeid($control);
				$types[$typeId] = $typeId;
				$control->preRender();

			}
			foreach($types as $typeId)
			{
				$this->registerThemeResources($typeId);

				if ($control instanceof IThemable)
				{
					$this->registerThemeResources($typeId);
				}
			}
			foreach($this->themeResources as $type => $resourcesToRegister)
			{
				switch($type)
				{
					case 'css' :
						$this->registerStylesheet('/!theme-css/' . implode('/', array_unique($resourcesToRegister)));
						break;
				}
			}
		}
	}
}