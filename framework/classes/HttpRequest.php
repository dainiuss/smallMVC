<?php
/**
 * HttpRequest class - HTTP related method collection
 * 
 * @param $_host
 * 
 * @method redirect
 * @method getHost
 *
 * @author Dainius
 * @since 0.1
 */

class HttpRequest {

	private $_host;
	
	/**
     * Redirects the browser to the specified URL - wrapped in MainController
     * @param string $url
     * @param type $terminate
     * @param type $status
     */
	public function redirect($url,$terminate = true,$status = 302){
		Helpers::trace("HttpRequest", "redirect", array("Redirect to URL", $url));
		if(strpos($url,'/') === 0){
			$url = $this->getHost().$url;
		}
		header('Location: '.$url, true, $status);
		if($terminate){
			Main::getApplication()->terminate();
		}
	}

	/**
	 * Get host of the server
	 * @return host
	 */
	public function getHost(){
		if($this->_host === null){
			$http = 'http';
			if(isset($_SERVER['HTTP_HOST']))
				$this->_host = $http.'://'.$_SERVER['HTTP_HOST'];
			else{
				$this->_host = $http.'://'.$_SERVER['SERVER_NAME'];
			}
		}
		Helpers::trace("HttpRequest", "getHost", array("Host", $this->_host));
		return $this->_host;
	}

}