<?php

namespace red\data
{
	use \red\data\sqlite\SqliteConnection;
	use \PDO;
	
	class DataStore extends SqliteConnection
	{
		/**
		 * connect to a sqlite file
		 */
		public function connect($fileName=':memory:')
		{
			$this->pdo = new PDO(sprintf('sqlite:%s', $fileName));
			$this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		}
	}
}