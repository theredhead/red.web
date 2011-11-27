<?php

namespace demo\pages
{
	use red\web\ui\controls\Button;
	use \red\web\ui\controls\DropdownList;
	use red\EventArgument;
	use red\web\ui\controls\Image;

	/**
	 * This exception is thrown when the "crash" button is clicked.
	 */
	class DemonstrationException extends \Exception
	{
	}

	/**
	 * Hello contains the code required to respond to events activated by the
	 * user in the UI
	 */
	class Hello extends BasePage
	{
		/**
		 * @var DropdownList
		 */
		protected $selTheme;

		/**
		 * @var DropdownList
		 */
		protected $selLanguage;

		protected function load(\red\web\http\HttpRequest $request, \red\web\http\HttpResponse $response)
		{
			parent::load($request, $response);
			$this->selTheme->setSelectedValue($this->getApplication()->getTheme(), false);
			$this->selLanguage->setSelectedValue($this->getApplication()->getLanguage(), false);
		}


		/**
		 * event handler, triggered when btnHello is clicked
		 * 
		 * @param Button $sender
		 * @param EventArgument $argument 
		 */
		private function onBtnHello_clicked(Button $sender, EventArgument $argument)
		{
			// add a simple alert to the page
			$this->alert("You clicked me!");
		}

		/**
		 * Event handler, triggered when btnCrash is clicked
		 *
		 * @param Button $sender
		 * @param EventArgument $argument 
		 * @throws DemonstrationException
		 */
		private function onBtnCrash_clicked(Button $sender, EventArgument $argument)
		{
			// crash this request
			throw new DemonstrationException('You asked for it...');
		}


		private function onSelTheme_SelectedIndexChanged(DropdownList $sender, EventArgument $argument)
		{
			$this->getApplication()->setTheme($sender->getSelectedItem()->getValue());
		}

		private function onSelLanguage_SelectedIndexChanged(DropdownList $sender, EventArgument $argument)
		{
			$this->getApplication()->setLanguage($sender->getSelectedItem()->getValue());
			// by the time execution has reached here, many templates are already loaded.
			// therefor we'll have to redirect to the page again.
//			$this->alert('Your settings will be applied fully upon reload.');
			$this->getCurrentResponse()->redirect($this->getCurrentRequest()->getRequestUrl());
		}

		/**
		 * @param \red\web\ui\controls\Image $sender
		 * @param \red\EventArgument $argument
		 * @return void
		 */
//		private function onImgScreenshot_clicked(Image $sender, EventArgument $argument)
//		{
//			$this->alert("You clicked the image!");
//		}
	}
}

#EOF