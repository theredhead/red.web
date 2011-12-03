<?php

namespace red\web\ui\controls
{
	use \red\EventArgument;
	use \red\Convert;
	use \red\data\SortDescriptor;
	use \red\web\ui\html\HtmlTableRow;
	use \red\web\ui\html\HtmlTableCell;
	use \red\web\ui\html\HtmlTableHeaderCell;

	/**
	 * The event argument used to indicate a header cell was clicked.
	 * 
	 * Contains a reference to the column the header that was clicked belongs 
	 * to in the Column property.
	 */
	class HeaderClickedEventArgument extends EventArgument
	{
		// <editor-fold defaultstate="collapsed" desc="Property TableColumn Column">
		private $column = null;
		/**
		 * @return TableColumn
		 */
		public function getColumn()
		{
			return $this->column;
		}

		/**
		 * @param TableColumn $newColumn
		 */
		private function setColumn(TableColumn $newColumn)
		{
			$this->column = $newColumn;
		}
		// </editor-fold>
		
		public function __construct(TableColumn $column)
		{
			$this->setColumn($column);
			parent::__construct();
		}
	}

	/**
	 * The event argument used to indicate a header cell was clicked.
	 * 
	 * Contains a reference to the column the cell that was clicked belongs 
	 * to in the Column property, a reference to the DataItem that was in the
	 * cell that got clicked in the DataItem property and the row index the 
	 * cell that got clicked is inside of in the RowIndex property .
	 */
	class CellClickedEventArgument extends EventArgument
	{
		// <editor-fold defaultstate="collapsed" desc="Property TableColumn Column">
		private $column = null;
		/**
		 * @return TableColumn
		 */
		public function getColumn()
		{
			return $this->column;
		}

		/**
		 * @param TableColumn $newColumn
		 */
		private function setColumn(TableColumn $newColumn)
		{
			$this->column = $newColumn;
		}
		// </editor-fold>
		// <editor-fold defaultstate="collapsed" desc="Property integer RowIndex">
		private $rowIndex = null;

		/**
		 * @return integer
		 */
		public function getRowIndex()
		{
			return $this->rowIndex;
		}

		/**
		 * @param integer $newRowIndex
		 */
		private function setRowIndex($newRowIndex)
		{
			$this->rowIndex = $newRowIndex;
		}
		// </editor-fold>
		// <editor-fold defaultstate="collapsed" desc="Property mixed DataItem">
		private $dataItem = null;

		/**
		 * @return mixed
		 */
		public function getDataItem()
		{
			return $this->dataItem;
		}

		/**
		 * @param mixed $newDataItem
		 */
		public function setDataItem($newDataItem)
		{
			$this->dataItem = $newDataItem;
		}
		// </editor-fold>

		public function __construct(TableColumn $column, $rowIndex, $dataItem)
		{
			parent::__construct();
			$this->setColumn($column);
			$this->setRowIndex($rowIndex);
			$this->setDataItem($dataItem);
		}
	}
	
	/**
	 * DataGrid presents more feature rich table exprerience than the Table control.
	 */
	class DataGrid extends Table implements IPublishEvents
	{
		const EV_CELL_CLICKED = 'CellClicked';
		const EV_HEADERCELL_CLICKED = 'HeaderCellClicked';
		const EV_SELECTEDINDEX_CHANGED = 'SelectedIndexChanged';

		// <editor-fold defaultstate="collapsed" desc="Property boolean AutoPostback">
		private $autoPostback = true;

		/**
		 * @return boolean
		 */
		public function getAutoPostback()
		{
			return $this->autoPostback == true;
		}

		/**
		 * @param boolean $newAutoPostback
		 */
		public function setAutoPostback($newAutoPostback)
		{
			$this->autoPostback = $newAutoPostback == true;
		}
		// </editor-fold>
		// <editor-fold defaultstate="collapsed" desc="Property integer SelectedIndex">
		/**
		 * @return integer
		 */
		public function getSelectedIndex()
		{
			return isset($this->state['selIx']) ? $this->state['selIx'] : null;
		}

		/**
		 * @param integer $newSelectedIndex
		 */
		public function setSelectedIndex($newSelectedIndex)
		{
			if ($newSelectedIndex !== $this->getSelectedIndex())
			{
				if ($newSelectedIndex === false)
				{
					unset($this->state['selIx']);
					$this->selectedIndexChanged();
				}
				else if ((integer)$newSelectedIndex == $newSelectedIndex)
				{
					$this->state['selIx'] = (integer)$newSelectedIndex;
					$this->selectedIndexChanged();
				}
				else
				{
					static::fail('SelectedIndex must be an integral value, or null');
				}
			}
		}

		/**
		 * Notifies listeners that the selected index of this repeater has changed.
		 */
		protected function selectedIndexChanged()
		{
			$this->notifyListenersOfEvent(self::EV_SELECTEDINDEX_CHANGED, new EventArgument());
		}
		// </editor-fold>
		// <editor-fold defaultstate="collapsed" desc="Property boolean AutoSetSelectedIndexOnCellClick">
		/**
		 * @return boolean
		 */
		public function doesAutoSetSelectedIndexOnCellClick()
		{
			return $this->state['autoSel'] == 'on';
		}

		/**
		 * @param boolean $newAutoSetSelectedIndexOnCellClick
		 */
		public function setAutoSetSelectedIndexOnCellClick($newAutoSetSelectedIndexOnCellClick)
		{
			$this->state['autoSel'] = (Convert::toBoolean($newAutoSetSelectedIndexOnCellClick))
					? 'on'
					: 'off';
		}
		// </editor-fold>
		
