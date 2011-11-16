<?php

namespace demo\data
{
	use \red\web\ui\controls\BaseControl;
	use \red\web\ui\controls\Repeater;
	use \red\web\ui\controls\IRepeaterDatasourceDelegate;
	
	class RepeaterData extends BaseControl implements IRepeaterDatasourceDelegate
	{
		public function __construct()
		{
			parent::__construct();
		}

		public function numberOfRowsInRepeater(Repeater $repeater)
		{
			return 10;
		}

		public function objectValueForRepeaterAtRowIndex(Repeater $repeater, $rowIndex)
		{
			return sprintf('%d bottles of beer on the wall...', $rowIndex);
		}

		/**
		 * TODO: migrate this to an abstract baseclass that is invisible
		 * @return boolean
		 */
		public function isVisible()
		{
			return false;
		}
	}
}