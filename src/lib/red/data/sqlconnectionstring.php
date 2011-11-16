<?php

namespace red\data
{
	use red\Object;

	class SqlConnectionStringException extends \Exception
	{
		public function getDescription()
		{
			return $this->getMessage();
		}
	}

	class SqlConnectionString extends Object
	{
		const CONNECTOR_MYSQL = '\\red\\data\\mysql\\MySqlConnection';
		const CONNECTOR_SQLITE = '\\red\\data\\sqlite\\SqLiteConnection';

		protected $connectorClassName = null;
		protected $serverName = null;
		protected $initialCatalog = null;
		protected $username = null;
		protected $password = null;
		protected $socket = null;
		protected $file = null;

		public function __construct($connectionString=null)
		{
			parent::__construct();
			if (is_string($connectionString))
			{
				$this->parse($connectionString);
			}
			else if ($connectionString !== null)
			{
				throw new SqlConnectionStringException('Invalid argument to ' . __METHOD__, 0, \E_USER_ERROR, __FILE__, __LINE__, null);
			}
		}

		public function createPDO()
		{
			switch($this->connectorClassName)
			{
				case self::CONNECTOR_MYSQL :
					$pdo = new \PDO(
							sprintf('mysql: host=%s; dbname=%s', 
									$this->serverName, 
									$this->initialCatalog), 
							$this->username, $this->password);
					$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
					return $pdo;
					break;
				case self::CONNECTOR_SQLITE :
					try
					{
						$pdo = new \PDO(sprintf('sqlite3:%s', $this->file));
					}
					catch(\PDOException $ex)
					{
						if ($ex->getMessage() === 'could not find driver')
						{
							$pdo = new \PDO(sprintf('sqlite:%s', $this->file));
						}
						else
						{
							throw $ex;
						}
					}
					$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
					return $pdo;

				default :
					throw new \red\NotImplementedException(__METHOD__.'('.$this->connectorClassName.')');					
			}
		}
		
		/**
		 *
		 * @param string $connectionString
		 * @return SqlConnectionString
		 */
		static public function fromString($connectionString)
		{
			$instance = new self();
			$instance->parse($connectionString);
			$instance->setup();

			return $instance;
		}

		protected function parse($connectionString)
		{
			$connectorAlias = trim(strchr($connectionString, ':', true));
			$this->connectorClassName = self::mapConnector($connectorAlias);

			$workString = substr($connectionString, strlen($connectorAlias)+1);
//			printf('$workString:<pre>%s</pre>', print_r($workString, true));

			$matches = array();
			$count = preg_match_all(
					// '~(?P<PROPERTY>[^=]+)?=((\'(?P<VALUE>([^\']+)?)\';?)|null;?)~i'
					'~(?P<PROPERTY>[^=]+)?=((\'(?P<VALUE>([^\']+)?)\';?)|null;?)~i',
					$workString, $matches, PREG_SET_ORDER);

//			printf('$matches<pre>%s</pre>', print_r($matches, true));

			foreach($matches as $match)
			{
				if (isset($match['PROPERTY']))
				{
					$workString = str_replace($match[0], '', $workString);
					$property	= trim(strtolower($match['PROPERTY']));
					$value		= isset($match['VALUE'])
								? $match['VALUE']
								: null;

					switch($property)
					{
						case 'host' :
						case 'server' :
							$this->serverName = $value;
							break;

						case 'database' :
						case 'dbname' :
						case 'db' :
						case 'initial catalog' :
							$this->initialCatalog = $value;
							break;

						case 'user' :
						case 'uid' :
						case 'username' :
						case 'user name' :
						case 'user id' :
							$this->username = $value;
							break;

						case 'pwd' :
						case 'password' :
						case 'passphrase' :
							$this->password = $value;
							break;

						case 'file' :
						case 'path' :
							$this->file = $value;
							break;

						default:
							printf('<strong>%s</strong>: "%s"<br/>', $property, $value);
							break;
					}
				}
			}

			if (strlen(trim($workString)) != 0)
			{
				throw new SqlConnectionStringException(sprintf(
						 '%s: Unable to parse part (%s) of the ConnectionString'
						,__METHOD__, $workString));
			}
		}
		
		/**
		 * Makes sure that the ConnectionString is complete.
		 */
		protected function setup()
		{
		}

		/**
		 *
		 *
		 * @return string
		 */
		protected function asMySqlConnectionString()
		{
			return str_replace("''", 'null', sprintf(
					  "%s: Server='%s'; Initial Catalog='%s'; Username='%s'; Password='%s';"
					, $this->connectorClassName
					, $this->serverName
					, $this->username
					, $this->initialCatalog
					, $this->password));
		}

		/**
		 *
		 *
		 * @return string
		 */
		protected function asSQLiteConnectionString()
		{
			return str_replace("''", 'null', sprintf(
					  "%s: File='%s';"
					, $this->connectorClassName
					, $this->file));
		}

		/**
		 * Get a string representation of this instance that can later be used to
		 * instantiate another SqlConnectionString
		 *
		 * not doing the 'serializad' representation under toString for this one
		 * in order to prevent passwords from shoing up by accident. That doesn't
		 *
		 * @return string
		 */
		public function getAsString()
		{
			$result = null;

			switch ($this->connectorClassName)
			{
				case self::CONNECTOR_MYSQL :
					$result = $this->asMySqlConnectionString();
				break;
				case self::CONNECTOR_SQLITE :
					$result = $this->asSQLiteConnectionString();
				break;
			}

			return $result;
		}

		/**
		 * @return SqlConnection
		 */
		public function getConnection()
		{
			$reflector = new \ReflectionClass($this->connectorClassName);
			if ($reflector->isSubclassOf('\\red\\data\\SqlConnection') && $reflector->isInstantiable())
			{
				$instance = $reflector->newInstance();
				$instance->setConnectionString($this);
				return $instance;
			}
			else
			{
				throw new SqlConnectionStringException(sprintf(
						'Invalid connector %s.', $this->connectorClassName));
			}
		}

		/**
		 * Get the fully qualified classname for the connector or null if the
		 * connector name is not recognized.
		 *
		 * @param string $connectorName
		 * @return string
		 */
		protected static function mapConnector($connectorName)
		{
			$result = null;

			switch(strtolower($connectorName))
			{
				case self::CONNECTOR_MYSQL :
				case 'mysql' :
					$result = self::CONNECTOR_MYSQL;
				break;

				case self::CONNECTOR_SQLITE :
				case 'sqlite' :
					$result = self::CONNECTOR_SQLITE;
				break;
			}

			return $result;
		}
	}
}

#EOF