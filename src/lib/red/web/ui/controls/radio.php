<?php

namespace red\web\ui\controls
{
    use red\web\ui\html\HtmlTag;
    use red\web\ui\html\HtmlText;

    class Radio extends BaseControl
    {
        const LABEL_POSITIONED_RIGHT = 0x0001;
        const LABEL_POSITIONED_LEFT = 0x0002;

        /**
         * @param boolean $selected
         */
        public function setSelected($selected)
        {
            $this->state['selected'] = \red\Convert::toBoolean($selected) ? 'Y' : 'N';
        }
        /**
         * @return boolean
         */
        public function getSelected()
        {
            return $this->state['selected'] === 'Y' ? true : false;
        }

        public function __construct()
        {
            parent::__construct('label');
        }

        protected function attachToParent(\red\xml\XMLNode $parent)
        {
            $parent instanceof RadioGroup or static::fail('Cannot use a `Radio` outside of a `RadioGroup`');
            parent::attachToParent($parent);
        }

        /**
         * @return \red\web\ui\controls\RadioGroup
         */
        public function getParentNode()
        {
            return parent::getParentNode();
        }

        /**
         * @return string
         */
        public function getLabelText()
        {
            return $this->state['lbl'];
        }
        /**
         * @param string $labelText
         */
        public function setLabelText($labelText)
        {
            $this->state['lbl'] = $labelText;
        }

        /**
         * @var integer
         */
        protected $labelPosition = self::LABEL_POSITIONED_RIGHT;
        /**
         * @return int
         */
        public function getLabelPosition()
        {
            return isset($this->state['lblPos'])
                        ? (integer)$this->state['lblPos']
                        : self::LABEL_POSITIONED_RIGHT;
        }
        /**
         * @param int $labelPosition
         */
        public function setLabelPosition($labelPosition)
        {
            $this->state['lblPos'] = $labelPosition;
        }

        public function setAttribute($name, $value)
        {
            switch(strtolower($name))
            {
                case 'labeltext' :
                    $this->setLabelText($value);
                    break;

                case 'labelposition' :
                case 'align' :
                    $position = strtolower($value) == 'left'
                            ? self::LABEL_POSITIONED_LEFT
                            : self::LABEL_POSITIONED_RIGHT;
                    $this->setLabelPosition($position);
                    break;
                default :
                    parent::setAttribute($name, $value);
                    break;
            }
        }


        public function preRender()
        {
            parent::preRender();

            $this->appendChild(new \red\web\ui\html\HtmlText($this->getLabelText()));
            $input = new \red\web\ui\html\HtmlTag('input');
            $input->setAttribute('type', 'radio');
            if ($this->getSelected())
            {
                $input->setAttribute('checked', 'checked');
            }
            $input->setAttribute('name', $this->getParentNode()->getClientId());
            $input->setAttribute('value', $this->getAttribute('value'));
            $this->unsetAttribute('value');

            if ($this->getLabelPosition() == self::LABEL_POSITIONED_LEFT)
            {
                $this->appendChild($input);
            }
            else
            {
                $this->prependChild($input);
            }
        }
    }
}

#EOF