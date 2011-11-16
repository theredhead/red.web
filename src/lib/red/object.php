<?php

namespace red
{
	abstract class Object
	{
		private static $instanceCounter = 0;
		private static $reflectors = array();
		
		private $objectIdentity = -1;
		
		public function __construct()
		{
			if ($this->objectIdentity !== -1)
			{
				throw new \BadMethodCallException(
						sprintf('[%s __construct() is the constructor and can only be called once.]'
									, $this->getObjectIdentity()), E_USER_ERROR, null);
			}
			if (! isset(self::$reflectors[get_class($this)]))
			{
				self::$reflectors[get_class($this)] = new \ReflectionClass($this);
			}
			$this->objectIdentity = (++self::$instanceCounter);
			
			// toggle crash on missing docblock
			if(false) 
			{
				$this->assertProperDocumentation();
			}
		}
		
		public function __clone()
		{
//			static::fail('__clone must be implemented to support cloning of objects.');
		}
		
		public function __destruct()
		{
			if ($this->objectIdentity === -1)
			{
				throw new \Exception(sprintf('%s is not calling Object::__construct();', get_class($this)));
			}
		}
		
		/**
		 * @return ReflectionClass
		 */
		public function getReflector()
		{
			return self::$reflectors[get_class($this)];
		}
		/**
		 * Get a string representation of this object.
		 * 
		 * return string
		 */
		public function toString()
		{
			return sprintf('[%s id:%d]', typeid($this), $this->getObjectIdentity());
		}
		
		/**
		 * Get a hash that will uniquely identify this instance during its lifetime
		 * 
		 * return string
		 */
		final public function toHash()
		{
			return md5($this->getObjectIdentity());
		}

		/**
		 * Get an the number of Object (and descendents) instances created at the time this instance was created.
		 * 
		 * return integer
		 */
		final public function getObjectIdentity()
		{
			return (integer)$this->objectIdentity;
		}
		
		/**
		 * Determine if another instance is the same instance.
		 */
		final public function isSameInstance(Object $obj)
		{
			return $this->getObjectIdentity() === $obj->getObjectIdentity();
		}

		/**
		 * PHP internal method. use toString() instead.
		 *
		 * @return string
		 */
		final public function __toString()
		{
			return (string)$this->toString();
		}
		
		/**
		 * Throw an exception
		 * 
		 * @param mixed $message
		 * @throws \Exception
		 * @throws \LogicException 
		 */
		static protected function fail($message)
		{
			$innerException = null;
			if ($message instanceof \Exception)
			{
				$innerException = $message;
				$message = $innerException->getMessage();
			}
			if (func_num_args() > 1)
			{
				$message = call_user_func_array('sprintf', func_get_args());
			}
			throw new \LogicException(sprintf('[%s] %s', get_called_class(), $message), E_ERROR, $innerException);
		}
		// <editor-fold defaultstate="collapsed" desc="Event handling">
		
		/**
		 * Holds all event listeners.
		 * 
		 * @var array
		 */
		private $listeners = array();
		
		/**
		 * Register an event handler to fire whenever $eventName
		 *
		 * @param string $eventName
		 * @param callback $handler
		 * @param boolean $prepend 
		 * @return EventInfo The handler registered.
		 */
		final public function registerEventListener($eventName, $handlerMethodName=null, $handlingInstance=null, $prepend=false)
		{
			$handlerMethodName = $handlerMethodName !== null
					? $handlerMethodName
					: 'on'.$eventName;

			$handlingInstance = $handlingInstance !== null
					? $handlingInstance
					: $this;

			if (! isset($this->listeners[$eventName]))
			{
				$this->listeners[$eventName] = array();
			}
			
			$handler = new EventInfo($this, $handlingInstance, $eventName, $handlerMethodName);
			if ($prepend)
			{
				array_push($this->listeners[$eventName], $handler);
			}
			else 
			{
				array_unshift($this->listeners[$eventName], $handler);
			}
			return $handler;
		}

