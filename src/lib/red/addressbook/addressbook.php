<?php

namespace red\addressbook
{
	use \red\Object;
	use \red\data\DataStore;
	
	/**
	 * Represents a single addressbook and the standardized addressbook API
	 */
	class AddressBook extends Object
	{
		/**
		 * @var DataStore
		 */
		private $storage = null;

		/**
		 * Create a new addressbook.
		 */
		public function __construct($pathToFile=null)
		{
			parent::__construct();
		}
		
		/**
		 * Connect this addressbook to a file on disk
		 * 
		 * @param string $pathToFile 
		 */
		public function connectToFile($pathToFile)
		{
			$this->storage = new DataStore();
			$this->storage->connect($pathToFile);
		}
		
		/**
		 * Create a new addressbook instance that is already connected to a file on disk
		 *
		 * @param type $pathToFile
		 * @return static 
		 */
		static public function withFile($pathToFile)
		{
			$instance = new static();
			$instance->connectToFile($pathToFile);
			return $instance;
		}
		
		
		/**
		 * @return AddressCard
		 */
		public function createCard()
		{
			$card = new AddressCard($this);
			return $card;
		}
		
		/**
		 * Get the default addressbook
		 *
		 * @staticvar AddressBook $sharedDefault
		 * @return AddressBook
		 */
		static public function sharedAddressBook()
		{
			static $sharedDefault = null;
			if ($sharedDefault === null)
			{
				$addressesFile = realpath($_SERVER['DOCUMENT_ROOT'] . 
					DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, array('..', 'app-data', 'addressbook.sqlite')));
				$sharedDefault = AddressBook::withFile($addressesFile);
			}
			return $sharedDefault;
		}
		
		/**
		 * Counts all cards in the store and returns the number
		 *
		 * @return integer
		 */
		public function getTotalNumberOfCards()
		{
			return $this->storage->createCommand('SELECT COUNT(*) FROM `Person`')->executeScalar();
		}
		
		/**
		 * @param AddressCard $card 
		 */
		public function persistCard(AddressCard $card)
		{
			
		}
		
		public function getPrimaryEmail(Card $card)
		{
			return $this->storage->createCommand('SELECT Value FROM `Email` WHERE Person=?', array($card->getId()))->executeScalar();
		}
		
		public function getPhoneNumbers(Card $card)
		{
			return $this->storage->createCommand('SELECT Type, CountryCode, AreaCode, PhoneNumber, Display FROM `PhoneNumber` WHERE Person=?', array($card->getId()))->executeTable();
		}

		public function getEmailAddresses(Card $card)
		{
			return $this->storage->createCommand('SELECT Type, Value FROM `Email` WHERE Person=?', array($card->getId()))->executeTable();
		}

		public function getWebsites(Card $card)
		{
			return $this->storage->createCommand('SELECT Type, Value FROM `Website` WHERE Person=?', array($card->getId()))->executeTable();
		}

		public function getNotes(Card $card)
		{
			return $this->storage->createCommand('SELECT * FROM `Note` WHERE Person=? ORDER BY EntryDate DESC', array($card->getId()))->executeTable();
		}
		
		public function getAllCards()
		{
			$reflector = new \ReflectionClass('red\\addressbook\\Card');
			$fillMethod = $reflector->getMethod('fill');
			$fillMethod->setAccessible(true);
			
			$bookProperty = $reflector->getProperty('addressbook');
			$bookProperty->setAccessible(true);
			
			$cardReader = $this->storage->createCommand('SELECT * FROM `Person` ORDER BY Lastname, Firstname')->execute();
			$cards = array();
			while (false !== ($record = $cardReader->fetch(\PDO::FETCH_ASSOC)))
			{
				$card = new Card();
				$bookProperty->setValue($card, $this);
				$fillMethod->invoke($card, $record);
				array_push($cards, $card);
			}
			$fillMethod->setAccessible(false);
			$bookProperty->setAccessible(false);
			return $cards;
		}
	}
}

#EOF