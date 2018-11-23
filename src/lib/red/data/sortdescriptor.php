<?php

namespace red\data
{
	use red\Obj;
	
	class SortDescriptor extends Obj
	{
		// <editor-fold defaultstate="collapsed" desc="Property string ColumnName">
		private $columnName = null;

		/**
		 * @return string
		 */
		public function getColumnName()
		{
			return $this->columnName;
		}

		/**
		 * @param string $newColumnName
		 */
		public function setColumnName($newColumnName)
		{
			$this->columnName = $newColumnName;
		}
		// </editor-fold>
		// <editor-fold defaultstate="collapsed" desc="Property string Direction">
		private $direction = null;

		/**
		 * @return string
		 */
		public function getDirection()
		{
			return $this->direction == 'ASC' ? 'ASC' : 'DESC';
		}

		/**
		 * @param string $newDirection
		 */
		public function setDirection($newDirection)
		{
			$this->direction = $newDirection;
		}
		// </editor-fold>

		public function __construct($columnName=null, $direction=null)
		{
			parent::__construct();
			$this->setColumnName($columnName);
			$this->setDirection($direction);
		}
		
		public function toggle()
		{
			$this->setSortDirection(
					$this->getSortDirection() == 'DESC' ? 'ASC' : 'DESC');
		}
		
		public function toString()
		{
			if (strlen($this->getColumnName()) > 0)
			{
				return sprintf(' ORDER BY `%s` %s', $this->getColumnName(), $this->getDirection());
			}
			return '';
		}
	}
}