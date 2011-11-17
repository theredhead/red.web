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
	use red\web\ui\controls\IBindable;
	use red\web\ui\controls\IRepeaterDatasourceDelegate;

	
	abstract class ___ReflectionControl extends TemplateControl implements IBindable
	{
		/**
		 * Flag indicating databinding status
		 *
		 * @var boolean
		 */
		private $isBound = false;

		/**
		 * called during databinding
		 *
		 * @param Reflector $dataItem 
		 */
		public function bind($dataItem)
		{
			
			$this->bindTo($dataItem);
			$this->isBound = true;
		}
		
		/**
		 * Implements this controls actual binding.
		 * 
		 * @param \Reflector $reflector
		 * @return type 
		 */
		protected function bindTo(\Reflector $reflector)
		{
			$me = $this;
			foreach($this->findAll(function($e)use($me){return !$e->isSameInstance($me) && $e instanceof IBindable;}) as $bindable)
			{
				if ($bindable->canBindTo($reflector))
				{
					$bindable->bind($reflector);
				}
			}
		}
		
		/**
		 * See if this control has been databound
		 * 
		 * @return boolean
		 */
		public function isBound()
		{
			return $this->isBound;
		}
	}
}

#EOF