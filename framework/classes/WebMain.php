<?php
/**
 * WebMain class - the main class with all functionality all the methods in this
 * class can be called using Main::getApplication()->methodName()
 * 
 * @param $_publicUrl
 * @param $_scriptUrl
 * @param string $routeVar
 * @param string $defaultController
 * @param array $controllerMap
 * @param string $_protectedPath
 * @param boolean $caseSensitive
 * @param string $_controllerPath
 * @param Object $_controller
 * 
 * @method __construct
 * @method configure
 * @method getWebsiteName
 * @method getPublicUrl
 * @method getScriptUrl
 * @method db
 * @method terminate
 * @method getSession
 * @method init
 * @method processRequest
 * @method run
 * @method parseUrl
 * @method getProtectedPath
 * @method getControllerPath
 * @method getController
 * @method setController
 * @method createController
 * @method runController
 * @method parseActionParameters
 * @method parsePathInfo
 * @method getCaptcha
 * @method getHost
 * @method isAdmin
 * @method getStatus
 *
 * @author Dainius
 * @since 0.1
 */
 
class WebMain {

    private $_publicUrl;
	private $_scriptUrl;
	public  $routeVar          = Constants::ROUTE_VAR;
	public  $defaultController = Constants::DEFAULT_CONTROLLER;
	public  $controllerMap     = array();
	private $_protectedPath    = PROTECTED_PATH;
	public  $caseSensitive     = true;
	private $_controllerPath;
	private $_controller;
	
    /**
	 * Constructor:
	 * - Sets application so all the methods in WebMain can be called statically
     *   Ex: Main::getApplication()->method()
	 * - Loads configuration file in protected/config
     *   Configuration file has database configuration
     * - Calls init() which processes the request
	 */
	public function __construct($config = null){
		Helpers::trace("WebMain", "__construct");
		Base::setApplication($this);

		if(is_string($config)){
			$config = require($config);
		}
		$this->configure($config);
        $this->init();
	}
    
    /**
	 * Load parameters in configuration file
	 */
	public function configure($config){
		Helpers::trace("WebMain", "configure");
		if(is_array($config)){
			foreach($config as $k=>$v){
				$this->$k=$v;
			}
		}
	}
	
	/**
	 * Get website name
	 * @return website name
	 */
	public function getWebsiteName(){
		Helpers::trace("WebMain", "getWebsiteName");
		return $this->website_name;
	}
	
	/**
	 * Get public URL
	 * @return public directory URL
	 */
	public function getPublicUrl(){
		if($this->_publicUrl === null){
			$this->_publicUrl = rtrim(dirname($this->getScriptUrl()),'\\/');
		}
		return $this->_publicUrl;
	}
	
	/**
	 * Get main index URL
	 * @return URL
	 */
	public function getScriptUrl(){
		if($this->_scriptUrl === null){
			$scriptName = basename($_SERVER['SCRIPT_FILENAME']);
			if(basename($_SERVER['SCRIPT_NAME'])===$scriptName){
				$this->_scriptUrl=$_SERVER['SCRIPT_NAME'];
			}
			else if(basename($_SERVER['PHP_SELF'])===$scriptName){
				$this->_scriptUrl=$_SERVER['PHP_SELF'];
			}
			else if(isset($_SERVER['ORIG_SCRIPT_NAME']) && basename($_SERVER['ORIG_SCRIPT_NAME'])===$scriptName){
				$this->_scriptUrl=$_SERVER['ORIG_SCRIPT_NAME'];
			}
			else if(($pos=strpos($_SERVER['PHP_SELF'],'/'.$scriptName))!==false){
				$this->_scriptUrl=substr($_SERVER['SCRIPT_NAME'],0,$pos).'/'.$scriptName;
			}
			else if(isset($_SERVER['DOCUMENT_ROOT']) && strpos($_SERVER['SCRIPT_FILENAME'],$_SERVER['DOCUMENT_ROOT'])===0){
				$this->_scriptUrl=str_replace('\\','/',str_replace($_SERVER['DOCUMENT_ROOT'],'',$_SERVER['SCRIPT_FILENAME']));
			}
			else{
				echo "Error: can not determine script entry point.";	
			}
		}
		return $this->_scriptUrl;
	}
	
