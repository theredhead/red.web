<?php

namespace red\data\sqlite
{
	abstract class SqliteAggregateFunction extends \red\Obj
	{
		// <editor-fold defaultstate="collapsed" desc="Property mixed Context">
		private $context = null;

		/**
		 * @return mixed
		 */
		protected function getContext()
		{
			return $this->context;
		}

		/**
		 * @param mixed $newContext
		 */
		private function setContext($newContext)
		{
			$this->context = $newContext;
		}
		// </editor-fold>

		abstract protected function step($rowNumber, $value);
		
		abstract protected function result($rowNumber);

		final public function execStep(&$context, $rowNumber, $value)
		{
			$this->setContext($context);
			return $this->step($rowNumber, $value);
		}
		
		final public function execResult(&$context, $rowNumber)
		{
			$this->setContext($context);
			return $this->result($rowNumber, $value);
		}
		
		public function getExecStepCallback()
		{
			return array($this, 'execStep');
		}

		public function getExecResultCallback()
		{
			return array($this, 'execStep');
		}
	}
	
//	class SqliteSum extends SqliteAggregateFunction
//	{
//		
//	}
	
	class SqliteConnection extends \red\data\SqlConnection
	{
		protected $log = null;
		protected function log($method, \red\data\SqlCommand $command)
		{
			if (!isset($this->log[$method]))
			{
				$this->log[$method] = array();
			}
			
			$message = array(
				  'commandText'	=> $command->getCommandText()
				, 'arguments'	=> count($command->getArguments()) > 0 ? $command->getArguments() : null);
			
			array_push($this->log[$method], $message);
		}
	
		public function __destruct()
		{
//			if (true)
//			{
//				var_dump($this->log);
//			}
			parent::__destruct();
		}
		
		// <editor-fold defaultstate="collapsed" desc="SqlConnection implementation">
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
			return new SqliteCommand($this, $commandText, $arguments);
		}

		/**
		 *
		 * @param Command $command
		 * @return \PDOStatement 
		 */
		protected function prepare(\red\data\SqlCommand $command)
		{
			$this->log(__METHOD__, $command);
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
			$this->log(__METHOD__, $command);
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
		// </editor-fold>

		// <editor-fold defaultstate="collapsed" desc="Sqlite specific functionality">
		/**
		 * Register a php function callback so that it can be used as a function inside sql statements.
		 * 
		 * Note: Callback functions should return a type understood by SQLite (i.e. scalar type).
		 *
		 * @param string $functionName
		 * @param callback $callback
		 * @param integer $numberOfArguments 
		 */
		public function registerUserFunction($functionName, $callback, $numberOfArguments)
		{
			$success = $this->pdo->sqliteCreateFunction($functionName, $callback, $numberOfArguments);
			if (! $success)
			{
				throw new \ErrorException(sprintf('Unable to register user function "%s"', $functionName));
			}
		}
		
		
//		bool PDO::sqliteCreateAggregate ( string $function_name , callback $step_func , callback $finalize_func [, int $num_args ] )

		public function registerAggregateFunction($functionName, SqliteAggregateFunction $function)
		{
//			$success = $this->pdo->sqliteCreateFunction($functionName, $callback, $numberOfArguments);
		}
		// </editor-fold>
	}
}

#EOF