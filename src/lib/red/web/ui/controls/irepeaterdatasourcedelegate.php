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
		 * Get the DataItems in position $rowIndex this delegate manages for $repeater
		 * 
		 * @param Repeater $repeater
		 * @param integer $rowIndex
		 * @return integer
		 */
		public function objectValueForRepeaterAtRowIndex(Repeater $repeater, $rowIndex);
			
		/**
		 * Get the number of DataItems this delegate manages for $repeater
		 * 
		 * @param Repeater $repeater
		 * @return integer
		 */
		public function numberOfRowsInRepeater(Repeater $repeater);
	}
}

#EOF