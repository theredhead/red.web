<?php

namespace red\web\ui\controls
{
	interface IBindable
	{
		public function canBindTo($dataItem);
		public function isBound();
		public function bind($dataItem);
	}
}

#EOF