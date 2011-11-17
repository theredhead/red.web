<?php

namespace red\web\ui\controls\apidoc
{
	use \Reflector;
	use \ReflectionClass;
	use \ReflectionMethod;
	use \ReflectionParameter;
	use \ReflectionProperty;
	use red\web\ui\controls\TemplateControl;
	use red\web\ui\controls\BindableControl;
	use red\web\ui\controls\Repeater;
	use red\web\ui\controls\RepeaterItemCreatedEventArgument;
	use red\web\ui\controls\IBindable;
	use red\web\ui\controls\IRepeaterDatasourceDelegate;
	
	/**
	 * Generate documentation for a code unit on the fly
	 */
	class Documentor extends TemplateControl implements IRepeaterDatasourceDelegate
	{
		private $datasource = null;
		/**
		 * @return \ReflectionClass
		 */
		protected function getDatasource()
		{
			return $this->datasource;
		}
		protected function setDatasource(\ReflectionClass $newDatasource)
		{
			$this->datasource = $newDatasource;
		}
		
		// <editor-fold defaultstate="collapsed" desc="child controls">
		
		/**
		 * @var \red\web\ui\controls\Repeater
		 */
		protected $rptMethods;
		
		/**
		 * @var \red\web\ui\controls\Repeater
		 */
		protected $rptProperties;
		
		/**
		 * @var \red\web\ui\controls\StaticText
		 */
		protected $txtClassName;
		
		// </editor-fold>
		
		/**
		 * Will hold the methods for the class being documented.
		 * 
		 * @var array
		 */
		private $methods = null;

		/**
		 * Will hold the properties for the class being documented.
		 * 
		 * @var array
		 */
		private $properties = null;
		
		
		/**
		 * Bind this control
		 *
		 * @param ReflectionClass $reflector 
		 */
		public function bind(ReflectionClass $reflector)
		{
			trace(__METHOD__);
			$this->setDatasource($reflector);
			$this->txtClassName->setText($reflector->getName());
			
			$this->rptMethods->registerEventListener(Repeater::EV_ITEM_CREATED, 'onRptMethods_ItemCreated', $this);
			$this->rptMethods->bind($this);
		}
		
		private function onRptMethods_ItemCreated(Repeater $sender, RepeaterItemCreatedEventArgument $argument)
		{
			trace('%s %s', __METHOD__, typeid($argument->getRepeatedItem()));

//			$method = $argument->getRepeatedItem()->findFirst(function($o){return $o instanceof MethodHeader;});
//			$method->bind($this->rptMet);
		}
		
		protected function assertReflectionDataGathered()
		{
			assert(is_array($this->methods) or $this->methods = $this->getDatasource()->getMethods());
			assert(is_array($this->properties) or $this->properties = $this->getDatasource()->getProperties());
		}
		
		/**
		 *
		 * @param Repeater $repeater
		 * @return integer
		 */
		public function numberOfRowsInRepeater(Repeater $repeater)
		{
			$this->assertReflectionDataGathered();
			$result = null;

			if ($repeater->isSameInstance($this->rptMethods))
			{
				$result = count($this->methods);
			}
			else if ($repeater->isSameInstance($this->rptProperties))
			{
				$result = count($this->properties);
			}
			else
			{
				static::fail('Unknown repeater.');
			}
			return $result;
		}

		/**
		 *
		 * @param Repeater $repeater
		 * @param type $rowIndex
		 * @return ReflectionMethod
		 */
		public function objectValueForRepeaterAtRowIndex(Repeater $repeater, $rowIndex)
		{
			$this->assertReflectionDataGathered();
			$result = null;

			if ($repeater->isSameInstance($this->rptMethods))
			{
				$result = isset($this->methods[$rowIndex])
						? $this->methods[$rowIndex]
						: null;
			}
			else if ($repeater->isSameInstance($this->rptProperties))
			{
				$result = isset($this->properties[$rowIndex])
						? $this->properties[$rowIndex]
						: null;
			}
			else
			{
				static::fail('Unknown repeater.');
			}
			return $result;
		}
	}
}

#EOF