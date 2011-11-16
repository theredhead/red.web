<?php

namespace red\web\ui\controls
{	
	use red\data\SortDescriptor;
	use \red\web\ui\html\HtmlTag;
	use \red\web\ui\html\HtmlTable;
	use \red\web\ui\html\HtmlTableHead;
	use \red\web\ui\html\HtmlTableBody;
	use \red\web\ui\html\HtmlTableFoot;
	use \red\web\ui\html\HtmlTableRow;
	use \red\web\ui\html\HtmlTableCell;
	use \red\web\ui\html\HtmlTableHeaderCell;
	use \red\web\ui\html\HtmlText;
	use \red\web\http\HttpRequest;
	use \red\EventArgument;
	
	require_once 'red/web/ui/html/elements.php';
	
	class CurrentPageIndexChangedEventArg extends EventArgument
	{
		
	}

	class Table extends BaseControl implements IPageable, IBindable
	{
		const EV_PAGEINDEX_CHANGED = 'PageIndexChanged';

		// <editor-fold defaultstate="collapsed" desc="State Properties">
		// <editor-fold defaultstate="collapsed" desc="Property boolean RenderEmptyRows">
		private $renderEmptyRows = false;

		/**
		 * @return boolean
		 */
		public function getRenderEmptyRows()
		{
			return $this->renderEmptyRows;
		}

		/**
		 * @param boolean $newRenderEmptyRows
		 */
		public function setRenderEmptyRows($newRenderEmptyRows)
		{
			$this->renderEmptyRows = $newRenderEmptyRows;
		}

		// </editor-fold>
		// <editor-fold defaultstate="collapsed" desc="Property integer PageSize">
		/**
		 * @return integer
		 */
		public function getPageSize()
		{
			return isset($this->state['pageSize'])
					? (int)$this->state['pageSize']
					: 20;
		}
		/**
		 * @param integer $newPageSize
		 */
		public function setPageSize($newPageSize)
		{
			$this->state['pageSize'] = (int)$newPageSize;
		}
		// </editor-fold>
		// <editor-fold defaultstate="collapsed" desc="Property integer CurrentPageIndex">

		/**
		 * @return integer
		 */
		public function getCurrentPageIndex()
		{
			return isset($this->state['currentPageIndex'])
					? (int)$this->state['currentPageIndex']
					: 0;
		}

		/**
		 * @param integer $newCurrentPageIndex
		 */
		public function setCurrentPageIndex($newCurrentPageIndex)
		{
			if ($this->state['currentPageIndex'] != $newCurrentPageIndex)
			{
				$this->state['currentPageIndex'] = $newCurrentPageIndex;
				
				$this->notifyListenersOfEvent(self::EV_PAGEINDEX_CHANGED, 
					new CurrentPageIndexChangedEventArg($newCurrentPageIndex));
			}
		}
		// </editor-fold>
		
		// </editor-fold>

		// <editor-fold defaultstate="collapsed" desc="Property string PagerClassName">
		private $pagerClassName = '\\red\\web\\ui\\controls\\Pager';

		/**
		 * @return string
		 */
		public function getPagerClassName()
		{
			return $this->pagerClassName;
		}

		/**
		 * @param string $newPagerClassName
		 */
		public function setPagerClassName($newPagerClassName)
		{
			if ($this->pagerClassName != $newPagerClassName)
			{
				$this->pagerClassName = $newPagerClassName;
				$this->pager = null;
				$this->getPager();
			}
		}

		// </editor-fold>
		// <editor-fold defaultstate="collapsed" desc="Property boolean ShowPager">
		private $showPager = true;

		/**
		 * @return boolean
		 */
		public function getShowPager()
		{
			return $this->showPager == true;
		}

		/**
		 * @param boolean $newShowPager
		 */
		public function setShowPager($newShowPager)
		{
			$this->showPager = $newShowPager == true;
		}

		// </editor-fold>
		// <editor-fold defaultstate="collapsed" desc="Property string CaptionText">
		private $captionText = '';

