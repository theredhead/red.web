<?php

namespace red\web\ui\controls
{
	class Form extends BaseControl
	{
		public function __construct()
		{
			parent::__construct('form');

			$this->setAttribute('action', $_SERVER['REQUEST_URI']);
			$this->setAttribute('method', 'post');
			$this->setAttribute('enctype', 'multipart/form-data');
		}
	}
}