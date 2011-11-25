<?php

namespace red\web\http
{
	use \red\web\Url;
	use \red\Object;
	
	/**
	 * Represents an http request to some endpoint along with all its data.
	 */
	class HttpRequest extends Object
	{
		// <editor-fold defaultstate="collapsed" desc="Property string RequestUrl">
		private $requestUrl = null;

		/**
		 * @return \red\web\URL
		 */
		public function getRequestUrl()
		{
			return $this->requestUrl;
		}

		/**
		 * @param Url $newRequestUrl
		 */
		public function setRequestUrl($newRequestUrl)
		{
			$newRequestUrl instanceof Url or 
				Url::isUrl($newRequestUrl) and $newRequestUrl = new Url($newRequestUrl);

			$this->requestUrl = $newRequestUrl;
		}
		// </editor-fold>
		// <editor-fold defaultstate="collapsed" desc="Property string RedirectUrl">
		private $redirectUrl = null;

		/**
		 * @return string
		 */
		public function getRedirectUrl()
		{
			return $this->redirectUrl;
		}

		/**
		 * @param string $newRedirectUrl
		 */
		public function setRedirectUrl($newRedirectUrl)
		{
			$newRedirectUrl instanceof Url or 
				Url::isUrl($newRedirectUrl) and $newRedirectUrl = new Url($newRedirectUrl);

			$this->redirectUrl = $newRedirectUrl;
		}
		// </editor-fold>
		// <editor-fold defaultstate="collapsed" desc="Property Url RefererUrl">
		private $refererUrl = null;

		/**
		 * @return Url
		 */
		public function getRefererUrl()
		{
			return $this->refererUrl;
		}

		/**
		 * @param Url $newRefererUrl
		 */
		public function setRefererUrl($newRefererUrl)
		{
			assert($newRefererUrl === null or Url::isUrl($newRefererUrl));
			
			$newRefererUrl instanceof Url or 
				Url::isUrl($newRefererUrl) and $newRefererUrl = new Url($newRefererUrl);

			$this->refererUrl = $newRefererUrl;
		}

		// </editor-fold>
		// <editor-fold defaultstate="collapsed" desc="Property string HostName">
		private $hostName = null;

		/**
		 * @return string
		 */
		public function getHostName()
		{
			return $this->hostName;
		}

		/**
		 * @param string $newHostName
		 */
		public function setHostName($newHostName)
		{
			$this->hostName = $newHostName;
		}
		// </editor-fold>
		// <editor-fold defaultstate="collapsed" desc="Property string UserAgent">
		private $userAgent = 'red.web';

		/**
		 * @return string
		 */
		public function getUserAgent()
		{
			return $this->userAgent;
		}

		/**
		 * @param string $newUserAgent
		 */
		public function setUserAgent($newUserAgent)
		{
			$this->userAgent = $newUserAgent;
		}
		// </editor-fold>
		// <editor-fold defaultstate="collapsed" desc="Property string RemoteAddress">
		private $remoteAddress = null;

		/**
		 * @return string
		 */
		public function getRemoteAddress()
		{
			return $this->remoteAddress;
		}

		/**
		 * @param string $newRemoteAddress
		 */
		public function setRemoteAddress($newRemoteAddress)
		{
			$this->remoteAddress = $newRemoteAddress;
		}
		// </editor-fold>
		// <editor-fold defaultstate="collapsed" desc="Property string Accept">
		private $accept = null;

		/**
		 * @return string
		 */
		public function getAccept()
		{
			return $this->accept;
		}

		/**
		 * @param string $newAccept
		 */
		public function setAccept($newAccept)
		{
			$this->accept = $newAccept;
		}
		// </editor-fold>
		// <editor-fold defaultstate="collapsed" desc="Property string AcceptLanguage">
		private $acceptLanguage = 'en-us';

		/**
		 * @return string
		 */
		public function getAcceptLanguage()
		{
			return $this->acceptLanguage;
		}

		/**
		 * @param string $newAcceptLanguage
		 */
		public function setAcceptLanguage($newAcceptLanguage)
		{
			$this->acceptLanguage = $newAcceptLanguage;
		}
		// </editor-fold>
		// <editor-fold defaultstate="collapsed" desc="Property string CacheControl">
		private $cacheControl = null;

		/**
		 * @return string
		 */
		public function getCacheControl()
		{
			return $this->cacheControl;
		}

		/**
		 * @param string $newCacheControl
		 */
		public function setCacheControl($newCacheControl)
		{
			$this->cacheControl = $newCacheControl;
		}
		// </editor-fold>
		// <editor-fold defaultstate="collapsed" desc="Property string AcceptEncoding">
		private $acceptEncoding = null;

