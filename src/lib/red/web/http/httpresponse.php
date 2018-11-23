<?php

namespace red\web\http
{
	class HttpResponse extends \red\Obj
	{
		private $buffer = array();

		/**
		 * Append some text to this response
		 * 
		 * @param string $str 
		 */
		protected function append($str)
		{
			array_push($this->buffer, $str);
		}
		
		/**
		 * @param string $string 
		 */
		public function write($string)
		{
			$this->append($string);
		}

		/**
		 * @param string $string 
		 */
		public function writeLn($string)
		{
			$this->append($string);
			$this->append("\n");
		}

		/**
		 * @return string
		 */
		public function getOutputBuffer()
		{
			return implode('', $this->buffer);
		}
		
		// <editor-fold defaultstate="collapsed" desc="Property array Headers">
		private $headers = array();

		/**
		 * @return array
		 */
		public function getHeaders()
		{
			return $this->headers;
		}

		/**
		 * @param array $newHeaders
		 */
		public function setHeaders(array $newHeaders)
		{
			$this->headers = $newHeaders;
		}
		
		/**
		 * Set a value for an http header field
		 *
		 * @param string $name
		 * @param string $value 
		 */
		public function setHeader($name, $value)
		{
			$this->headers[$name] = $value;
		}
		
		/**
		 * See if a value is set for an http header field
		 * 
		 * @param type $name
		 * @return type 
		 */
		public function hasHeader($name)
		{
			return isset($this->headers[$name]);
		}
		
		/**
		 * Gemote an http header field
		 * 
		 * @param type $name 
		 */
		public function unsetHeader($name)
		{
			if ($this->hasHeader($name))
			{
				unset($this->headers[$name]);
			}
		}
		
		/**
		 * Get the value that is set for an http header field
		 * 
		 * @param type $name
		 * @return type 
		 */
		public function getHeader($name)
		{
			return $this->hasHeader($name)
					? $this->headers[$name]
					: null;
		}
		// </editor-fold>
		
		/**
		 * Send this response to the client
		 */
		public function send()
		{
			foreach($this->headers as $name => $value)
			{
				header(sprintf("%s: %s", $name, $value));
			}
			echo $this->getOutputBuffer();
		}

		/**
		 * Redirect to a url.
		 *
		 * @param $url
		 * @param int $responseCode
		 * @return void
		 */
		public function redirect($url, $responseCode=303)
		{
			header('Location: '.$url, $responseCode);
			exit;
		}

        public function clearBuffer()
        {
            $this->buffer = array();
        }
    }
}

#EOF