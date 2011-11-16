<?php

namespace demo\pages
{
	use red\Object;
	use red\web\ui\html\HtmlText;
	use red\web\ui\WebPage;
	use red\web\ui\controls\Button;
	use red\web\ui\controls\DataGrid;
	use red\web\ui\controls\HeaderClickedEventArgument;
	use red\web\ui\controls\CellClickedEventArgument;
	use red\EventArgument;
	
	class DemonstrationException extends \Exception
	{
		
	}
	
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
		 * event handler, triggered when btnCrash is clicked
		 *
		 * @param Button $sender
		 * @param EventArgument $argument 
		 */
		private function onBtnCrash_clicked(Button $sender, EventArgument $argument)
		{
			// crash this request
			throw new DemonstrationException('You asked for it...');
		}
	}
}

#EOF