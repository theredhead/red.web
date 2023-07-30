<?php

namespace red\web
{
	use red\Obj;
	use red\MBString;
	
	/**
	 * URL provides an object oriented approach to working with
	 * Uniform Resource Locators.
	 *
	 * You have ArrayAccess to the query so:
	 *  $url = new URL('http://www.google.com/search');
	 *  $url['q'] = 'Hello, World!';
	 *  echo $url; // outputs 'http://www.google.com/search?q=Hello,%20World!'
	 * 
	 * @author kris
	 */
	class Url extends Obj implements \ArrayAccess
	{
		// <editor-fold defaultstate="collapsed" desc="Property string Protocol">
		private $protocol = 'http';
		/**
		 * Get a string representing the protocol for this URL
		 * 
		 * some examples: http, https, ftp, svn+ssh, mate.
		 * 
		 * @return string defaults to 'http'
		 */
		public function getProtocol()
		{
			return $this->protocol;
		}

		/**
		 * Set the string representing the protocol for this URL
		 *
		 * @param string $newProtocol
		 */
		public function setProtocol($newProtocol)
		{
			$this->protocol = $newProtocol;
		}
		// </editor-fold>
		// <editor-fold defaultstate="collapsed" desc="Property string Username">
		private $username = null;

		/**
		 * Get the username part of this URL
		 * 
		 * @return string defaults to null
		 */
		public function getUsername()
		{
			return $this->username;
		}

		/**
		 * Set the username part of this URL
		 * 
		 * @param string $newUsername
		 */
		public function setUsername($newUsername)
		{
			$this->username = $newUsername;
		}
		// </editor-fold>
		// <editor-fold defaultstate="collapsed" desc="Property string Password">
		private $password = null;

		/**
		 * Get the password part of this URL
		 * 
		 * @return string
		 */
		public function getPassword()
		{
			return $this->password;
		}

		/**
		 * Set the password part of this URL
		 * 
		 * @param string $newPassword
		 */
		public function setPassword($newPassword)
		{
			$this->password = $newPassword;
		}
		// </editor-fold>
		// <editor-fold defaultstate="collapsed" desc="Property string Hostname">
		private $hostname = null;

		/**
		 * Get the hostname part of this URL
		 * 
		 * @return string
		 */
		public function getHostname()
		{
			if ($this->hostname === null)
			{
				$this->hostname = $_SERVER['SERVER_NAME'];
			}
			return $this->hostname;
		}

		/**
		 * Set the hostname part of this URL
		 * 
		 * @param string $newHostname
		 */
		public function setHostname($newHostname)
		{
			$this->hostname = $newHostname;
		}
		// </editor-fold>
		// <editor-fold defaultstate="collapsed" desc="Property string Path">
		private $path = array();

		/**
		 * Set the path part of this URL. returns an array of "words"directory" names
		 * 
		 * @return array
		 */
		public function getPath()
		{
			return $this->path;
		}

		/**
		 * Set the path part of this URL as a string
		 * 
		 * @return string
		 */
		public function getPathAsString()
		{
			return '/'.implode('/', $this->getPath());
		}

		/**
		 * Set the path of the URL
		 * 
		 * @param mixed $newPath array of directory names or string
		 */
		public function setPath($newPath)
		{
			$this->path = is_array($newPath)
					? $newPath
					: \preg_split('/\//', $newPath, -1, \PREG_SPLIT_NO_EMPTY);
		}
		// </editor-fold>
		// <editor-fold defaultstate="collapsed" desc="Property array Query">
		private $query = array();

		/**
		 * @return QueryString
		 */
		public function getQuery()
		{
			return $this->query;
		}

		/**
		 * Set the array that represents this instances' query
		 * 
		 * @param QueryString $newQuery
		 */
		public function setQuery(array $newQuery)
		{
			$this->query = $newQuery;
		}

