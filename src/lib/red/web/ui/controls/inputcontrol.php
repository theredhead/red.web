<?php

namespace red\web\ui\controls
{
	abstract class InputControl extends BaseControl
	{
		// <editor-fold defaultstate="collapsed" desc="State property string Value">
		/**
		 * @return string
		 */
		public function getValue()
		{
			return $this->state['value'];
		}

		/**
		 * @param string $newValue
		 */
		public function setValue($newValue)
		{
			if ($newValue != $this->state['value'])
			{
				// TextChanged
				$this->state['value'] = $newValue;
			}
		}
		// </editor-fold>
		// <editor-fold defaultstate="collapsed" desc="State Property string Name">
		/**
		 * @return string
		 */
		public function getName()
		{
			return $this->state['name'];
		}

		/**
		 * @param string $newName
		 */
		public function setName($newName)
		{
			$this->state['name'] = $newName;
		}
		// </editor-fold>


		public function __construct($type)
		{
			static $instances = 0;
			parent::__construct('input');
			$this->setAttribute('type', $type);
			$this->setName(str_replace(NAMESPACE_SEPARATOR, '_', get_class($this)) . ++$instances);
		}
		
		public function preRender()
		{
			$this->setAttribute('name', $this->getName());
			$this->setAttribute('value', $this->getValue());
		}
		
		public function notePostback(\red\web\http\HttpRequest $request)
		{
			$this->state['value'] = ($request->getFormField($this->getName(), $this->state['value']));
		}
	}
}