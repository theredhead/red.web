<?php

namespace red\web\ui\controls
{
	class CarouselContent extends BaseControl
	{
        /**
         * @var string
         */
        protected $width;
        /**
         * @param string $width
         */
        public function setWidth($width)
        {
            $this->width = $width;
        }
        /**
         * @var string
         */
        protected $height;
        /**
         * @param string $height
         */
        public function setHeight($height)
        {
            $this->height = $height;
        }

        public function preRender()
        {
            parent::preRender();

            $this->setAttribute('style', sprintf('width: %s; height: %s;', $this->width, $this->height));
        }
    }
}

#EOF