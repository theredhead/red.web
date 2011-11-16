<?php

namespace red\addressbook
{
	use \red\Object;
	use \red\MBString;
	use \red\DateTime;
	use \red\data\DataSchemaRecord;

	/**
	 * Represents a single persons or business' address card.
	 * 
	 * ID
	 * IsCompany
	 * Title
	 * Company
	 * Department
	 * Firstname
	 * Lastname
	 * MiddleName
	 * MaidenName
	 * Displayname
	 * BirthDate
	 * Anniversary
	 */
	class Card extends Object
	{
		/**
		 * @var array
		 */
		private $data;

		/**
		 * @var AddressBook
		 */
		private $addressbook;
		protected function book()
		{
			return $this->addressbook;
		}
		
		
		//<editor-fold defaultstate="collapsed" desc="Property integer ID">
		private $id = null;
		/**
		 * @return integer
		 */
		public function getID()
		{
			return $this->id;
		}
		/**
		 * @param integer $newID
		 */
		protected function setID($newID)
		{
			$this->id = (integer)$newID;
		}
		// </editor-fold>
		// <editor-fold defaultstate="collapsed" desc="Property boolean IsCompany">
		private $isCompany = false;

		/**
		 * @return boolean
		 */
		public function isCompany()
		{
			return $this->isCompany == true;
		}

		/**
		 * @param boolean $newIsCompany
		 */
		public function setIsCompany($newIsCompany)
		{
			$this->isCompany = $newIsCompany == true;
		}
		// </editor-fold>
		// <editor-fold defaultstate="collapsed" desc="Property string Title">
		private $title = '';

		/**
		 * @return string
		 */
		public function getTitle()
		{
			return $this->title;
		}

		/**
		 * @param string $newTitle
		 */
		public function setTitle(MBString $newTitle)
		{
			$this->title = $newTitle;
		}
		// </editor-fold>
		// <editor-fold defaultstate="collapsed" desc="Property MBString Company">
		private $company = null;

		/**
		 * @return MBString
		 */
		public function getCompany()
		{
			return $this->company;
		}

		/**
		 * @param MBString $newCompany
		 */
		public function setCompany(MBString $newCompany)
		{
			$this->company = $newCompany;
		}
		// </editor-fold>
		// <editor-fold defaultstate="collapsed" desc="Property MBString Department">
		private $department = null;

		/**
		 * @return MBString
		 */
		public function getDepartment()
		{
			return $this->department;
		}

		/**
		 * @param MBString $newDepartment
		 */
		public function setDepartment(MBString $newDepartment)
		{
			$this->department = $newDepartment;
		}
		// </editor-fold>
		// <editor-fold defaultstate="collapsed" desc="Property MBString Firstname">
		private $firstname = null;

		/**
		 * @return MBString
		 */
		public function getFirstname()
		{
			return $this->firstname;
		}

		/**
		 * @param MBString $newFirstname
		 */
		public function setFirstname(MBString $newFirstname)
		{
			$this->firstname = $newFirstname;
		}
		// </editor-fold>
		// <editor-fold defaultstate="collapsed" desc="Property MBString Lastname">
		private $lastname = null;

		/**
		 * @return MBString
		 */
		public function getLastname()
		{
			return $this->lastname;
		}

		/**
		 * @param MBString $newLastname
		 */
		public function setLastname(MBString $newLastname)
		{
			$this->lastname = $newLastname;
		}
		// </editor-fold>
		// <editor-fold defaultstate="collapsed" desc="Property MBString Middlename">
		private $middlename = null;

		/**
		 * @return MBString
		 */
		public function getMiddlename()
		{
			return $this->middlename;
		}

		/**
		 * @param MBString $newMiddlename
		 */
		public function setMiddlename(MBString $newMiddlename)
		{
			$this->middlename = $newMiddlename;
		}
		// </editor-fold>
		// <editor-fold defaultstate="collapsed" desc="Property MBString Maidenname">
		private $maidenname = null;

		/**
		 * @return MBString
		 */
		public function getMaidenname()
		{
			return $this->maidenname;
		}

		/**
		 * @param MBString $newMaidenname
		 */
		public function setMaidenname(MBString $newMaidenname)
		{
			$this->maidenname = $newMaidenname;
		}
		// </editor-fold>
		// <editor-fold defaultstate="collapsed" desc="Property MBString DisplayName">
		private $displayName = null;

		/**
		 * @return MBString
		 */
		public function getDisplayName()
		{
			if ($this->displayName->length() == 0)
			{
				if ($this->isCompany() === true)
				{
					$this->displayName = $this->getCompany();
				}
				else
				{
					$this->displayName =
							  $this->getLastName()
							->append(' ')
							->append($this->getMiddlename())
							->append(' ')
							->append($this->getFirstname())
							->replace('  ', ' ');
				}
			}
			// if we're still empty...
			if ($this->displayName->length() == 0)
			{
				$this->displayName = MBString::withString('[Unnamed person]');
			}
			return $this->displayName;
		}

		/**
		 * @param MBString $newDisplayName
		 */
		public function setDisplayName(MBString $newDisplayName)
		{
			$this->displayName = $newDisplayName;
		}
		// </editor-fold>
		// <editor-fold defaultstate="collapsed" desc="Property DateTime Birthdate">
		private $birthdate = null;

		/**
		 * @return DateTime
		 */
		public function getBirthdate()
		{
			return $this->birthdate;
		}

		/**
		 * @param DateTime $newBirthdate
		 */
		public function setBirthdate(DateTime $newBirthdate)
		{
			$this->birthdate = $newBirthdate;
		}
		// </editor-fold>
		// <editor-fold defaultstate="collapsed" desc="Property DateTime Anniversary">
		private $anniversary = null;

		/**
		 * @return DateTime
		 */
		public function getAnniversary()
		{
			return $this->anniversary;
		}

		/**
		 * @param DateTime $newAnniversary
		 */
		public function setAnniversary(DateTime $newAnniversary)
		{
			$this->anniversary = $newAnniversary;
		}
		// </editor-fold>

		public function getPrimaryEmail()
		{
			return $this->book()->getPrimaryEmail($this);
		}
		
		public function getCardSubTitle()
		{
			return $this->isCompany()
					? $this->getDepartment()
					: $this->getTitle();
		}
		
		public function getPhoneNumbers()
		{
			return $this->book()->getPhoneNumbers($this);
		}
		
		public function getEmailAddresses()
		{
			return $this->book()->getEmailAddresses($this);
		}
		
		public function getWebsites()
		{
			return $this->book()->getWebsites($this);
		}
		
		public function getNotes()
		{
			return $this->book()->getNotes($this);
		}

		protected function fill(array $rawData)
		{
//			echo __METHOD__ . '(' . typeid($rawData) . ")<br />\n";

			$this->data = $rawData;

			$this->setID($rawData['ID']);
			$this->setIsCompany($rawData['IsCompany'] == 1);
			$this->setCompany(MBString::withString($rawData['Company']));
			$this->setDepartment(MBString::withString($rawData['Department']));
			$this->setTitle(MBString::withString($rawData['Title']));
			$this->setFirstname(MBString::withString($rawData['Firstname']));
			$this->setMiddlename(MBString::withString($rawData['MiddleName']));
			$this->setLastname(MBString::withString($rawData['Lastname']));
			$this->setMaidenname(MBString::withString($rawData['MaidenName']));
			$this->setDisplayName(MBString::withString($rawData['Displayname']));
			
			//$this->setDisplayName(implode(', ', array_keys($rawData)));
		}
	}
}

#EOF