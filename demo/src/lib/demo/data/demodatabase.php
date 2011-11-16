<?php

namespace demo\data
{
	use \red\data\SortDescriptor;

	class DemoDatabase extends \red\data\sqlite\SqliteConnection
	{
		public function __construct()
		{
			$dbFile = realpath($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, array('..', 'app-data', 'demo.sqlite')));
			$cnString = \red\data\SqlConnectionString::fromString(sprintf("sqlite:path='%s';", $dbFile));
			parent::__construct($cnString);
			$this->connect();
		}
		
		/**
		 * @return array
		 */
		public function getSchemaInfo(SortDescriptor $sort=null)
		{
			$commandText = sprintf('SELECT * FROM sqlite_master %s', $sort);
			return $this->createCommand($commandText)->executeTable();
		}
	}
}