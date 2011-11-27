<?php

namespace red\web\ui\controls
{
	use \red\web\ui\html\HtmlTag;
	use \red\web\ui\html\HtmlText;
	use \red\web\ui\IThemable;
	use \red\EventArgument;


	/**
	 * Directory browser gives you a control that can display files in a
	 * directory.
	 * 
	 * @TODO: implement this control.
	 */
	class DirectoryBrowser extends BindableControl implements IThemable
	{
		const DISPLAYMODE_ICONS		    = 'icons';
		const DISPLAYMODE_LIST		    = 'list';
		const DISPLAYMODE_DETAILS	    = 'details';

		const EV_ICON_CLICKED           = 'IconClicked';
		const EV_SELECTEDINDEX_CHANGED  = 'SelectedIndexChanged';

		/**
		 * The display mode value used when one isn't explicitly set.
		 */
		const DEFAULT_DISPLAY_MODE = self::DISPLAYMODE_ICONS;
		
		// <editor-fold defaultstate="collapsed" desc="State property string DisplayMode">
		/**
		 * Get the current display mode
		 *
		 * @return string
		 */
		public function getDisplayMode()
		{
			return isset($this->state['mode'])
					? $this->state['mode']
					: self::DEFAULT_DISPLAY_MODE;
		}
		/**
		 * @param string $newDisplayMode 
		 */
		public function setDisplayMode($newDisplayMode)
		{
			$this->state['mode'] = $newDisplayMode;
		}
		// </editor-fold>

		/**
		 * @return integer
		 */
		public function getSelectedIndex()
		{
			return isset($this->state['selIx']) ? (integer)$this->state['selIx'] : -1;
		}

		/**
		 * @param integer $newSelectedIndex
		 */
		public function setSelectedIndex($newSelectedIndex)
		{
			if ($newSelectedIndex !== $this->getSelectedIndex())
			{
				if ($newSelectedIndex === false)
				{
					unset($this->state['selIx']);
					$this->selectedIndexChanged();
				}
				else if ((integer)$newSelectedIndex == $newSelectedIndex)
				{
					$this->state['selIx'] = (integer)$newSelectedIndex;
					$this->selectedIndexChanged();
				}
				else
				{
					static::fail('SelectedIndex must be an integral value, or null');
				}
			}
		}

		/**
		 * Notifies listeners that the selected index of this repeater has changed.
		 */
		protected function selectedIndexChanged()
		{
			$this->notifyListenersOfEvent(self::EV_SELECTEDINDEX_CHANGED, new EventArgument());
		}

		/**
		 * @var HtmlTag
		 */
		protected $contentElement;

		public function __construct($tagName = 'div')
		{
			parent::__construct($tagName);
			$this->contentElement = $this->appendChild(new HtmlTag('div'));
		}


		/**
		 * expand logical properties from the template
		 *
		 * @param type $name
		 * @param type $value 
		 */
		public function setAttribute($name, $value)
		{
			switch(strtolower($name))
			{
				case 'mode':
					$this->setDisplayMode($value);
					break;
				default:
					parent::setAttribute($name, $value);
					break;
			}
		}

		/**
		 * implements BindableControls' buildControl to build this controls
		 * inner markup. 
		 */
		protected function buildControl()
		{
			switch($this->getDisplayMode())
			{
				case self::DISPLAYMODE_ICONS :
					$this->buildIconView();
					break;
				case self::DISPLAYMODE_LIST :
					$this->buildListView();
					break;
				case self::DISPLAYMODE_DETAILS :
					$this->buildDetailView();
					break;
				default:
					static::fail("Unknown DisplayMode '%s'", $this->getDisplayMode());
					break;
			}
		}

		public function bind($dataItem)
		{
			$this->canBindTo($dataItem) or static::fail('Cannot bind to "%s"', typeId($dataItem));
			$this->setDatasource($dataItem);
			$this->isBound = true;
		}

		/**
		 * @return void
		 */
		protected function buildIconView()
		{
			$delegate = $this->getDatasource();

//			$columnCount = 4;
//			$this->setAttribute('style', "column-count: $columnCount; -webkit-column-count: $columnCount; -moz-column-count: $columnCount;");

			if ($delegate instanceof IDirectoryBrowserDelegate)
			{
				$numberOfItemsInView = $delegate->numberOfItemsInView();
				$selectedIndex = $this->getSelectedIndex();

				for ($ix = 0; $ix < $numberOfItemsInView; $ix ++)
				{
					$item = $delegate->getFilesystemItemAtIndex($ix);

					$icon = $this->contentElement->appendChild($this->buildIcon($item));
					$icon->addCssClass('file-representation');

					if ($ix === $selectedIndex)
					{
						$icon->addCssClass('selected');
					}
					else
					{
						$icon->setAttribute('onclick', $this->createClientEventTrigger(self::EV_ICON_CLICKED, $ix));
					}
				}
			}
		}

		protected function buildIcon(\SplFileInfo $file)
		{
//			var_dump($file);
			$div = new HtmlTag('div');
			$div->setAttribute('style', 'text-align: center;');

			$icon = new HtmlTag('img');
			$icon->setAttribute('height', '64');
			$icon->setAttribute('width', '64');

//			$type='application/octet-stream';

			if ($file->isDir())
			{
				$type = 'inode/directory';
			}
			else
			{
				$extension = substr(strstr($file->getBasename(), '.'), 1);
				switch($extension)
				{
					case 'php':
						$type = 'application/x-httpd-php';
						break;

					default:
						$type = mime_content_type(''.$file);
				}
			}

			$icon->setAttribute('src', sprintf('http://www.stdicon.com/%s?default=http://www.stdicon.com/application/octet-stream', $type));

			$label = new HtmlTag('span');
			$label->appendChild(new HtmlText($file->getBasename()));
			$label->setAttribute('style', 'white-space: nowrap;');

			$div->appendChild($icon);
			$div->appendChild(new \red\web\ui\html\HtmlLineBreak());
			$div->appendChild($label);
			return $div;
		}

		public function canBindTo($dataItem)
		{
			return $dataItem instanceof IDirectoryBrowserDelegate;
		}

		protected function notePostbackEvent($eventName, $eventArgument)
		{
			switch($eventName)
			{
				case self::EV_ICON_CLICKED:
					$this->setSelectedIndex((integer)$eventArgument);
					break;
				default:
					parent::notePostbackEvent($eventName, $eventArgument);
					break;
			}
		}

		/**
		 * get an array of resource types to try and register.
		 *
		 * @return array
		 */
		static public function getThemeResourceTypes()
		{
			return array('css');
		}
	}
}

#EOF