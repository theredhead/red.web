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
	class EventInfo extends Obj
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
		 * @return Obj
		 */
		public function getHandlerInstance()
		{
			return $this->handlerInstance;
		}

		/**
		 * @param Obj $newHandlerInstance
		 */
		public function setHandlerInstance(Obj $newHandlerInstance)
		{
			$this->handlerInstance = $newHandlerInstance;
		}
		// </editor-fold>
		// <editor-fold defaultstate="collapsed" desc="Property Object RegisteringInstance">
		private $registeringInstance = null;

		/**
		 * @return Obj
		 */
		public function getRegisteringInstance()
		{
			return $this->registeringInstance;
		}

		/**
		 * @param Obj $newRegisteringInstance
		 */
		public function setRegisteringInstance(Obj $newRegisteringInstance)
		{
			$this->registeringInstance = $newRegisteringInstance;
		}

		// </editor-fold>

		/**
		 * @param Obj $registeringInstance The instance that registered the handler
		 * @param Obj $handlingInstance The instance that will act on an $eventName
		 * @param string $eventName The name of the event to act on
		 * @param string $methodName The name of the method that will be used to act on $eventName
		 */
		public function __construct(Obj $registeringInstance, Obj $handlingInstance, $eventName, $methodName)
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
		 * @param Obj $sender
		 * @param EventArgument $argument 
		 */
		final public function __invoke(Obj $sender, EventArgument $argument)
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
					if (	(	$sender->getClass()->isSubclassOf('red\\Obj')
							||  $sender->getClass()->getName() == 'red\\Obj')
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