		/**
		 * @return string
		 */
		public function getCaptionText()
		{
			return $this->captionText;
		}

		/**
		 * @param string $newHeaderText
		 */
		public function setCaptionText($newCaptionText)
		{
			$this->captionText = $newCaptionText;
		}
		// </editor-fold>

		// <editor-fold defaultstate="collapsed" desc="Property SortDescriptor SortDescriptor">
		/**
		 * @return SortDescriptor
		 */
		public function getSortDescriptor()
		{
			return null;
		}
		// </editor-fold>
				
		// <editor-fold defaultstate="collapsed" desc="Property Pager Pager">
		private $pager = null;

		/**
		 * @return Pager
		 */
		public function getPager()
		{
			if ($this->pager === null)
			{
				$reflector = new \ReflectionClass($this->getPagerClassName());
				$pager = $reflector->newInstance();
				if ($pager instanceof Pager)
				{
					$pager->setForControl($this);
					$this->pager = $pager;
				}
				else
				{
					static::fail('Pager must inherit from \\red\\web\\ui\\controls\\Pager');
				}
			}
			return $this->pager;
		}

		/**
		 * @param Pager $newPager
		 */
		public function setPager(Pager $newPager)
		{
			$this->pager = $newPager;
		}
		// </editor-fold>

		public function __construct()
		{
			parent::__construct('table');
			$this->preBuildControl();
		}

		/**
		 * Get the number of items this table will provide access to
		 *
		 * @return integer
		 */
		public function getNumberOfItems()
		{
			$delegate = $this->getDelegate();

			if ($delegate instanceof ITableDatasourceDelegate)
			{
				return $delegate->numberOfRowsInTableView($this);
			}

			return 0;
		}
		
		/**
		 * expand logical properties from the template
		 *
		 * @param type $name
		 * @param type $value 
		 */
		public function setAttribute($name, $value)
		{
			switch(strtolower($name))
			{
				case 'pagesize' :
					$this->setPageSize($value);
					break;

				case 'currentpageindex' :
				case 'currentpage' :
				case 'pageindex' :
					$this->setCurrentPageIndex($value);
					break;

				case 'caption' :
				case 'captiontext' :
					$this->setCaptionText($value);
					break;

				case 'pager' :
				case 'pagerclass' :
					$this->setPagerClassName($value);
					break;

				case 'showpager' :
					$this->setShowPager(in_array(''.$value, array('1', 'true', 'yes', 'on')));
					break;
				
				case 'showemptyrows' :
					$this->setRenderEmptyRows(in_array($value, array('1', 1, true, 'true', 'yes', 'on')));
					break;
					

				default:
					parent::setAttribute($name, $value);
					break;
			}
		}
		
		// <editor-fold defaultstate="collapsed" desc="IControl implementation">
		/**
		 * @return \red\web\ui\WebPage
		 */
		public function getPage()
		{
			return $this->getOwnerDocument();
		}
		
		/**
		 * called by the page to give this table a chance to finish up its
		 * internal markup
		 */
		public function preRender()
		{
			parent::preRender();

			// @TODO: (IControl)$this->getThemeResource('table.css') ???
			$this->getPage()->registerStyleSheet('/css/table.css');

			// if the table has not been built manually, do it automatically last minute.
			if (! $this->isBuilt)
			{
				$this->buildControl();
			}
			if (!$this->getShowPager())
			{
				$this->getPager()->setIsVisible(false);
			}
		}

		/**
		 * called by the page when a postback event occurs that references this
		 * control
		 *
		 * @param type $eventName
		 * @param type $eventArgument 
		 */
		protected function notePostbackEvent($eventName, $eventArgument)
		{
			parent::notePostbackEvent($eventName, $eventArgument);
		}

		// </editor-fold>

		/**
		 * IBindable::canBindTo
		 *
		 * @param ITableDatasourceDelegate $dataItem
		 * @return type 
		 */
		public function canBindTo($dataItem)
		{
			return $dataItem instanceof ITableDatasourceDelegate;
		}
		
