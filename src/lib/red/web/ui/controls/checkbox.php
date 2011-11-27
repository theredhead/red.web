<?php

namespace red\web\ui\controls
{
	class Checkbox extends InputControl
	{
		public function isChecked()
		{
			return $this->getValue() == 'checked';
		}
		public function setChecked($checked)
		{
			$this->setValue($checked == true ? 'checked' : '');
		}
		
		public function __construct()
		{
			parent::__construct('checkbox');
		}
		
		public function preRender()
		{
			parent::preRender();
			$this->unsetAttribute('value');

			if ($this->isChecked())
			{
				$this->setAttribute('checked', 'checked');
			}
			else
			{
				$this->unsetAttribute('checked');
			}
		}
		
		public function notePostback(\red\web\http\HttpRequest $request)
		{
			$this->state['value'] = $request->getFormField($this->getName(), null) != null ? 'checked' : null;
		}

	}
}