<?php

namespace red\web\http
{
	use \red\IPlugin;

	interface IHttpApplicationPlugin extends IPlugin
	{
		public function setApplication($application);
	}
}