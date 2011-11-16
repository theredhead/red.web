<?php

namespace red\web
{
	use \red\Object;
	
	/**
	 * Description of Session
	 *
	 * @author kris
	 */
	abstract class Session extends Object
	{
		/**
		 * Open the session. Called by the PHP runtime upon session_start()
		 *
		 * Note that PHP expects session data to be stored as a serialized
		 * array of key => value pairs.
		 *
		 * @param string $savePath
		 * @param string $sessionName
		 * @return boolean
		 */
		public function open($savePath, $sessionName)
		{
			return true;
		}

		/**
		 * Close the session
		 *
		 * @return boolean
		 */
		public function close()
		{
			return true;
		}

		/**
		 * Get all session data stored under a particular session $id
		 *
		 * @param string $id
		 * @return string
		 */
		public function read($id)
		{
			return $this->getConnection()->createCommand(
					'SELECT `data` FROM sessions WHERE id=:id',
					array(':id'=>$id))->executeScalar();
		}

		/**
		 * Write all $data associated with session $id
		 *
		 * @param string $id
		 * @param string $data
		 * @return boolean
		 */
		public function write($id, $data)
		{
			$this->getConnection()->createCommand(
					'REPLACE INTO `sessions` (id, data) VALUES (:id, :data)',
					array(':id'=>$id,
						':data'=>$data)
					)->executeNonQuery();

			return true;
		}

		/**
		 * Destroy the given session $id
		 *
		 * @param string $id
		 */
		public function destroy($id)
		{
			$this->getConnection()->createCommand(
					'DELETE FROM `sessions` WHERE id=:id',
					array(':id'=>$id)
					)->executeNonQuery();

			return true;
		}

		/**
		 * Run garbage collection.
		 *
		 * @param $maxLifeTime
		 */
		public function gc($maxLifeTime)
		{
		}

		// <editor-fold defaultstate="collapsed" desc="Property MySqlClient\Connection Connection">
		/**
		 * @return SqlConnection
		 */
		abstract protected function getConnection();
		// </editor-fold>

		protected function __construct($connection)
		{
			parent::__construct();
			$this->setConnection($connection);
		}

		/**
		 * Initialize Session handling.
		 *
		 * @staticvar Session $session
		 */
		static public function init()
		{
			static $session = null;

			if ($session == null)
			{
				$session = new static();

				session_set_save_handler(
					array($session, 'open'),
					array($session, 'close'),
					array($session, 'read'),
					array($session, 'write'),
					array($session, 'destroy'),
					array($session, 'gc'));

				session_start();
			}
		}
	}
}

#EOF