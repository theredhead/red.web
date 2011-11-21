<?php

namespace demo\pages
{
	use red\web\ui\controls\Button;
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