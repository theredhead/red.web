<?php

namespace red\addressbook\controls
{
	use \red\MBString;
	use \red\web\ui\controls\BindableControl;
	use \red\addressbook\Card;
	use \red\web\ui\controls\Gravatar;
	use \red\web\ui\controls\StaticText;
	use \red\web\ui\html\HtmlTag;
	use \red\web\ui\html\HtmlText;
	use \red\web\ui\html\HtmlLineBreak;
	use red\web\ui\html\HtmlOrderedList;
	use red\web\ui\html\HtmlListItem;

	class CardDetailView extends BindableControl
	{
		// <editor-fold defaultstate="collapsed" desc="Property boolean Editable">

		/**
		 * @return boolean
		 */
		public function isEditable()
		{
			return $this->state['editable'] == true;
		}

		/**
		 * @param boolean $newEditable
		 */
		public function setEditable($newEditable)
		{
			$this->state['editable'] = $newEditable == true;
		}

		// </editor-fold>

		public function canBindTo($dataItem)
		{
			return $dataItem instanceof Card;
		}

		protected function buildControl()
		{
			$card = $this->getDatasource();
			if ($card instanceof Card)
			{				
				$big = $this->appendChild(new HtmlTag('big'));
				$hr = $this->appendChild(new HtmlTag('hr'));
				$gravatar = new Gravatar();
				$gravatar->setSize(64);
				$gravatar->bind($card);
				$big->appendChild($gravatar);
				$displayName = $big->appendChild(new StaticText());
				$displayName->setText($card->getDisplayName());
				
//				if ($this->isEditable())
//				{
//					$big->appendChild(new HtmlText('**'));
//				}
				
				$dl = $this->appendChild(new HtmlTag('dl'));
				
				$this->createDefinition($dl, 'Company', $card->getCompany());
				$this->createDefinition($dl, 'Department', $card->getDepartment());
				$this->createDefinition($dl, 'Firstname', $card->getFirstname());
				$this->createDefinition($dl, 'Middlename', $card->getMiddlename());
				$this->createDefinition($dl, 'Maidenname', $card->getMaidenname());
				$this->createDefinition($dl, 'Lastname', $card->getLastname());
				
				if ($card->getBirthdate() instanceof \red\DateTime)
				{
					$dl->appendChild(new HtmlLineBreak());
					$this->createDefinition($dl, 'Birthdate', $card->getBirthdate());
				}
				$dl->appendChild(new HtmlLineBreak());

				$phoneNumbers = $card->getPhoneNumbers();
				if (count($phoneNumbers) > 0)
				{
					foreach($phoneNumbers as $phoneNumber)
					{
						// if ($phoneNumber instanceof PhoneNumberInfo)
						$this->createDefinition($dl, $phoneNumber['Type'], $phoneNumber['Display']);	
					}
					$dl->appendChild(new HtmlLineBreak());
				}
				
				$webSites = $card->getWebsites();
				if (count($webSites) > 0)
				{
					foreach($webSites as $siteInfo)
					{
						// if ($emailInfo instanceof WebsiteInfo)
						$this->createDefinitionLink($dl, $siteInfo['Type'], $siteInfo['Value']);	
					}
				}
				
				$emailAddresses = $card->getEmailAddresses();
				if (count($emailAddresses) > 0)
				{
					foreach($emailAddresses as $emailInfo)
					{
						// if ($emailInfo instanceof EmailAddressInfo)
						$this->createDefinitionMailTo($dl, $emailInfo['Type'], $emailInfo['Value']);	
					}
				}

				$notes = $card->getNotes();
				if (count($notes) > 0)
				{
					$dl->appendChild(new HtmlTag('hr'));
					foreach($notes as $note)
					{
						$dt = $dl->appendChild(new HtmlTag('dt'));
						$dt->appendChild(new HtmlText($note['EntryDate']));
						$dd = $dl->appendChild(new HtmlTag('dd'));
						$dd->appendChild(new HtmlText($note['Note']));
						$dl->appendChild(new HtmlLineBreak());
					}
				}
			}
		}
		
		protected function valueDisplay($term, $data, $result = null)
		{
			if ($this->isEditable())
			{
				$result = new \red\web\ui\controls\Textbox();
				$result->setAttribute('name', $term);
				$result->setValue($data);
				$result->preRender();
			}
			else
			{
				$result = $result !== null
						? $result
						: new StaticText($data);
			}
			return $result;
		}
		
		protected function createDefinition($dl, $term, $data)
		{
			$term instanceof MBString or $term = MBString::withString($term);
			$data instanceof MBString or $data = MBString::withString($data);
			
			if ($data->length() > 0)
			{
				$dt = $dl->appendChild(new HtmlTag('dt'));
				$dd = $dl->appendChild(new HtmlTag('dd'));

				$dt->appendChild(new StaticText($term));
				$dd->appendChild($this->valueDisplay($term, $data));
			}
		}

		protected function createDefinitionLink($dl, $term, $data)
		{
			$term instanceof MBString or $term = MBString::withString($term);
			$data instanceof MBString or $data = MBString::withString($data);
			
			if ($data->length() > 0)
			{
				$dt = $dl->appendChild(new HtmlTag('dt'));
				$dd = $dl->appendChild(new HtmlTag('dd'));
				$dt->appendChild(new StaticText())->setText($term);

				$mailTo = new HtmlTag('a');
				$mailTo->appendChild(new StaticText())->setText($data);
				$mailTo->setAttribute('href', $data);
				$dd->appendChild($this->valueDisplay($term, $data, $mailTo));
			}
		}

		protected function createDefinitionMailTo($dl, $term, $data)
		{
			$term instanceof MBString or $term = MBString::withString($term);
			$data instanceof MBString or $data = MBString::withString($data);
			
			if ($data->length() > 0)
			{
				$dt = $dl->appendChild(new HtmlTag('dt'));
				$dd = $dl->appendChild(new HtmlTag('dd'));
				$dt->appendChild(new StaticText())->setText($term);

				$mailTo = new HtmlTag('a');
				$mailTo->appendChild(new StaticText())->setText($data);
				$mailTo->setAttribute('href', sprintf('mailto:%s', $data));
				$dd->appendChild($this->valueDisplay($term, $data, $mailTo));
			}
		}
		
		public function getRequiresDataBinding()
		{
			return false;
		}
		
		public function preRender()
		{
			parent::preRender();
			foreach($this->findAll(function($e){return $e instanceof IControl;}) as $ctl)
			{
				$ctl->preRender();
			}
		}
	}
}

#EOF