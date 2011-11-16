<?php

namespace red\data\mysql
{
	class MySqlConnection extends \red\data\SqlConnection
	{
		/**
		 * @var \PDO
		 */
		protected $pdo = null;

		/**
		 * Create a MySqlCommand associated with this connection.
		 * 
		 * @param type $commandText
		 * @param array $arguments
		 * @return MySqlCommand 
		 */
		public function createCommand($commandText, array $arguments=array())
		{
			return new MySqlCommand($this, $commandText, $arguments);
		}

		/**
		 *
		 * @param Command $command
		 * @return \PDOStatement 
		 */
		protected function prepare(\red\data\SqlCommand $command)
		{
			return $this->pdo->prepare($command->getCommandText());
		}
		
		/**
		 * Execute the given command
		 * 
		 * @param Command $command
		 * @return \PDOStatement 
		 */
		public function execute(\red\data\SqlCommand $command)
		{
			$stmt = $this->prepare($command);
			$stmt->execute($command->getArguments());
			return $stmt;
		}
		
		/**
		 * Execute $command, return the 1st column of the 1st row of the 1st table in the result
		 *
		 * @param Command $command
		 * @param integer $columnIndex override the column index, defaults to 0 for first column
		 * @return string 
		 */
		public function executeScalar(\red\data\SqlCommand $command, $columnIndex=0)
		{
			return $this->execute($command)->fetchColumn($columnIndex);
		}

		/**
		 * Execute $command, return the 1st row of the 1st table in the result
		 *
		 * @param Command $command
		 * @return array[string] 
		 */
		public function executeRow(\red\data\SqlCommand $command)
		{
			return $this->execute($command)->fetch(\PDO::FETCH_ASSOC);
		}
		
		/**
 		 * Execute $command, return the 1st table in the result
		 *
		 * @param Command $command
		 * @return type 
		 */
		public function executeTable(\red\data\SqlCommand $command)
		{
			return $this->execute($command)->fetchAll(\PDO::FETCH_ASSOC);
		}
		
		/**
		 * Execute $command, return all tables in the result
		 *
		 * @param Command $command
		 * @return array 
		 */
		public function executeTableSet(\red\data\SqlCommand $command)
		{
			$dataset = array();
			$reader = $this->execute($command);
			do
			{
				array_push($dataset, $this->execute($command)->fetchAll(\PDO::FETCH_ASSOC));
			}
			while($reader->nextRowset());
			return $dataset;
		}
		
		/**
		 * Execute $command, return the executed statement
		 *
		 * @param Command $command
		 * @return \PDOStatement
		 */
		public function executeReader(\red\data\SqlCommand $command)
		{
			return $this->execute($command);
		}

		public function executeNonQuery(\red\data\SqlCommand $command)
		{
			$stmt = $this->execute($command);
			return $stmt->rowCount();
		}
		
		public function __construct($connectionString=null)
		{
			parent::__construct();
			
			if ($connectionString !== null)
			{
				if (! $connectionString instanceof \red\data\SqlConnectionString)
				{
					$connectionString = \red\data\SqlConnectionString::fromString($connectionString);
				}
				$this->setConnectionString($connectionString);
			}
		}
		
		public function connect()
		{
			$this->pdo = $this->getConnectionString()->createPDO();			
		}
		
		
		public function beginTransaction()
		{
			return $this->pdo->beginTransaction();
		}

		public function commit()
		{
			return $this->pdo->commit();
		}

		public function rollback()
		{
			return $this->pdo->rollBack();
		}
	}
}

#EOF