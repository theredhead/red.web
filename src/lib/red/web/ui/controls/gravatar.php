<?php

namespace red\web\ui\controls
{
	use \red\addressbook\Card;

	class Gravatar extends BindableControl
	{
		const TYPE_MONSTERID = 'monsterid';
		const TYPE_IDENTICON = 'identicon';

		const RATING_PG = 'pg';

		// <editor-fold defaultstate="collapsed" desc="Property string EmailAddress">
		private $emailAddress = null;

		/**
		 * @return string
		 */
		public function getEmailAddress()
		{
			return $this->emailAddress;
		}

		/**
		 * @param string $newEmailAddress
		 */
		public function setEmailAddress($newEmailAddress)
		{
			$this->emailAddress = $newEmailAddress;
		}
		// </editor-fold>
		// <editor-fold defaultstate="collapsed" desc="Property integer Size">
		private $size = 48;

		/**
		 * @return integer
		 */
		public function getSize()
		{
			return $this->size;
		}

		/**
		 * @param integer $newSize
		 */
		public function setSize($newSize)
		{
			$this->size = (integer)$newSize;
		}
		// </editor-fold>
		// <editor-fold defaultstate="collapsed" desc="Property string Type">
		private $type = self::TYPE_IDENTICON;

		/**
		 * @return string
		 */
		public function getType()
		{
			return $this->type;
		}

		/**
		 * @param string $newType
		 */
		public function setType($newType)
		{
			$this->type = $newType;
		}

		// </editor-fold>
		// <editor-fold defaultstate="collapsed" desc="Property string Rating">
		private $rating = self::RATING_PG;

		/**
		 * @return string
		 */
		public function getRating()
		{
			return $this->rating;
		}
		/**
		 * @param string $newRating
		 */
		public function setRating($newRating)
		{
			$this->rating = $newRating;
		}
		// </editor-fold>

		/**
		 * override parent behaviour because it does not make sense
		 * for a gravatar to have children.
		 * 
		 * @todo: make HtmlTag have a canHaveChildren() etc so this does not
		 *		  have to be implemented here.
		 * 
		 * @return boolean 
		 */
		public function hasChildren()
		{
			return false;
		}

		/**
		 * @todo: make HtmlTag have a getChildNodes() that respects canHaveChildren() 
		 *		  etc so this does not have to be implemented here.
		 * 
		 * @return type 
		 */
		public function getChildNodes()
		{
			return array();
		}
		
		public function __construct()
		{
			parent::__construct('img');
		}
		
		/**
		 * formats the url for a gravatar
		 * 
		 * @return type 
		 */
		protected function formatGravatarUrl()
		{
			// http://www.gravatar.com/avatar/5beeab66d6fe021cbd4daa041330cc86?s=128&d=identicon&r=PG
			return sprintf('http://www.gravatar.com/avatar/%s?s=%d&d=%s&r=%s'
					, md5($this->getEmailAddress())
					, $this->getSize()
					, $this->getType()
					, $this->getRating());
		}
		
		/**
		 * IBindable::canBindTo
		 * 
		 * @param mixed $dataItem
		 * @return boolean 
		 */
		public function canBindTo($dataItem)
		{
			return  $dataItem instanceof Card | is_string($dataItem);
		}
		
		/**
		 * overrides parent behaviour to make gravatars not crash if they were
		 * not databound
		 *
		 * @return boolean 
		 */
		public function getRequiresDataBinding()
		{
			return false;
		}
		
		/**
		 * build this control
		 */
		protected function buildControl()
		{
			$dataItem = $this->getDatasource();
			if ($dataItem === null)
			{
				$this->setIsVisible(false);
				return;
			}
			if ($dataItem instanceof Card)
			{
				$this->setEmailAddress($dataItem->getPrimaryEmail());
			}
			else
			{
				$this->setEmailAddress($dataItem);
			}
			if (strlen($this->getEmailAddress()) == 0)
			{
				$this->setIsVisible(false);
				return;
			}

			$this->setAttribute('src', $this->formatGravatarUrl());
			$this->setAttribute('width', $this->getSize());
			$this->setAttribute('height', $this->getSize());
			$this->setAttribute('alt', '-- alt text to be determined --');
			$this->isBuilt = true;
		}
		
		/**
		 * overrides parent implementation to prevent rendering of gravatars 
		 * that don't have a valid url
		 * 
		 * @return type 
		 */
		public function isVisible()
		{
			return $this->getAttribute('src') != '';
		}
		
		/**
		 * overrides parent::setAttribute to expand logical properties from template
		 * 
		 * @param type $name
		 * @param type $value 
		 */
		public function setAttribute($name, $value)
		{
			switch(strtolower($name))
			{
				case 'email':
				case 'emailaddress':
					$this->setEmailAddress($value);
					break;
				case 'size':
					$this->setSize($value);
					break;
				case 'type':
					$this->setType($value);
					break;
				case 'rating':
					$this->setRating($value);
					break;
				default:
					parent::setAttribute($name, $value);
					break;
			}
		}
	}
}