		/**
		 * Remove a previously registered handler
		 * 
		 * @param type $eventName
		 * @param EventInfo $info
		 * @return void
		 */
		final public function removeEventListener($eventName, EventInfo $info)
		{
			if (!isset($this->listeners[$eventName]))
			{
				return;
			}
			else
			{
				$numberOfListeners = count($this->listeners[$eventName]);
				for ($i = 0; $i < $numberOfListeners; $i ++)
				{
					if ($info === $this->listeners[$eventName][$i])
					{
						// remove from array
						unset($this->listeners[$eventName][$i]);
						// reindex
						$this->listeners[$eventName] = array_values($this->listeners[$eventName]);
						break;
					}
				}
			}
		}
		
		/**
		 *
		 * @param type $eventName
		 * @param EventArgument $eventArg 
		 */
		final protected function notifyListenersOfEvent($eventName, EventArgument $eventArg)
		{
			if (isset($this->listeners[$eventName]))
			{
				foreach($this->listeners[$eventName] as $handler)
				{
					$handler($this, clone $eventArg);
				}
			}
		}

		/**
		 * Notify this instance that it should handle an event message
		 *
		 * @param EventInfo $eventInfo
		 * @param Object $sender
		 * @param EventArgument $arg 
		 */
		final protected function receiveEventMessage(EventInfo $eventInfo, Object $sender, EventArgument $arg)
		{
			$reflector = new \ReflectionObject($this);
			$method = $reflector->getMethod($eventInfo->getHandlerMethodName());

			// @FIXME: there is a bug in my logic here,,,
			// 
			// allow event handlers to be private only if the instance that
			// registered the listener is the instance that is going to handle
			// the callback.
//			if ($eventInfo->getRegisteringInstance() === $this || $sender === $this)
			{
				$method->setAccessible(true);
			}
			$method->invoke($this, $sender, $arg);
		}
		// </editor-fold>
		
		/**
		 * dump an overview of code entities that don't have any documentation.
		 * meant to be used during debugging and called from the __constructor
		 * 
		 * @staticvar array $docRegistry 
		 */
		private function assertProperDocumentation()
		{
			$typeId = typeid($this);

			$check = array(
//						'constants' => $this->getReflector()->getConstants(),
				'properties' => $this->getReflector()->getProperties(),
				'methods' => $this->getReflector()->getMethods()
			);
			$result = array();
			$dump = false;
			foreach($check as $section => $reflectors)
			{
				foreach($reflectors as $reflector)
				{
					// activate code completion
					if ($reflector instanceof \Reflector)
					{
						if ($reflector instanceof ReflectionClass && !$reflector->isUserDefined()) break;
						if ($reflector->isPrivate()) break;
						if ($reflector->getDeclaringClass()->getName() === get_class($this))
						{
							if ($reflector->getDocComment() == '' && substr($reflector->getName(), 0, 2) !== '__')
							{
								if ($reflector instanceof \ReflectionMethod)
								{
									$lbl = sprintf('%s line: %d', basename($reflector->getFileName()), $reflector->getStartLine());
									$url = sprintf('txmt://open?url=file://%s&line=%s', $reflector->getFileName(), $reflector->getStartLine());
								}
								else
								{
									$lbl = sprintf('%s', basename($reflector->getDeclaringClass()->getFileName()));
									$url = sprintf('txmt://open?url=file://%s', $reflector->getDeclaringClass()->getFileName());
								}
								$result[$typeId][$section][$reflector->getName()] = sprintf('<a href="%s">%s</a>', $url, $lbl);
								$dump = true;
							}
						}
					}
				}
			}

			if ($dump)
			{
				echo '<h1>Missing documentation on '.$typeId.'</h1>';
				echo bootstrap_exception_handler_var_dump($result);
				exit;
			}

			assert(! $dump);
		}
	}
}