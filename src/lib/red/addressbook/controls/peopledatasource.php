<?php

namespace red\addressbook\controls
{
	use red\addressbook\AddressBook;
	use red\web\ui\controls\DatasourceControl;
	use red\web\ui\controls\IRepeaterDatasourceDelegate;
	use red\web\ui\controls\Repeater;
	
	class PeopleDatasource extends DatasourceControl implements IRepeaterDatasourceDelegate
	{
		// <editor-fold defaultstate="collapsed" desc="Property AddressBook AddressBook">
		private $addressBook = null;

		/**
		 * @return AddressBook
		 */
		public function getAddressBook()
		{
			if ($this->addressBook === null)
			{
				$this->addressBook = AddressBook::sharedAddressBook();
			}
			return $this->addressBook;
		}

		/**
		 * @param AddressBook $newAddressBook
		 */
		public function setAddressBook(AddressBook $newAddressBook)
		{
			$this->addressBook = $newAddressBook;
		}

		// </editor-fold>

		// <editor-fold defaultstate="collapsed" desc="Property array People">
		private $people = null;

		/**
		 * @return array
		 */
		protected function getPeople()
		{
			if ($this->people === null)
			{
				$this->people = $this->getAddressBook()->getAllCards();
			}
			return $this->people;
		}

		/**
		 * @param array $newPeople
		 */
		private function setPeople(array $newPeople)
		{
			$this->people = $newPeople;
		}
		// </editor-fold>

		public function numberOfRowsInRepeater(Repeater $repeater)
		{
			return $this->getAddressBook()->getTotalNumberOfCards();
		}

		public function objectValueForRepeaterAtRowIndex(Repeater $repeater, $rowIndex)
		{
			$people = $this->getPeople();
			return isset($people[$rowIndex])
					? $people[$rowIndex]
					: null;
		}
		
		public function setAttribute($name, $value)
		{
			switch(strtolower($name))
			{
				case 'from' : 
					$this->setFrom($value);
					break;
				case 'to' : 
					$this->setTo($value);
					break;
				default:
					parent::setAttribute($name, $value);
					break;
			}
		}
	}
}