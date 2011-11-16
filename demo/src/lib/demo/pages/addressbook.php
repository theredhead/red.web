<?php

namespace demo\pages
{
	use \red\EventArgument;
	use \red\web\ui\WebPage;
	use \red\addressbook\AddressBook as Addresses;
	use \red\web\ui\html\HtmlTag;
	use \red\web\ui\html\HtmlText;
	use \red\web\ui\controls\DataGrid;
	use \red\web\ui\controls\CellClickedEventArgument;

	class AddressBook extends BasePage
	{
		public function __construct()
		{
			parent::__construct();
			$this->registerEventListener(WebPage::EV_PAGE_INIT, 'onPageInit', $this);
		}
		
		private function onPageInit(WebPage $sender, EventArgument $argument)
		{
//			$addressBook = Addresses::sharedDefault();
//			$cardCount = $addressBook->getTotalNumberOfCards();
//
//			$h1 = $this->getBodyElement()->prependChild(new HtmlTag('h1'));
//			$h1->appendChild(new HtmlText(sprintf('%d cards.', $cardCount)));
		}
		
		private function onCardList_CellClicked(DataGrid $sender, CellClickedEventArgument $argument)
		{
			$this->alert($argument->getDataItem());
		}
	}
}

#EOF