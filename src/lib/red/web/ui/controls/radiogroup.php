<?php

namespace red\web\ui\controls
{
    use red\web\ui\html\HtmlTag;
    use red\web\ui\html\HtmlText;

    class RadioGroup extends BaseControl
    {
        /**
         * @return integer
         */
        public function getSelectedIndex()
        {
            return isset($this->state['selIx'])
                    ? (integer)$this->state['selIx']
                    : -1;
        }

        /**
         * @param integer $selectedIndex
         */
        public function setSelectedIndex($selectedIndex)
        {
            $this->state['selIx'] = $selectedIndex;
        }

        public function setCaption($caption)
        {
            $this->state['caption'] = $caption;
        }
        public function getCaption()
        {
            return $this->state['caption'];
        }

        protected function acceptsChild(\red\xml\XMLNode $child)
        {
            return  $child instanceof Radio
                    || $child instanceof HtmlTag && $child->getTagName() == 'legend'
                    || $child instanceof \red\xml\XMLText && $child->isWhitespace();
        }

        public function setAttribute($name, $value)
        {
            switch(strtolower($name))
            {
                case 'caption':
                case 'legend':
                    $this->setCaption($value);
                    break;

                default:
                    parent::setAttribute($name, $value);
                    break;
            }
        }

        private $preRendering = false;
        public function preRender()
        {
            $selectedIndex = $this->getSelectedIndex();
            foreach($this->findAll(function($o){return $o instanceof Radio;}) as $ix => $radio)
            {
                $radio->setAttribute('value', $ix);
                $radio->setSelected($ix === $selectedIndex);
            }

            parent::preRender();

            $this->preRendering = true;
            if (strlen($this->getCaption()) > 0)
            {
                $this->setTagName('fieldset');
                $caption = new HtmlTag('legend');
                $caption->appendChild(new HtmlText($this->getCaption()));
//                $caption->appendChild(new HtmlText(sprintf(' (%d)', $selectedIndex)));

                $this->prependChild($caption);;
            }
            $this->preRendering = false;
        }

        public function notePostback(\red\web\http\HttpRequest $request)
        {
            if (false !== ($selectedIx = $request->getFormField($this->getClientId(), false)))
            {
                $this->setSelectedIndex($selectedIx);
            }

            parent::notePostback($request);
        }
    }
}

#EOF