		/**
		 * IBindable::bind
		 *
		 * @param ITableDatasourceDelegate $dataItem 
		 */
		public function bind($dataItem)
		{
			$this->canBindTo($dataItem) or $this->fail('Cannot bind to %s', typeid($dataItem));
			$this->setDelegate($dataItem);
			$this->buildControl();
			$this->isBound = true;
		}
		/**
		 * flag indicating wether this table has been databound
		 *
		 * @var boolean
		 */
		protected $isBound = false;
		
		/**
		 * See if this table is databound
		 *
		 * @return type 
		 */
		public function isBound()
		{
			return $this->isBound;
		}
		
		
		// <editor-fold defaultstate="collapsed" desc="Datasource delegate">
		/**
		 *
		 * @var ITableDatasourceDelegate
		 */
		protected $delegate = null;

		/**
		 * Get the delegate responsible for maintaining the data associated 
		 * with this table
		 *
		 * @return ITableDatasourceDelegate
		 */
		protected function getDelegate()
		{
			if ($this->delegate === null)
			{
				$this->delegate = $this->findFirst(function($o){return $o instanceof ITableDatasourceDelegate;});
			}
			if ($this->delegate === null)
			{
				static::fail('No ITableDatasourceDelegate found');
			}
			return $this->delegate;
		}
		// </editor-fold>
		// <editor-fold defaultstate="collapsed" desc="Columns">
		/**
		 * the columns inside this table
		 *
		 * @var \red\xml\XMLNodeList
		 */
		protected $columns = null;
		
		/**
		 * Get the columns that are used to render this table
		 *
		 * @return \red\xml\XMLNodeList
		 */
		public function getColumns()
		{
			if ($this->columns === null)
			{
				$this->columns = $this->findAll(function($o){return $o instanceof TableColumn;});
			}
			return $this->columns;
		}
		// </editor-fold>
		// <editor-fold defaultstate="collapsed" desc="Building the control.">
		
		protected $head;
		protected $body;
		protected $foot;
		
		/**
		 * gets called early on in the init step of the request handling
		 * so that the table may find or create the child controls it depends 
		 * on for certain events
		 */
		protected function preBuildControl()
		{
			$this->head = $this->appendChild(new HtmlTableHead());
			$this->foot = $this->appendChild(new HtmlTableFoot());
			$this->body = $this->appendChild(new HtmlTableBody());;
			
			$this->preBuildTableFooter();
		}
	
		/**
		 * A flag indicating wether this control has been built fully
		 *
		 * @var boolean
		 */
		protected $isBuilt = false;
		
		/**
		 * build the controls' internal markup
		 */
		public function buildControl()
		{
			$columns = $this->getColumns();
			$delegate = $this->getDelegate();

			if ($this->getCaptionText() != null)
			{
				$caption = new HtmlTag('caption');
				$caption->appendChild(new HtmlText($this->getCaptionText()));
				$this->appendChild($caption);
			}
			
			$this->buildColumnHeaders();
			$this->buildTableBody();
			$this->buildTableFooter();

			$this->isBuilt = true;
		}
		
		/**
		 * Build column headers
		 *
		 * @param HtmlTableHead $head 
		 */
		protected function buildColumnHeaders()
		{
			$row = $this->head->appendChild(new HtmlTableRow());
			foreach($this->getColumns() as $ix => $column)
			{
				$cell = $row->appendChild(new HtmlTableHeaderCell());
				$cell->appendChild(new HtmlText($column->getHeaderText()));
				$this->headerCellCreated($cell, $column);
			}
		}
		/**
		 * Build the body
		 * 
		 * @param HtmlTableBody $body
		 */
		protected function buildTableBody()
		{
			$delegate = $this->getDelegate();

			if ($delegate instanceof ITableDatasourceDelegate)
			{
				$pageSize      = $this->getPageSize();
				$currentPage   = $this->getCurrentPageIndex();
				$numberOfRows  = $delegate->numberOfRowsInTableView($this);
				$numberOfPages = ceil($numberOfRows / $pageSize);
				$startRowIndex = $pageSize * $currentPage;
				$endRowIndex   = $startRowIndex + $pageSize;
				
				for($i = $startRowIndex; $i < $endRowIndex; $i ++)
				{
					if ($i < $numberOfRows || $this->getRenderEmptyRows())
					{
						$this->body->appendChild($this->buildRowAtIndex($i));
					}
				}
			}
		}
		
