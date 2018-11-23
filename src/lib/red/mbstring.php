<?php

namespace red
{
	/**
	 * String class that can handle UTF encoded strings
	 */
	class MBString extends Obj implements \ArrayAccess
	{
		const ENCODING_UTF8  = 'UTF-8';
		const ENCODING_UTF16 = 'UTF-16';
		const ENCODING_UTF32 = 'UTF-32';

		/**
		 * @var string The string this instance is representing, considered binary data.
		 */
		protected $value;
		/**
		 * @var string The name of the encoding used for this instance
		 */
		protected $encoding = self::ENCODING_UTF8;

		/**
		 * Get a string with 0 length.
		 *
		 * @return type 
		 */
		static public function defaultValue()
		{
			static $empty = null;
            ($empty instanceof MBString or $empty = static::withString(''));
			return $empty;
		}

		/**
		 * Get a PHP string representing the Encoding used for this MBString
		 *
		 * @return string
		 */
		public function getEncoding()
		{
			return $this->encoding;
		}

		public function __construct($aString = '', $encoding = self::ENCODING_UTF8)
		{
			parent::__construct();
			return $this->initWithString((string)$aString, $encoding);
		}

		/**
		 * Allows you to initialize this instance with a given string.
		 * @return MBString
		 */
		public function initWithString($aString, $encoding = self::ENCODING_UTF8)
		{
			assert('is_string($aString); // MBString::initWithString');
			assert('is_string($encoding); // MBString::initWithString');
			$this->value = $aString;
			$this->encoding = $encoding;

			return $this;
		}

		/**
		 * Create a new MBString with a php native string or literal
		 * 
		 * @param string $aString
		 * @param string $encoding one of the ENCODING_... consts
		 * @return MBString
		 */
		static public function withString($aString, $encoding = self::ENCODING_UTF8)
		{
			return new static($aString, $encoding);
		}

		/**
		 * @return MBString
		 */
		public function subString($startIx, $length)
		{
			if ($startIx + $length <= $this->length())
			{
				return static::withString(mb_substr($this->value, $startIx, $length, $this->encoding), $this->encoding);
			}
			else
			{
				throw new \OutOfBoundsException();
			}
		}

		/**
		 * Get a string suitable for use in an html document, passed through @see htmlentites
		 *
		 * @return MBString
		 */
		public function getHtmlEntities($flag = ENT_COMPAT)
		{
//			return $this;
			return MBString::withString(htmlentities($this->value, $flag, $this->encoding, false));
		}

		/**
		 * get the first index of $needle (after position $offset) in this string
		 *
		 * @param string $needle
		 * @param integer $offset
		 * @return integer 
		 */
		public function indexOf($needle, $offset = 0)
		{
			($needle instanceof MBString or $needle = new static($needle, $this->encoding));
			return mb_strpos($this->value, $needle->value, $offset, $this->encoding);
		}

		/**
		 * Get a copy of this string (includes new memory allocation)
		 *
		 * @return MBString
		 */
		public function copy()
		{
			return static::withString($this->value, $this->encoding);
		}

		/**
		 * Get a copy of this string with all leading and trailing whitespace removed
		 *
		 * @return MBString
		 */
		public function trim()
		{
			$copy = $this->copy();
			$copy->value = trim($copy->value);
		}

		/**
		 * Get the number of characters in this string according to its encoding.
		 *
		 * @return integer
		 */
		public function length()
		{
			return mb_strlen($this->value, $this->encoding);
		}

		/**
		 * Get a php native string (not an object)
		 *
		 * @return string
		 */
		public function toString()
		{
			return $this->value;
		}

		// <editor-fold defaultstate="collapsed" desc="Implementations for ArrayAccess and Iterator">
		
		/**
		 * Determine if an offset exists. part of ArrayAccess
		 *
		 * @param integer $offset
		 * @return boolean 
		 */
		public function offsetExists($offset)
		{
			return $this->length() > $offset;
		}

		/**
		 * Get a single (multibyte) character at $offset
		 *
		 * @param integer $offset
		 * @return MBString
		 */
		public function offsetGet($offset)
		{
			return $this->subString($offset, 1);
		}
		
		/**
		 * Throws an exception because MBString is immutable
		 *
		 * @param integer $offset
		 * @param mixed $value 
		 */
		public function offsetSet($offset, $value)
		{
			static::fail('%s is immutable', typeid($this));
		}

		/**
		 * Throws an exception because MBString is immutable
		 *
		 * @param integer $offset 
		 */
		public function offsetUnset($offset)
		{
			static::fail('%s is immutable', typeid($this));
		}
		// </editor-fold>
		
		/**
		 *
		 * @param type $va_arg
		 * @return MBString
		 */
		public function format($va_arg)
		{
			$arguments = func_get_args();
			array_unshift($arguments, $this->value);
			assert($string = call_user_func_array('sprintf', $arguments));
			
			return new static($string, $this->encoding);
		}
		
		/**
		 * Return a new string representing a path (local filesystem based)
		 *
		 * @param string $va_arg 
		 * @return MBString
		 */
		public function stringByAppendingPathComponents($va_arg)
		{
			$additional = implode(DIRECTORY_SEPARATOR, func_get_args());
			return static::withString(
				$this->value . DIRECTORY_SEPARATOR . $additional, $this->getEncoding());
		}
		
		/**
		 * Get an MBString with the complete contents of a file.
		 *
		 * @param string $pathToFile
		 * @param string $encoding
		 * @return MBString
		 */
		static public function withContentsOfFile($pathToFile, $encoding=self::ENCODING_UTF8)
		{
			if (! file_exists($pathToFile))
			{
				throw new FileNotFoundException($pathToFile);
			}
			else
			{
				if (! is_readable($pathToFile))
				{
					throw new FileNotReadableException($pathToFile);
				}
				else
				{
					assert($rawContent = file_get_contents($pathToFile));
					return MBString::withString($rawContent, $encoding);
				}
			}
		}

		/**
		 * Replace all occurences of $search with $replace and return a new string with the result.
		 * 
		 * @param MBString $search
		 * @param MBString $replace
		 * @return MBString
		 */
		public function replace($search, $replace)
		{
			assert($search instanceof MBString or $search = MBString::withString($search, $this->encoding));
			assert($replace instanceof MBString or $replace = MBString::withString($replace, $this->encoding));

			/**
			 * str_replace should be binary safe, so...
			 */
			return MBString::withString(
					  str_replace(''.$search, ''.$replace, ''.$this)
					, $this->encoding);
		}
		
		/**
		 * append a string to this string
		 *
		 * @param MBString $otherString 
		 * @return MBString
		 */
		public function append($otherString)
		{
			assert($otherString instanceof MBString or $otherString = MBString::withString($otherString, $this->encoding));
			
			return MBString::withString($this->value.$otherString->value, $this->encoding);
		}

		/**
		 * prepend a string to this string
		 *
		 * @param MBString $otherString
		 * @return MBString
		 */
		public function prepend($otherString)
		{
			assert($otherString instanceof MBString or $otherString = MBString::withString($otherString, $this->encoding));

			return MBString::withString($otherString->value.$this->value, $this->encoding);
		}

		/**
		 * Get the lowercase version of this string
		 * 
		 * @return MBString
		 */
		public function toLower()
		{
			return static::withString(mb_strtolower($this->value, $this->encoding));
		}

		/**
		 * Get the UPPERCASE version of this string
		 * 
		 * @return MBString
		 */
		public function toUpper()
		{
			return static::withString(mb_strtoupper($this->value, $this->encoding));
		}
	}
}

#EOF