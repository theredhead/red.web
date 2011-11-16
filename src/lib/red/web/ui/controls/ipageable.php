<?php

namespace red\web\ui\controls
{	
	interface IPageable
	{
		public function getPageSize();
		public function getNumberOfItems();
		public function getCurrentPageIndex();
		public function setCurrentPageIndex($newCurrentPageIndex);
	}
}

#EOF
