<?php

namespace red\web\ui\controls
{
	class Container extends BaseControl
	{
		public function setAttribute($name, $value)
		{
			switch(strtolower($name))
			{
				case 'tagname':
					$this->setTagName($value);
					break;
				default:
					parent::setAttribute($name, $value);
					break;
			}
		}
	}
}

#EOF