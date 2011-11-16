<?php

namespace red\web\ui\html
{
	use red\MBString;
	use red\xml\XMLText;
	use red\xml\XMLLiteral;
	// <editor-fold defaultstate="collapsed" desc="HTML Document anatomy">
	
	/**
	 * Represents the head tag of an html document: /html/head 
	 */
	class HtmlHead extends HtmlTag
	{
		/**
		 * /html/head/title
		 * 
		 * @var HtmlTitle
		 */
		protected $title;
		
		/**
		 * Find this elements title element or create one if it does not exists
		 * 
		 * @return HtmlTitle
		 */
		public function getTitleElement()
		{
			if ($this->title === null)
			{
				$this->title = $this->findFirst(function($o){return $o instanceof XMLElement && $o->getTagName() == 'title';});
			}
			if ($this->title === null)
			{
				$this->title = $this->appendChild(new HtmlTitle());
			}
			return $this->title;
		}
		
		public function __construct()
		{
			parent::__construct('head');
		}
	}
	
	/**
	 * Represents the head tag of an html document: /html/head 
	 */
	class HtmlMeta extends HtmlTag
	{
		public function __construct()
		{
			parent::__construct('meta');
		}
	}
	
	/**
	 * represents /html/head/title 
	 */
	class HtmlTitle extends HtmlTag
	{	
		public function __construct()
		{
			parent::__construct('title');
		}
		
		/**
		 * Get the text content of this title tag
		 *
		 * @return MBString
		 */
		public function getText()
		{
			// @todo: make this handle all possible scenarios
			
			if (! $this->hasChildren())
			{
				return null;
			}
			else if ($this->firstChild() instanceof XMLText || $this->firstChild() instanceof XMLLiteral)
			{
				return $this->firstChild()->getTextContent();
			}
			else if ($this->countChildren() > 0)
			{
				static::fail('getText not supported on title tags where setText was not used to set the text content');
			}
		}

		/**
		 * set the text content for this title tag
		 * 
		 * @param MBString $text 
		 */
		public function setText(MBString $text)
		{
			$this->clear();
			$this->appendChild(new XMLText($text));
		}
	}
	
	/**
	 * represents /html/body
	 */
	class HtmlBody extends HtmlTag
	{
		public function __construct()
		{
			parent::__construct('body');
		}
	}
	// </editor-fold>

	// <editor-fold defaultstate="collapsed" desc="Lists">
	/**
	 * represents a <ul> tag
	 */
	class HtmlUnorderedList extends HtmlTag
	{
		public function __construct()
		{
			parent::__construct('ul');
		}
	}

	/**
	 * represents an <ol> tag
	 */
	class HtmlOrderedList extends HtmlTag
	{
		public function __construct()
		{
			parent::__construct('ol');
		}
	}
	
	/**
	 * represents a <li> tag
	 */
	class HtmlListItem extends HtmlTag
	{
		public function __construct()
		{
			parent::__construct('li');
		}
	}
	// </editor-fold>

	// <editor-fold defaultstate="collapsed" desc="Layers">
	class HtmlDiv extends HtmlTag
	{
		public function __construct()
		{
			parent::__construct('div');
		}
	}

	class HtmlSpan extends HtmlTag
	{
		public function __construct()
		{
			parent::__construct('span');
		}
	}
	// </editor-fold>
	
	/**
	 * represents a literal piece of text, not treated for special characters)
	 */
	class HtmlLiteral extends XMLLiteral
	{
	}

	/**
	 * represents a piece of text 
	 */
	class HtmlText extends XMLText
	{
	}
	
	/**
	 * represents a <p> tag
	 */
	class HtmlParagraph extends HtmlTag
	{
		public function __construct()
		{
			parent::__construct('p');
		}
	}

	/**
	 * represents an <a> tag
	 */
	class HtmlAnchor extends HtmlTag
	{
		public function __construct()
		{
			parent::__construct('a');
		}
	}
	
	/**
	 * represents an <img> tag 
	 */
	class HtmlImage extends HtmlTag
	{
		public function __construct()
		{
			parent::__construct('img');
		}
	}

	/**
	 * represents a <br> tag 
	 */
	class HtmlLineBreak extends HtmlTag
	{
		public function __construct()
		{
			parent::__construct('br');
		}
	}
	
	// <editor-fold defaultstate="collapsed" desc="Headings (h1 .. h6)">
	abstract class HtmlHeading extends HtmlTag
	{
		protected static $level = '1';
		public function __construct()
		{
			assert(static::$level > 0 && static::$level < 7);
			parent::__construct('h'.static::$level);
		}
	}
	class HtmlHeading1 extends HtmlHeading
	{
		protected static $level = '1';
	}
	class HtmlHeading2 extends HtmlHeading
	{
		protected static $level = '2';
	}
	class HtmlHeading3 extends HtmlHeading
	{
		protected static $level = '3';
	}
	class HtmlHeading4 extends HtmlHeading
	{
		protected static $level = '4';
	}
	class HtmlHeading5 extends HtmlHeading
	{
		protected static $level = '5';
	}
	class HtmlHeading6 extends HtmlHeading
	{
		protected static $level = '6';
	}
	// </editor-fold>
	
	// <editor-fold defaultstate="collapsed" desc="Table elements">
	class HtmlTable extends HtmlTag
	{
		public function __construct()
		{
			parent::__construct('table');
		}
	}	
	class HtmlTableRow extends HtmlTag
	{
		public function __construct()
		{
			parent::__construct('tr');
		}
	}
	class HtmlTableCell extends HtmlTag
	{
		public function __construct()
		{
			parent::__construct('td');
		}
	}
	class HtmlTableHeaderCell extends HtmlTag
	{
		public function __construct()
		{
			parent::__construct('th');
		}
	}
	class HtmlTableHead extends HtmlTag
	{
		public function __construct()
		{
			parent::__construct('thead');
		}
	}
	class HtmlTableBody extends HtmlTag
	{
		public function __construct()
		{
			parent::__construct('tbody');
		}
	}
	class HtmlTableFoot extends HtmlTag
	{
		public function __construct()
		{
			parent::__construct('tfoot');
		}
	}
	// </editor-fold>
}

#EOF