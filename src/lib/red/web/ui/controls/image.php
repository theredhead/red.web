<?php

namespace red\web\ui\controls
{
	class Image extends BaseControl implements IPublishEvents
	{
		/**
		 * The event that gets triggered when the user clicks the image in the
		 * browser. Note that it will not be registered in the client if there
		 * is no listener registered on the server.
		 */
		const EV_CLICKED = 'Clicked';

		public function __construct()
		{
			parent::__construct('img');
		}

		/**
		 * @return array with event names
		 */
		public function getPublishedEvents()
		{
			return array(self::EV_CLICKED);
		}

		/**
		 * The value for the `src` attribute of the image. Must point to a valid
		 * url
		 *
		 * @return string
		 */
		public function getImageHref()
		{
			return $this->state['src'];
		}
		/**
		 * @param string $imageHref
		 */
		public function setImageHref($imageHref)
		{
			$this->state['src']= $imageHref;
		}


		/**
		 * Set the value for the `alt` attribute
		 *
		 * @param string $altText
		 * @return void
		 */
		public function setAltText($altText)
		{
			$this->state['altText'] = $altText;
		}
		/**
		 * Get the value for the `alt` attribute
		 *
		 * @return string;
		 */
		public function getAltText()
		{
			return $this->state['altText'];
		}

		/**
		 * Set the height of the image in pixels.
		 * 
		 * @param $height
		 * @return void
		 */
		public function setHeight($height)
		{
			$this->state['h'] = (integer)$height;
		}

		/**
		 * Get the height of the image in pixels
		 * 
		 * @return int|null
		 */
		public function getHeight()
		{
			return isset($this->state['h']) ? (integer)$this->state['h'] : null;
		}


		/**
		 * Set the width of the image in pixels.
		 *
		 * @param $height
		 * @return void
		 */
		public function setWidth($width)
		{
			$this->state['w'] = (integer)$width;
		}

		/**
		 * Get the width of the image in pixels
		 *
		 * @return int|null
		 */
		public function getWidth()
		{
			return isset($this->state['w']) ? (integer)$this->state['w'] : null;
		}

		/**
		 * Expands logical properties from the template
		 *
		 * @param string $name
		 * @param string $value
		 * @return void
		 */
		public function setAttribute($name, $value)
		{
			switch(strtolower($name))
			{
				case 'src':
				case 'imagehref':
					$this->setImageHref($value);
					break;

				case 'alt':
				case 'alttext':
					$this->setAltText($value);
					break;

				case 'height':
					$this->setHeight($value);
					break;
				case 'width':
					$this->setWidth($value);
					break;

				default:
					parent::setAttribute($name, $value);
					break;
			}
		}

		public function preRender()
		{
			$this->clear();
			if ($this->getImageHref() == '')
			{
				static::fail('ImageHref is not set.');
			}
			if (!$this->hasAttribute('alt'))
			{
				$this->setAttribute('alt', basename($this->getImageHref()));
			}
			if ($this->getWidth() == null || $this->getHeight() == null)
			{
				$realPath = $this->getPage()->getApplication()->mapPath($this->getImageHref());

				if (file_exists($realPath) && is_readable($realPath))
				{
					$img = imagecreatefromstring(file_get_contents($realPath));
					$this->setHeight(imagesy($img));
					$this->setWidth(imagesx($img));
				}
				else
				{
					static::fail('Image file %s does not exist or is not readable.', $this->getImageHref());
				}
			}
			if ($this->hasEventListeners(self::EV_CLICKED))
			{
				$this->setAttribute('onclick', $this->createClientEventTrigger(self::EV_CLICKED, null));
			}

			parent::setAttribute('src', $this->getImageHref());
			parent::setAttribute('height', (string)$this->getHeight());
			parent::setAttribute('width', (string)$this->getWidth());

			parent::preRender();
		}
	}
}

#EOF