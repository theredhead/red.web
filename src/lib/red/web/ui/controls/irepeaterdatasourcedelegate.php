<?php

namespace red\web\ui\controls
{	
	use \red\web\ui\controls\Repeater;
	
	/**
	 * This interfaces defines the minimum requirements for an object to communicate
	 * table content with a table.
	 */
	interface IRepeaterDatasourceDelegate
	{
		/**
		 * @param Repeater $repeater
		 * @param integer $rowIndex
		 * @return integer
		 */
		public function objectValueForRepeaterAtRowIndex(Repeater $repeater, $rowIndex);
			
		/**
		 * @param Repeater $repeater
		 * @return integer
		 */
		public function numberOfRowsInRepeater(Repeater $repeater);
	}
}

#EOF