	/**
	 * Create database connection
	 * @return database object
	 */
	public function db(){
		Helpers::trace("WebMain", "db");
		$db = new DbConnection($this->dsn,$this->username,$this->password);
		return $db;
	}

	/**
	 * Terminates the application.
	 * @param $status - exit status
	 * @param $exit - if true exit
	 */
	public function terminate($status = 0, $exit = true){
		if($exit){
			exit($status);
		}
	}
	
    /**
     * Get session Object
     * @return Object Session
     */
	public function getSession(){
		$session = new Session;
		return $session;
	}
	
	/**
	 * Initialize application
	 */
	protected function init(){
		Helpers::trace("WebMain", "init");
        $this->setExceptions();
		$this->processRequest();
	}
	
	/**
	 * Process request, call protected parseUrl, run controller
	 */
	public function processRequest(){
		Helpers::trace("WebMain", "processRequest");
		$route = $this->parseUrl();
		$this->runController($route);
	}
	
	/**
	 * Runs the application. This is the last function loaded on init
	 * For now used for debugging only
	 */
	public function run(){
		Helpers::trace("WebMain", "run");
	}
		
	/**
	 * Parse URL, check for routeVariable - should be set in Constants
	 * @return POST or GET request
	 */
	private function parseUrl(){
		Helpers::trace("WebMain", "parseUrl");
		if(isset($_GET[$this->routeVar])){
			return $_GET[$this->routeVar];
		}
		else if(isset($_POST[$this->routeVar])){
			return $_POST[$this->routeVar];
		}
		else{
			return '';
		}
	}
	
	/**
	 * Get protected path - protected directory
	 * @return string the path of protected directory
	 */
	private function getProtectedPath(){
		return $this->_protectedPath;
	}
	
	/**
	 * Get controller path
	 * @return controller path
	 */
	public function getControllerPath(){
		if($this->_controllerPath!==null){
			return $this->_controllerPath;
		}
		else{
			return $this->_controllerPath = $this->getProtectedPath().DIRECTORY_SEPARATOR.'controllers';
		}
	}
	
	/**
	 * Get controller
	 * @return this controller
	 */
	public function getController(){
		Helpers::trace("WebMain", "getController");
		return $this->_controller;
	}

	/**
	 * Set controller
	 * @param $value
	 */
	public function setController($value){
		Helpers::trace("WebMain", "setController");
		$this->_controller = $value;
	}
	
	/**
     * Create controller from the route
	 * If no route - load default controller - default controller is set in Constants
     * Calls MainController
     * 
     * @param type $route
     * 
     * @return null
     */
	public function createController($route){
		Helpers::trace("WebMain", "createController");

		if(($route = trim($route,'/')) === ''){
			$route = $this->defaultController;
		}
		Helpers::trace("WebMain", "createController", array("Route",$route));

		$route .= '/';
		while(($position = strpos($route,'/')) !== false){
			$id = substr($route,0,$position);
			if(!preg_match('/^\w+$/',$id)){
				return null;
			}
			if(!$this->caseSensitive){
				$id = strtolower($id);
			}
			$route = (string)substr($route, $position+1);
			if(!isset($basePath)){
				$basePath = $this->getControllerPath();
				$controllerID = '';
			}
			else{
				$controllerID.='/';
			}
			$className = ucfirst($id).'Controller';
			$classFile = $basePath.DIRECTORY_SEPARATOR.$className.'.php';
			Helpers::trace("WebMain", "createController", array("Class file",$classFile));

			if(is_file($classFile)){
				Helpers::trace("WebMain", "createController", array("File Exists?", "YES"));
				if(!class_exists($className,false)){
					require($classFile);
				}
				if(class_exists($className,false) && is_subclass_of($className,'MainController')){
					$id[0] = strtolower($id[0]);
					$newClass = new $className($controllerID.$id);
                    
					return array(
						$newClass,
						$this->parseActionParameters($route),
					);
				}
				return null;
			}
			$controllerID .= $id;
			$basePath .= DIRECTORY_SEPARATOR.$id;
		}
	}
	