		/**
		 * Get the TableColumn at the $index-th position in this table
		 *
		 * @param integer $index
		 * @return TableColumn
		 */
		protected function findColumnByIndex($index)
		{
			$columns = $this->getColumns();
			return isset($columns[$index]) ? $columns[(integer)$index]
					: static::fail('Column not found');
		}

		/**
		 * Get the index inside this table for $column
		 *
		 * @param TableColumn $column
		 * @return int 
		 */
		protected function findIndexByColumn(TableColumn $column)
		{
			$columns = $this->getColumns();
			
			// cannot foreach this because it resets the pointer causing outside searcg
			// never to finish because of inside searches while building headers.
			for($ix = 0; $ix < count($columns); $ix ++)
			{
				if ($columns[$ix]->isSameInstance($column))
				{
					return $ix;
				}
			}
			
			static::fail('That\'s not one of mine!');
		}

		/**
		 * gets called whenever a header cell is created inside buildControl
		 *
		 * @param HtmlTableHeaderCell $cell
		 * @param TableColumn $column 
		 */
		protected function headerCellCreated(HtmlTableHeaderCell $cell, TableColumn $column)
		{
		}

		/**
		 * gets called whenever a data cell is created inside buildControl
		 *
		 * @param HtmlTableCell $cell
		 * @param TableColumn $column
		 * @param type $rowIndex 
		 */
		protected function cellCreated(HtmlTableCell $cell, TableColumn $column, $rowIndex)
		{
		}

		/**
		 * Gets called whenever a row is created inside buildControl
		 *
		 * @param HtmlTableRow $row
		 * @param type $rowIndex 
		 */
		protected function rowCreated(HtmlTableRow $row, $rowIndex)
		{
		}


		/**
		 * @param HtmlTableRow $row
		 * @param integer $rowIndex
		 * @return HtmlTableRow 
		 */
		protected function buildRowAtIndex($rowIndex)
		{
			$row = new HtmlTableRow();
			$delegate = $this->getDelegate();

			foreach($this->getColumns() as $columnIndex => $column)
			{
				$cell = $row->appendChild(new HtmlTableCell());
				$value = $delegate->objectValueForTableColumnAtRowIndex($this, $column, $rowIndex);
				$content = $column->buildCellContent($value);
				$cell->appendChild($content);
				$this->cellCreated($cell, $column, $rowIndex);
			}
			$this->rowCreated($row, $rowIndex);
			return $row;
		}
		
		/**
		 * allows the table to attach the pager so it can register events during
		 * the init phase of request processing. (later would be too late)
		 */
		protected function preBuildTableFooter()
		{
			$row = $this->foot->appendChild(new HtmlTableRow());
			$cell = $row->appendChild(new HtmlTableCell()); 

			if ($this->getShowPager())
			{
				$pager = $this->getPager();
				$cell->appendChild($pager);
			}
			else
			{
				$cell->appendChild(new HtmlText(''));
			}
		}
		
		/**
		 * build the table footer during buildControl.
		 */
		protected function buildTableFooter()
		{
			// we have to do this here because diring the preBuild phase
			// we don't know how many columns there may end up being.
			$cell = $this->getPager()->getParentNode();
			if ($cell instanceof HtmlTableCell)
			{
				$cell->setAttribute('colspan', ''.count($this->getColumns()));
			}
		}

		// </editor-fold>
	}
}

#EOF