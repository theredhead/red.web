<?php

namespace red\data
{
	/**
	 * Represents a SQL statement
	 */
	abstract class SqlCommand extends \red\Object
	{
		// <editor-fold defaultstate="collapsed" desc="Property SqlConnection Connection">
		private $connection = null;

		/**
		 * @return SqlConnection
		 */
		public function getConnection()
		{
			return $this->connection;
		}

		/**
		 * @param SqlConnection $newConnection
		 */
		public function setConnection($newConnection)
		{
			$newConnection instanceof SqlConnection or static::fail(__METHOD__ . 'Not a valid connection');
			$this->connection = $newConnection;
		}

		// </editor-fold>
		// <editor-fold defaultstate="collapsed" desc="Property string CommandText">
		private $commandText = null;

		/**
		 * @return string
		 */
		public function getCommandText()
		{
			return $this->commandText;
		}

		/**
		 * @param string $newCommandText
		 */
		public function setCommandText($newCommandText)
		{
			$this->commandText = $newCommandText;
		}

		// </editor-fold>
		// <editor-fold defaultstate="collapsed" desc="Property array Arguments">
		private $arguments = array();

		/**
		 * @return array
		 */
		public function getArguments()
		{
			return $this->arguments;
		}

		/**
		 * @param array $newArguments
		 */
		public function setArguments(array $newArguments)
		{
			$this->arguments = $newArguments;
		}

		/**
		 * @param string $name
		 * @param string $value
		 */
		public function setArgument($name, $value)
		{
			$this->arguments[$name] = $value;
		}

		/**
		 * @param string $name
		 */
		public function removeArgument($name)
		{
			if (isset($this->arguments[$name]))
			{
				unset($this->arguments[$name]);
			}
		}
		// </editor-fold>
		
		final public function __construct(SqlConnection $connection=null, $commandText=null, array $arguments=array())
		{
			parent::__construct();
			
			$this->setConnection($connection);
			$this->setCommandText($commandText);
			$this->setArguments($arguments);
		}

		/**
		 * Return a clone of this command that is paged over the resultset
		 *
		 * @param integer $pageIndex
		 * @param integer $pageSize
		 * @return SqlCommand
		 */
		public function paged($pageIndex, $pageSize=10)
		{
			$limitTop = $pageIndex * $pageSize;
			$limitLength = $pageSize;
			
			$pagedCmd = new static(
					  $this->getConnection()
					, sprintf('SELECT * FROM (%s) selection LIMIT %s, %s', $this->getCommandText())
					, $this->getArguments());
			
			return $pagedCmd;
		}
		
		/**
		 * Get the number of records this command would return.
		 *
		 * @return type @return integer
		 */
		public function count()
		{
			$countCmd = new static(
					  $this->getConnection()
					, sprintf('SELECT COUNT(*) FROM (%s) selection', $this->getCommandText())
					, $this->getArguments());

			return (integer)$countCmd->executeScalar();
		}
		
		/**
		 * Execute this command and return an engine driver native resource
		 *
		 * @return \PDOStatement
		 */
		public function execute()
		{
			return $this->getConnection()->execute($this);
		}

		/**
		 * Execute this command and return the value of the first column of the first row returned
		 *
		 * @return string
		 */
		public function executeScalar()
		{
			return $this->getConnection()->executeScalar($this);
		}

		/**
		 * Execute this command and return an associative array of the first row returned
		 *
		 * @return array
		 */
		public function executeRow()
		{
			return $this->getConnection()->executeRow($this);
		}

		/**
		 * Execute this command and return an an array of associative arrays for the first table in the resultset
		 *
		 * @return array[array]
		 */
		public function executeTable()
		{
			return $this->getConnection()->executeTable($this);
		}

		/**
		 * Execute this command and return an array of arrays of associative arrays for the entire resultset
		 *
		 * @return array[array[array]]
		 */
		public function executeTableSet()
		{
			return $this->getConnection()->executeTableSet($this);
		}

		/**
		 * Execute this command and return the number of rows affected
		 *
		 * @return array[array]
		 */
		public function executeNonQuery()
		{
			return $this->getConnection()->executeNonQuery($this);
		}
	}
}

#EOF