	/**
	 * Run controller
	 * If can create controller - run it
	 * Otherwise (else) - run default controller's error action - should be set in the 
	 * default controller
	 */
	public function runController($route){
		Helpers::trace("WebMain", "runController", array("Route",$route));
		
		if(($c = $this->createController($route)) !== null){
			list($controller,$actionID) = $c;
			Helpers::trace("WebMain", "runController", array("Action",$actionID));
			$oldController = $this->_controller;
			$this->_controller = $controller;
			$controller->run($actionID);
			$this->_controller = $oldController;
		}
		else{
			$route = $this->defaultController."/error";
			$c = $this->createController($route);
			list($controller,$actionID) = $c;
			Helpers::trace("WebMain", "runController", array("Action",$actionID));
			$oldController = $this->_controller;
			$this->_controller = $controller;
			$controller->run($actionID);
			$this->_controller = $oldController;
		}
	}
	
	/**
	 * Parse action parameters fromt the url
     * @param String $pathInfo
	 * @return path
	 */
	protected function parseActionParameters($pathInfo){
		Helpers::trace("WebMain", "parseActionParameters");
		if(($position = strpos($pathInfo,'/')) !== false){
			$this->parsePathInfo((string)substr($pathInfo, $position+1));
			$actionID = substr($pathInfo, 0, $position);
			return $this->caseSensitive ? $actionID : strtolower($actionID);
		}
		else{
			return $pathInfo;
		}
	}
	
	/**
     * Parse path info
     * @param type $pathInfo
     * @return if no $pathInfo - return
     */
	public function parsePathInfo($pathInfo){
		Helpers::trace("WebMain", "parsePathInfo");
		if($pathInfo === ''){
			return;
		}
		$segments = explode('/',$pathInfo.'/');
		$n = count($segments);
		for($i=0; $i<$n-1; $i += 2){
			$key = $segments[$i];
			if($key === ''){
				continue;
			}
			$value = $segments[$i+1];
			if(($position = strpos($key,'[')) !== false && ($m = preg_match_all('/\[(.*?)\]/',$key,$matches)) > 0){
				$name = substr($key,0,$position);
				for($j=$m-1; $j>=0; $j--){
					if($matches[1][$j] === ''){
						$value = array($value);
					}
					else{
						$value = array($matches[1][$j]=>$value);
					}
				}
				if(isset($_GET[$name]) && is_array($_GET[$name])){
					$value = Helpers::mergeArray($_GET[$name],$value);
				}
				$_REQUEST[$name] = $_GET[$name] = $value;
			}
			else{
				$_REQUEST[$key] = $_GET[$key] = $value;
			}
		}
	}

    /** 
     * Get host name
     * @return string host name
     */
    public function getHost(){
        $http = new HttpRequest;
        return $http->getHost();
    }
    
    public function isAdmin(){
        Helpers::trace("WebMain", "isAdmin");
        $admin = false;
        $status = $this->getSession()->getValue('permissions');
        if($status==1){
            $admin = true;
        }
        return $admin;
    }
    
    public function getStatus(){
        return $status = $this->getSession()->getValue('status');
    }
    
    private function setExceptions(){
        set_exception_handler(array($this,'handleException'));
        set_error_handler(array($this,'handleError'),error_reporting());
    }
    
    public function handleException($e){
        restore_error_handler();
        restore_exception_handler();
    }
    
    public function handleError($e){
        restore_error_handler();
        restore_exception_handler();
    }
}
