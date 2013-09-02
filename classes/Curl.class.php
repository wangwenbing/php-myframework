<?php
/* --- 若无特别需求,请勿修改此文件,谢谢 --- */
class Curl {
	// protected $_useragent = 'Mozilla/5.0 (Windows NT 5.1; rv:19.0) Gecko/20100101 Firefox/19.0';
	protected $_useragent = 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.93';
	protected $_url;
	protected $_followlocation;
	protected $_timeout;
	protected $_maxRedirects;
	protected $_cookieFileLocation;
	protected $_post;
	protected $_postFields;
	protected $_proxy = false;
	protected $_proxySocket5 = true;
	protected $_proxyURL;
	protected $_proxyPort;
	protected $_referer = "http://www.google.co.nz";
	protected $_session;
	protected $_webpage;
	protected $_includeHeader;
	protected $_noBody;
	protected $_status;
	protected $_error;
	protected $_binaryTransfer;
	public    $authentication = 0;
	public    $auth_name      = '';
	public    $auth_pass      = '';
	public    $arrHeader = array('Expect:');
	
	public function __construct($url = '', $followlocation = true, $timeOut = 60, $maxRedirecs = 4, $binaryTransfer = false, $includeHeader = false, $noBody = false) {
		$this->_url = $url;
		$this->_followlocation = $followlocation;
		$this->_timeout = $timeOut;
		$this->_maxRedirects = $maxRedirecs;
		$this->_noBody = $noBody;
		$this->_includeHeader = $includeHeader;
		$this->_binaryTransfer = $binaryTransfer;
		if (defined('CACHES_PATH')) {
            $this->_cookieFileLocation = CACHES_PATH . '/cookie_curl.txt';
		} else {
		    $this->_cookieFileLocation = dirname(__FILE__) . '/cookie_curl.txt';
		}
	}
	
	public function useAuth($use) {
		 $this->authentication = 0;
		 if($use == true) $this->authentication = 1;
	}
	
	public function useProxy($blnUseProxy) {
		 $this->_proxy = ($blnUseProxy == true) ? 1 : 0;
	}

	public function setProxy($url) {
		if (strlen($url) > 0) {
			$this->useProxy(true);
			$this->_proxyURL = $url;
		}
	}
	
    public function setProxyPort($port) {
		if (strlen($this->_proxyURL) > 0 && is_numeric($port)) {
			$this->_proxyPort = $port;
		}
	}
		 
	public function setName($name) {
		$this->auth_name = $name;
	}

	public function setPass($pass) {
		$this->auth_pass = $pass;
	}
	
	public function setReferer($referer) {
		$this->_referer = $referer;
	}

	public function setCookiFileLocation($path) {
		$this->_cookieFileLocation = $path;
	}

	public function setPost ($postFields) {
		$this->_post = true;
		$this->_postFields = $postFields;
	}

	public function setUserAgent($userAgent) {
		$this->_useragent = $userAgent;
	}
	
	public function setUrl($strUrl) {
		$this->_url = $strUrl;
	}
	
	public function setHeader($arrHeader) {
	    if (is_array($arrHeader)) {
	        $this->arrHeader = $arrHeader;
	    }
	}
	
	public function makeHttpRequest() {
		$s = curl_init();

		curl_setopt($s,CURLOPT_URL,$this->_url);
		curl_setopt($s,CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($s,CURLOPT_HTTPHEADER, $this->arrHeader);
		curl_setopt($s,CURLOPT_TIMEOUT,$this->_timeout);
		curl_setopt($s,CURLOPT_MAXREDIRS,$this->_maxRedirects);
		curl_setopt($s,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($s,CURLOPT_FOLLOWLOCATION,$this->_followlocation);
		curl_setopt($s,CURLOPT_COOKIEJAR,$this->_cookieFileLocation);
		curl_setopt($s,CURLOPT_COOKIEFILE,$this->_cookieFileLocation);

		if($this->authentication == 1){
			curl_setopt($s, CURLOPT_USERPWD, $this->auth_name.':'.$this->auth_pass);
		}
		
		if($this->_post) {
			curl_setopt($s,CURLOPT_POST,true);
			curl_setopt($s,CURLOPT_POSTFIELDS,$this->_postFields);
		}

		if($this->_includeHeader) {
			curl_setopt($s,CURLOPT_HEADER,true);
		}

		if($this->_noBody) {
			 curl_setopt($s,CURLOPT_NOBODY,true);
		}
		
		if($this->_proxy) {
			curl_setopt($s, CURLOPT_PROXY, $this->_proxyURL);
		}
		 
		if ($this->_proxyPort) {
		    curl_setopt($s, CURLOPT_PROXYPORT, $this->_proxyPort);
		}
		
		if ($this->_proxy && $this->_proxySocket5) {
		    curl_setopt($s, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
		}
		
		/*
		if($this->_binary) {
			curl_setopt($s,CURLOPT_BINARYTRANSFER,true);
		}
		*/
		curl_setopt($s,CURLOPT_USERAGENT,$this->_useragent);
		
	    if ($this->_referer) {
		    curl_setopt($s, CURLOPT_REFERER, $this->_referer);
		}

		$this->_webpage = curl_exec($s);
		$this->_status = curl_getinfo($s,CURLINFO_HTTP_CODE);
		$this->_error = curl_error($s);
		curl_close($s);
	}

	public function getHttpStatus() {
		return $this->_status;
	}
	
	public function getLastError() {
		return $this->_error;
	}
	 
	public function getResult() {
		return $this->_webpage;
	}
	
	public function __toString(){
		return $this->_webpage;
	}
}
?>