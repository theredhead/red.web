<?php

namespace red\web\ui\controls
{
	use \red\MBString;
	use \red\web\ui\html\HtmlTag;
	use \red\web\ui\html\HtmlText;

	class DropdownItem extends BaseControl
	{
		public function __construct()
		{
			parent::__construct('option');
		}

		/**
		 * @var \red\MBString
		 */
		private $value = null;
		/**
		 * @return \red\MBString
		 */
		public function getValue()
		{
			return $this->value;
		}
		/**
		 * @param \red\MBString $value
		 */
		public function setValue($value)
		{
			$this->value = $value;
		}

		public function setAttribute($name, $value)
		{
			switch(strtolower($name))
			{
				case 'value':
					$this->setValue($value);
					break;

				default:
					parent::setAttribute($name, $value);
			}
		}


	}
}