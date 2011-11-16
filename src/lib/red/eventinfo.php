<?php

namespace red
{
	/**
	 * EventInfo is an invokable object meant to enforce the way event handler methods
	 * look in code (argument list wise.)
	 * 
	 * You are guaranteed to get an instance of \red\Object as the first argument
	 * and an instance of \red\EventArgument as the second.
	 * 
	 * By encapsulation and carefully delegated message passing, event handlers can be private.
	 */
	class EventInfo extends Object
	{
		// <editor-fold defaultstate="collapsed" desc="Property string EventName">
		private $eventName = null;

		/**
		 * @return string
		 */
		public function getEventName()
		{
			return $this->eventName;
		}

		/**
		 * @param string $newEventName
		 */
		public function setEventName($newEventName)
		{
			$this->eventName = $newEventName;
		}
		// </editor-fold>
		// <editor-fold defaultstate="collapsed" desc="Property string HandlerMethodName">
		private $handlerMethodName = null;

		/**
		 * @return string
		 */
		public function getHandlerMethodName()
		{
			return $this->handlerMethodName;
		}

		/**
		 * @param string $newHandlerMethodName
		 */
		public function setHandlerMethodName($newHandlerMethodName)
		{
			$this->handlerMethodName = $newHandlerMethodName;
		}
		// </editor-fold>
		// <editor-fold defaultstate="collapsed" desc="Property Object HandlerInstance">
		private $handlerInstance = null;

		/**
		 * @return Object
		 */
		public function getHandlerInstance()
		{
			return $this->handlerInstance;
		}

		/**
		 * @param Object $newHandlerInstance
		 */
		public function setHandlerInstance(Object $newHandlerInstance)
		{
			$this->handlerInstance = $newHandlerInstance;
		}
		// </editor-fold>
		// <editor-fold defaultstate="collapsed" desc="Property Object RegisteringInstance">
		private $registeringInstance = null;

		/**
		 * @return Object
		 */
		public function getRegisteringInstance()
		{
			return $this->registeringInstance;
		}

		/**
		 * @param Object $newRegisteringInstance
		 */
		public function setRegisteringInstance(Object $newRegisteringInstance)
		{
			$this->registeringInstance = $newRegisteringInstance;
		}

		// </editor-fold>

		/**
		 * @param Object $registeringInstance The instance that registered the handler
		 * @param Object $handlingInstance The instance that will act on an $eventName
		 * @param string $eventName The name of the event to act on
		 * @param string $methodName The name of the method that will be used to act on $eventName
		 */
		public function __construct(Object $registeringInstance, Object $handlingInstance, $eventName, $methodName)
		{
			parent::__construct();
			$this->setRegisteringInstance($registeringInstance);
			$this->setHandlerInstance($handlingInstance);
			$this->setEventName($eventName);
			$this->setHandlerMethodName($methodName);
		}
		
		/**
		 * Pass the message on to the receiving instance (HandlerInstance)
		 *
		 * @param Object $sender
		 * @param EventArgument $argument 
		 */
		final public function __invoke(Object $sender, EventArgument $argument)
		{
			$this->getHandlerInstance()->receiveEventMessage(
					$this, $sender, $argument);
		}
		
		/**
		 * Determine if a methods signature makes it a viable event handler.
		 *
		 * @param \ReflectionMethod $method
		 * @return boolean 
		 */
		static public function isEventHandler(\ReflectionMethod $method)
		{
			$result = false;
			if ($method->getNumberOfRequiredParameters() >= 2)
			{
				$parameters = $method->getParameters();
				$sender = array_shift($parameters);
				$argument = array_shift($parameters);
				if ($sender instanceof \ReflectionParameter && $argument instanceof \ReflectionParameter)
				{
					if (	(	$sender->getClass()->isSubclassOf('red\\Object')
							||  $sender->getClass()->getName() == 'red\\Object')
						&&	(	$argument->getClass()->isSubclassOf('red\\EventArgument')
							||	$argument->getClass()->getName() == 'red\\EventArgument'))
					{
						$result = true;
					}
				}
			}
			return $result;
		}
	}
}

#EOF