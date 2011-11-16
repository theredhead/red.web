<?php

namespace demo\data
{
	use \red\web\ui\controls\DatasourceControl;
	use \red\web\ui\controls\Repeater;
	use \red\web\ui\controls\IRepeaterDatasourceDelegate;
	
	class RepeaterData extends DatasourceControl implements IRepeaterDatasourceDelegate
	{
		/**
		 * Get the number of rows this delegate has information for.
		 *
		 * @param Repeater $repeater
		 * @return type 
		 */
		public function numberOfRowsInRepeater(Repeater $repeater)
		{
			return 10;
		}

		/**
		 *
		 * @param Repeater $repeater
		 * @param type $rowIndex
		 * @return type 
		 */
		public function objectValueForRepeaterAtRowIndex(Repeater $repeater, $rowIndex)
		{
			return sprintf('%d bottles of beer on the wall...', $rowIndex);
		}
	}
}