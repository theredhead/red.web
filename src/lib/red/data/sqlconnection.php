<?php

namespace red\data
{
	use red\Obj;

	abstract class SqlConnection extends Obj
	{
		/**
		 * @var SqlConnectionString
		 */
		protected $connectionString = null;
		/**
		 * Set the connection string to use for this connection
		 * 
		 * @param string $newConnectionString
		 */
		public function setConnectionString(SqlConnectionString $newConnectionString)
		{
			$this->connectionString = $newConnectionString;
			$this->connect();
		}

		/**
		 * Get the connectionString used for this connection
		 *
		 * @return SqlConnectionString
		 */
		public function getConnectionString()
		{
			return $this->connectionString;
		}

		abstract protected function connect();
		
		/**
		 * Create a parameterized SqlCommand
		 *
		 * @param string $commandText
		 * @param array $arguments
		 * @return SqlCommand
		 */
		abstract public function createCommand($commandText, array $arguments=array());

		/**
		 * Execute $command and return an engine driver native resource
		 *
		 * @return mixed
		 */
		abstract public function execute(SqlCommand $command);

		/**
		 * Execute $command and return the value of the first column of the first row returned
		 *
		 * @return string
		 */
		abstract public function executeScalar(SqlCommand $command);

		/**
		 * Execute $command and return an associative array of the first row returned
		 *
		 * @return array row
		 */
		abstract public function executeRow(SqlCommand $command);

		/**
		 * Execute $command and return an array of associative arrays for the entire resultset
		 *
		 * @return array[array] table
		 */
		abstract public function executeTable(SqlCommand $command);

		/**
		 * Execute $command and return an arraf of arrays of associative arrays for the entire resultset
		 *
		 * @return array[array[array]] list of tables
		 */
		abstract public function executeTableSet(SqlCommand $command);

		/**
		 * Execute $command and return the number of rows affected
		 *
		 * @return integer number of rows affected
		 */
		abstract public function executeNonQuery(SqlCommand $command);
	}
}

#EOF