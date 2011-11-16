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
	class RowIndicatorColumn extends TableColumn
	{
		public function __construct()
		{
			parent::__construct();
			$this->setHeaderText('');
			$this->setKey('ROWID');
		}

		public function buildCellContent($value)
		{
			$span = new HtmlTag('span');
			$span->addCssClass('RowIndicator');
			$span->appendChild(new HtmlText($value));
			return $span;
		}
	}
}