		/**
		 * IPublishEvents::getPublishedEvents
		 *
		 * @return array
		 */
		public function getPublishedEvents()
		{
			return array(self::EV_CELL_CLICKED
						,self::EV_HEADERCELL_CLICKED
						,self::EV_SELECTEDINDEX_CHANGED);
		}
		
		public function __construct()
		{
			parent::__construct();
			// share css styles with red.web.ui.controls.Table
			$this->addCssClass('Table');
			$this->setAutoSetSelectedIndexOnCellClick(false);
		}

		// <editor-fold defaultstate="collapsed" desc="State Properties">
		/**
		 * @return TableColumn
		 */
		public function getSortColumn()
		{
			return isset($this->state['sortCol'])
				? $this->findColumnByIndex((integer)$this->state['sortCol'])
				: null;
		}
		public function setSortColumn(TableColumn $column)
		{
			$this->state['sortCol'] = $this->findIndexByColumn($column);
		}
		public function getSortDirection()
		{
			return $this->state['sortDir'] == 'DESC' ? 'DESC' : 'ASC';
		}
		public function setSortDirection($direction)
		{
			$this->state['sortDir'] = $direction == 'DESC' ? 'DESC' : 'ASC';
		}

		public function getSortDescriptor()
		{
			$column = $this->getSortColumn();
			
			if ($column != null)
			{
				$key = $column->getKey();
				return new SortDescriptor($key, $this->getSortDirection());
			}
			return null;
		}
		// </editor-fold>

		/**
		 * Gets called whenever a header cell got created during buildControl
		 *
		 * @param HtmlTableHeaderCell $cell
		 * @param TableColumn $column 
		 */
		protected function headerCellCreated(HtmlTableHeaderCell $cell, TableColumn $column)
		{
			$sortCol = $this->getSortColumn();

			if ($sortCol !== null &&  $column->isSameInstance($sortCol))
			{
				$cell->addCssClass('SortIndicator');
				$cell->addCssClass($this->getSortDirection());
			}
            else if ($column->isSortable())
            {
                $cell->addCssClass('Sortable');
            }

			$cell->setAttribute('onclick', 
					$this->createClientEventTrigger(
						self::EV_HEADERCELL_CLICKED, $this->findIndexByColumn($column)));
		}

		/**
		 * Gets called whenever a data cell got created during buildControl
		 *
		 * @param HtmlTableCell $cell
		 * @param TableColumn $column
		 * @param integer $rowIndex 
		 */
		protected function cellCreated(HtmlTableCell $cell, TableColumn $column, $rowIndex)
		{
			$cell->setAttribute('onclick', 
					$this->createClientEventTrigger(
						self::EV_CELL_CLICKED, $rowIndex .':'. $this->findIndexByColumn($column)));
		}

		/**
		 * Gets called whenever a row got created during buildControl
		 *
		 * @param HtmlTableRow $row
		 * @param type $rowIndex 
		 */
		protected function rowCreated(HtmlTableRow $row, $rowIndex)
		{
			if ($this->getSelectedIndex() !== null && $rowIndex == $this->getSelectedIndex())
			{
				$row->addCssClass('selected');
			}
		}

		/**
		 * propagates the EV_HEADERCELL_CLICKED event
		 *
		 * @param TableColumn $column 
		 */
		protected function headerClicked(TableColumn $column)
		{
			if ($column->isSortable())
			{
				$sortCol = $this->getSortColumn();

				if ($sortCol !== null && $sortCol->isSameInstance($column))
				{
					$this->setSortDirection($this->getSortDirection() == 'DESC' ? 'ASC' : 'DESC');
				}
				else
				{
					$this->setSortColumn($column);
				}
				$this->getDelegate()->noteSortDescriptorChanged($this->getSortDescriptor());
			}
			$this->notifyListenersOfEvent(self::EV_HEADERCELL_CLICKED,
					new HeaderClickedEventArgument($column));
		}

		/**
		 * propagates the EV_CELL_CLICKED event
		 *
		 * @param TableColumn $column
		 * @param type $rowIndex 
		 */
		protected function cellClicked(TableColumn $column, $rowIndex)
		{
			$valueObject = $this->getDelegate()->objectValueForTableColumnAtRowIndex($this, $column, $rowIndex);
			
			if ($this->doesAutoSetSelectedIndexOnCellClick())
			{
				$this->setSelectedIndex($rowIndex);
			}

			$this->notifyListenersOfEvent(self::EV_CELL_CLICKED, 
					new CellClickedEventArgument($column, $rowIndex, $valueObject));
		}
		
		/**
		 * called by the framework when a postback event occured for this control
		 *
		 * @param string $eventName
		 * @param string $eventArgument 
		 */
		protected function notePostbackEvent($eventName, $eventArgument)
		{
			switch($eventName)
			{
				case self::EV_HEADERCELL_CLICKED :
					$column = $this->findColumnByIndex((int)$eventArgument);
					$this->headerClicked($column);
					break;
				
				case self::EV_CELL_CLICKED :
					list($rowIx, $colIx) = explode(':', $eventArgument);
					$column = $this->findColumnByIndex((int)$colIx);
					$this->cellClicked($column, $rowIx);
					break;

				default:
					parent::notePostbackEvent($eventName, $eventArgument);
					break;
			}
		}
	}
}

#EOF