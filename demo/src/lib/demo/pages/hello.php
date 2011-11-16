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
	
	class Hello extends BasePage
	{	
		private function onBtnHello_clicked(Button $sender, EventArgument $argument)
		{
			$this->alert("You clicked me!");
		}

		private function onBtnCrash_clicked(Button $sender, EventArgument $argument)
		{
			$this->alert("You clicked me!");

			throw new \Exception('You asked for it...');
		}
	}
}

#EOF