		/**
		 * Get the string that this instances' query represents
		 *
		 * @return MBString
		 */
		protected function getQueryString()
		{
			$pairs = array();
			foreach($this->query as $name => $value)
			{
				array_push($pairs, sprintf(
						'%s=%s', urlencode($name), urlencode($value)));
			}
			return count($pairs) > 0 ? MBString::withString('?'.implode('&', $pairs)) : MBString::defaultValue();
		}
		// </editor-fold>
		// <editor-fold defaultstate="collapsed" desc="Property string Fragment">
		private $fragment = '';

		/**
		 * @return string
		 */
		public function getFragment()
		{
			return $this->fragment;
		}

		/**
		 * @param string $newFragment
		 */
		public function setFragment($newFragment)
		{
			$this->fragment = $newFragment;
		}

		// </editor-fold>

		public function __construct($urlString=null)
		{
			parent::__construct();
			if ($urlString !== null)
			{
				$this->parse($urlString);
			}
		}
		
		/**
		 * Parse a url string into a new URL instance
		 *
		 * @param string $urlString
		 */
		static public function fromString($urlString)
		{
			$result = new static();
			$result->parse($urlString);
			return $result;
		}

		/**
		 * parse a url string into this object
		 * 
		 * @param string $urlString
		 */
		protected function parse($urlString)
		{
			if (self::isUrl($urlString))
			{
				$info = @parse_url($urlString);
				$this->setProtocol(isset($info['scheme']) ? $info['scheme'] : null);
				$this->setUsername(isset($info['user']) ? $info['user'] : null);
				$this->setPassword(isset($info['pass']) ? $info['pass'] : null);
				$this->setHostname(isset($info['host']) ? $info['host'] : null);
				$this->setPath(isset($info['path']) ? $info['path'] : '/');
				if (isset($info['query']))
				{
					$query = array();
					parse_str($info['query'], $query);
					$this->setQuery($query);
				}
				$this->setFragment(isset($info['fragment']) ? $info['fragment'] : null);
			}
			else
			{
				throw new \ErrorException(sprintf('Attempt to parse an invalid URL: "%s"', $urlString)
						, E_USER_ERROR, E_USER_ERROR, __FILE__, __LINE__, null);
			}
		}

		/**
		 * Get a string representation of this instance,
		 * 
		 * @return string
		 */
		public function toString()
		{
			$result = '';

			$result .= $this->getProtocol() != null ? $this->getProtocol() . '://' : '';
			$result .= $this->getUsername() != null ? $this->getUsername() : '';
			$result .= $this->getPassword() != null ? ':' . $this->getPassword() : '';
			$result .= $this->getUsername() != null ? '@' : '';
			$result .= $this->getHostname() != null ? $this->getHostname() : '';
			$result .= $this->getPath() != null ? $this->getPathAsString() : '';
			$result .= $this->getQueryString();
			$result .= $this->getFragment() != null ? '#' . $this->getFragment() : '';
			
			return $result;
		}

		/**
		 * Check wether a string holds a valid URL.
		 * 
		 * @param string $test
		 * @return boolean
		 */
		public static function isURL($test)
		{
			if ($test instanceof URL)
			{
				return true;
			}
			if (strpos($test, '.') > 1)
			{
				return parse_url($test) !== false;
			}
			return false;
		}

		// <editor-fold defaultstate="collapsed" desc="ArrayAccess on the Query">
		
		/**
		 * See if a specific query item identified by $offset exists in this Url
		 *
		 * @param string $offset
		 * @return boolean 
		 */
		public function offsetExists($offset)
		{
			return isset($this->query[(string)$offset]);
		}

		/**
		 * Get a specific query item value identified by $offset from this Url
		 * 
		 * @param string $offset
		 * @return string 
		 */
		public function offsetGet($offset)
		{
			return isset($this->query[(string)$offset])
					? $this->query[(string)$offset]
					: null;
		}

		/**
		 * Set a specific query item identified by $offset to $value in this Url
		 *
		 * @param string $offset
		 * @param string $value 
		 */
		public function offsetSet($offset, $value)
		{
			$this->query[(string)$offset] = (string)$value;
		}

		/**
		 * Remove a specific query item identified by $offset from this Url
		 *
		 * @param string $offset 
		 */
		public function offsetUnset($offset)
		{
			unset($this->query[(string)$offset]);
		}
		
		// </editor-fold>
	}
}