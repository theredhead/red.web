<?php

namespace red\web\ui\controls
{
	class DirectoryBrowser extends BaseControl
	{
		const DISPLAYMODE_ICONS = 'icons';
		const DISPLAYMODE_LIST = 'list';
		const DISPLAYMODE_DETAILS = 'details';
		
		
		
		protected function setAttribute($name, $value)
		{
			switch(strtolower($name))
			{
				case 'mode'
					// $this->setDisplayMode($value);
				break;
				default:
					parent::setAttribute($name, $value);
			}
		}
	}
}

#EOF