<?php

namespace red\web\ui\controls
{	
	use \red\data\SortDescriptor;
	use \red\web\ui\controls\Table;
	use \red\web\ui\controls\TableColumn;
	
	/**
	 * This interfaces defines the minimum requirements for an object to communicate
	 * table content with a table.
	 */
	interface ITableDatasourceDelegate
	{
		/**
		 * @param Table $table
		 * @param TableColumn $column
		 * @param integer $rowIndex
		 * @return mixed 
		 */
		public function objectValueForTableColumnAtRowIndex(Table $table, TableColumn $column, $rowIndex);
		
		/**
		 * @param Table $table
		 * @return void
		 */
		public function noteSortDescriptorChanged(SortDescriptor $sort=null);
		
		/**
		 * @param Table $table
		 * @return integer
		 */
		public function numberOfRowsInTableView(Table $table);
	}
}

#EOF