		/**
		 * @return string
		 */
		public function getAcceptEncoding()
		{
			return $this->acceptEncoding;
		}

		/**
		 * @param string $newAcceptEncoding
		 */
		public function setAcceptEncoding($newAcceptEncoding)
		{
			$this->acceptEncoding = $newAcceptEncoding;
		}
		// </editor-fold>
		// <editor-fold defaultstate="collapsed" desc="Property string Cookie">
		private $cookie = '';

		/**
		 * @return string
		 */
		public function getCookie()
		{
			return $this->cookie;
		}

		/**
		 * @param string $newCookie
		 */
		public function setCookie($newCookie)
		{
			$this->cookie = $newCookie;
		}
		// </editor-fold>
		// <editor-fold defaultstate="collapsed" desc="Property string RequestMethod">
		private $requestMethod = 'GET';

		/**
		 * @return string
		 */
		public function getRequestMethod()
		{
			return $this->requestMethod;
		}

		/**
		 * @param string $newRequestMethod
		 */
		public function setRequestMethod($newRequestMethod)
		{
			$this->requestMethod = $newRequestMethod;
		}
		// </editor-fold>
		// <editor-fold defaultstate="collapsed" desc="Property array PostData">
		private $postData = array();

		/**
		 * @return array
		 */
		public function getPostData()
		{
			return $this->postData;
		}

		/**
		 * @param array $newPostData
		 */
		public function setPostData(array $newPostData)
		{
			$this->postData = $newPostData;
		}
		
		/**
		 * @param string $key
		 * @return boolean
		 */
		public function hasPostData($key=null)
		{
			$result = false;

			if ($key == null)
			{
				$result = count($this->postData) > 0;
			}
			else
			{
				$result = isset($this->postData[$key]);
			}
			
			return $result;
		}
		
		/**
		 * @param string $key
		 * @param string $default
		 * @return string 
		 */
		public function getFormField($key, $default = null)
		{
			return $this->hasPostData($key)
					? $this->postData[$key]
					: $default;
		}
		// </editor-fold>
		// <editor-fold defaultstate="collapsed" desc="Property boolean IsPostback">
		/**
		 * Determine wether this request represents a posted respons back to the
		 * previous request.
		 * 
		 * @return boolean
		 */
		public function isPostback()
		{
			return $this->isPostback = $this->getRequestMethod() == 'POST'
					&& (string)$this->getRefererUrl() == (string)$this->getRequestUrl();
		}

		// </editor-fold>
		
		/**
		 * @return HttpRequest
		 */
		static public function withRequestArray(array $info)
		{
			$instance = new static();
//			$instance->info = $info;
			$extract = 
				function($key, $default=null) use ($info)
				{
					return isset($info[$key]) ? $info[$key] : $default;
				};
			
			if ($instance instanceof HttpRequest)
			{
				$fullRequestUrl = self::protocolNameFromFormalProtocolHeader($info['SERVER_PROTOCOL']) .'://'. $info['HTTP_HOST'] . $info['REQUEST_URI'];
				
				$instance->setRequestUrl($fullRequestUrl);
				$instance->setRequestMethod($info['REQUEST_METHOD']);
				$instance->setRedirectUrL($extract('REDIRECT_URL'));
				$instance->setRefererUrl($extract('HTTP_REFERER'));
				$instance->setHostName($extract('HTTP_HOST'));
				$instance->setUserAgent($extract('HTTP_USER_AGENT'));
				$instance->setRemoteAddress($extract('REMOTE_ADDR'));
				$instance->setAccept($extract('HTTP_ACCEPT'));
				$instance->setAcceptLanguage($extract('HTTP_ACCEPT_LANGUAGE'));
				$instance->setCacheControl($extract('HTTP_CACHE_CONTROL'));
				$instance->setAcceptEncoding($extract('HTTP_ACCEPT_ENCODING'));
				$instance->setCookie($extract('HTTP_COOKIE'));
				$instance->setPostData($_POST);
			}
			
			return $instance;
		}
		
		/**
		 * Extracts the protocol name from a header.
		 * for example: "HTTP/1.1" would return "http"
		 *
		 * @TODO: fix for other protocols.
		 * @return string
		 */
		static protected function protocolNameFromFormalProtocolHeader($protocolHeader)
		{
			$result = 'http';
			switch($protocolHeader)
			{
				case 'HTTP/1.1' : 
					$result = 'http';
					break;
				default:
					$result = 'https';
					break;
			}
			return 'http';
		}
	}
}

#EOF