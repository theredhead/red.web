<?php

namespace demo\data
{
	use \red\data\SortDescriptor;
	use \red\web\ui\controls\Table;
	use \red\web\ui\controls\TableColumn;
	
	
	class TestDatasource extends \red\web\ui\controls\BaseControl implements \red\web\ui\controls\ITableDatasourceDelegate
	{
		private $data;
		
		protected function getData(SortDescriptor $sort=null)
		{
			
			$db = new DemoDatabase();
			$this->data = $db->getSchemaInfo($sort);
			unset($db);
		}
		
		public function noteSortDescriptorChanged(SortDescriptor $sort=null)
		{
			$this->getData($sort);
		}

		public function numberOfRowsInTableView(Table $table)
		{
			if ($this->data === null)
			{
				$this->getData($table->getSortDescriptor());
			}
			return count($this->data);
		}

		public function objectValueForTableColumnAtRowIndex(Table $table, TableColumn $column, $rowIndex)
		{
			if ($this->data === null)
			{
				$this->getData($table->getSortDescriptor());
			}
			$key = $column->getKey();
			return $key == 'ROWID' 
					? $rowIndex
					: (isset($this->data[$rowIndex])
							? (isset($this->data[$rowIndex][$column->getKey()]) 
									? $this->data[$rowIndex][$column->getKey()] 
									: null)
							: null);
		}
	}
}