<?php

namespace red\web\ui\controls
{
    use red\web\ui\html\HtmlTag;

	class Carousel extends BaseControl implements \red\web\ui\IThemable
	{
        const SIZE_UNIT_PX = 'px';
        const SIZE_UNIT_PT = 'pt';
        const SIZE_UNIT_CM = 'cm';
        const SIZE_UNIT_EM = 'em';

        public function __construct()
        {
            parent::__construct('div');
            if (strlen($this->getSizeUnit()) == 0)
            {
                $this->setSizeUnit(self::SIZE_UNIT_PX);
            }
            if (strlen($this->getWidth()) == 0)
            {
                $this->setWidth(500);
            }
            if (strlen($this->getHeight()) == 0)
            {
                $this->setHeight(200);
            }
        }

        public function setEasing($easing)
        {
            $this->state['easing'] = $easing;
        }
        public function getEasing()
        {
            return isset($this->state['easing']) ? $this->state['easing'] : 'swing';
        }

        /**
         * @param string $sizeUnit
         */
        public function setSizeUnit($sizeUnit)
        {
            $this->state['sizeunit'] = $sizeUnit;
        }
        public function getSizeUnit()
        {
            return $this->state['sizeunit'];
        }
        /**
         * @param integer $width
         */
        public function setWidth($width)
        {
          $this->state['width'] = $width;
        }
        /**
         * @return integer
         */
        public function getWidth()
        {
          return (integer)$this->state['width'];
        }

        public function setHeight($height)
        {
            $this->state['height'] = $height;
        }

        public function getHeight()
        {
            return $this->state['height'];
        }

        protected function acceptsChild(\red\xml\XMLNode $child)
        {
            return $child instanceof CarouselContent
                    || ($child instanceof \red\xml\XMLText && $child->isWhitespace())
                    || ($child instanceof HtmlTag && $child->hasCssClass('CarouselContentWrapper'));
        }

        public function preRender()
        {
            $page = $this->getPage();
            $page->registerClientScript(\red\web\ui\ScriptManager::CDN_URL_JQUERY);

            $clientId = $this->getClientId();

            if ($page instanceof \red\web\ui\WebPage)
            {
                $options = json_encode(array(
                     'autoSlideDelay' => 5000 //$this->getAutoslideSpeed()
                    ,'animationSpeed' => 750 //$this->getAnimationSpeed()
                    ,'animationEasing' => $this->getEasing()
                ));
                $page->registerStartupScript("window.{$clientId} = $('#$clientId').carousel($options);");
            }

            if (! in_array($this->getEasing(), array('swing', 'linear')))
            {
                $page->registerClientScript(\red\web\ui\ScriptManager::CDN_URL_JQUERY);
                $page->registerClientScript(\red\web\ui\ScriptManager::CDN_URL_JQUERYUI);
            }

            $contents = $this->findAll(function($o){
                return $o instanceof CarouselContent;
            });

            $width = $this->getWidth() . $this->getSizeUnit();
            $height = $this->getHeight() . $this->getSizeUnit();
            $this->setAttribute('style', sprintf('width: %s; height: %s;', $width, $height));

            $this->clear();

            $wrapper = new HtmlTag('div');
            $wrapper->addCssClass('CarouselContentWrapper');
            $wrapper->setAttribute('style', sprintf('width: %d%s; height: %s;',
                ($this->getWidth() * count($contents)), $this->getSizeUnit(), $height));

            $this->appendChild($wrapper);

            foreach($contents as $content)
            {
                $content->setWidth($width);
                $content->setHeight($height);

                $wrapper->appendChild($content);
            }

            parent::preRender();
        }

        public function setAttribute($name, $value)
        {
            switch(strtolower($name))
            {
                case 'width' :
                    $this->setWidth(intval($value));
                    break;
                case 'height' :
                    $this->setHeight(intval($value));
                    break;
                case 'easing' :
                    $this->setEasing($value);
                    break;

                default:
                    parent::setAttribute($name, $value);
                    break;
            }
        }


        /**
         * get an array of resource types to try and register.
         *
         * array should hold filename extensions to register as values
         *
         * example:
         *  return array('css', 'js');
         *
         * @return array
         */
        static public function getThemeResourceTypes()
        {
            return array('css', 'js');
        }
    }
}

#EOF