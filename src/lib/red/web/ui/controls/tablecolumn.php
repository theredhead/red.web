<?php

namespace red\web\ui\controls
{	
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
	
	require_once 'red/web/ui/html/elements.php';
	
	/**
	 * TableColumn is suitable for rendering strings and other scalars.
	 */
	class TableColumn extends BaseControl
	{
		public function __construct()
		{
			parent::__construct();
			$this->setIsVisible(false);
		}
		
		public function getTable()
		{
			do {
				$result = !isset($result) ? $this : $result->parentNode();
			} while($result != null && ! $result instanceof Table);
			
			return $result;
		}

		// <editor-fold defaultstate="collapsed" desc="Property string HeaderText">
		private $headerText = 'Column';

		/**
		 * @return string
		 */
		public function getHeaderText()
		{
			return $this->headerText;
		}

		/**
		 * @param string $newHeaderText
		 */
		public function setHeaderText($newHeaderText)
		{
			$this->headerText = $newHeaderText;
		}
		// </editor-fold>
		// <editor-fold defaultstate="collapsed" desc="Property string Key">
		private $key = null;

		/**
		 * @return string
		 */
		public function getKey()
		{
			return $this->key;
		}

		/**
		 * @param string $newKey
		 */
		public function setKey($newKey)
		{
			$this->key = $newKey;
		}
		// </editor-fold>
		// <editor-fold defaultstate="collapsed" desc="Property string NullRepresentation">
		private $nullRepresentation = 'null';

		/**
		 * @return HtmlTag
		 */
		public function getNullRepresentation()
		{
			if($this->nullRepresentation instanceof \red\MBString || is_string($this->nullRepresentation))
			{
				$element = new HtmlTag('span');
				$element->addCssClass('null-representation');
				$element->appendChild(new HtmlText($this->nullRepresentation));
				
				$this->nullRepresentation = $element;
			}
			return $this->nullRepresentation;
		}

		/**
		 * @param HtmlTag|string $newNullRepresentation
		 */
		public function setNullRepresentation($newNullRepresentation)
		{
			$this->nullRepresentation = $newNullRepresentation;
		}

		// </editor-fold>
		// <editor-fold defaultstate="collapsed" desc="Property boolean Sortable">
		private $isSortable = true;

		/**
		 * @return string
		 */
		public function isSortable()
		{
			return $this->isSortable == true;
		}

		/**
		 * @param string $newIsSortable
		 */
		public function setSortable($newIsSortable)
		{
			$this->isSortable = $newIsSortable == true;
		}
		// </editor-fold>

		
		/**
		 * @param mixed $value
		 */
		public function buildCellContent($value)
		{
			if ($value === null)
			{
				return $this->getNullRepresentation();
			}
			return new HtmlText($value.'');
		}
		
		public function setAttribute($name, $value)
		{
			switch(strtolower($name))
			{
				case 'headertext' :
					$this->setHeaderText($value);
					break;
				case 'key' :
					$this->setKey($value);
					break;
				case 'sortable' :
					$this->setSortable($value);
					break;
				default:
					parent::setAttribute($name, $value);
					break;
			}
		}
	}
}

#EOF