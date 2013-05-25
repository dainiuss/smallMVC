<?php
/**
 * Base class file - do not use this class directlly, use Main as main is the
 * child of Base
 * 
 * @param Object $_application
 * @param string $_layoutPath
 * @param array $_loadClassDirectories
 * @param array $_loadClasses
 * 
 * @method getWebPath
 * @method getFrameworkPath
 * @method getViewPath
 * @method getProtectedPath
 * @method getLayoutPath
 * @method getApplication
 * @method setApplication
 * @method createApplication
 * @method renderApplication
 * @method autoloader
 *
 * @author Dainius
 * @since 0.1
 */

/* Define required constants */
defined('DEBUG') or define('DEBUG',false);
defined('WEB_PATH') or define('WEB_PATH',dirname(dirname(__FILE__)));
defined('MAIN_PATH') or define('MAIN_PATH',dirname(__FILE__));
defined('PROTECTED_PATH') or define('PROTECTED_PATH',dirname(dirname(__FILE__)).'/protected');
defined('DIRECTORY_SEPARATOR') or define('DIRECTORY_SEPARATOR','/');
defined('VIEW_PATH') or define('VIEW_PATH',PROTECTED_PATH.'/views');

abstract class Base{

	private static $_application;
	private static $_layoutPath = VIEW_PATH;
    
    /**
	 * @array of directories with classes inside to be autoloaded loaded
	 */
	private static $_loadClassDirectories = array(
		'models'     => '/../protected/models/',
		'components' => '/../protected/components/',
	);

	/**
	 * @array of classes to be autoloaded loaded
	 */
	private static $_loadClasses = array(
		'HttpRequest'    => '/classes/HttpRequest.php',
		'Session'        => '/classes/Session.php',
		'Captcha'        => '/classes/Captcha.php',
        'Authentication' => '/classes/Authentication.php',
		'WebMain'        => '/classes/WebMain.php',
		'MainController' => '/classes/MainController.php',
		'MainAction'     => '/classes/MainAction.php',
		'MainModel'      => '/classes/MainModel.php',
		'DbConnection'   => '/classes/DbConnection.php',
		'Command'        => '/classes/Command.php',
		'Helpers'        => '/classes/Helpers.php',
	);
	
	/**
	 * Get web directory
	 * @return string the path of view directory
	 */
	public static function getWebPath(){	
		return WEB_PATH;
	}
	
	/**
	 * Get framework path - framework directory
	 * @return string the path of the framework
	 */
	public static function getFrameworkPath(){
		return MAIN_PATH;
	}
	
	/**
	 * Get view path - protected/view directory
	 * @return string the path of view directory
	 */
	public static function getViewPath(){	
		return VIEW_PATH;
	}
	
	/**
	 * Get protected path - protected directory
	 * @return string the path of protected directory
	 */
	public static function getProtectedPath(){	
		return PROTECTED_PATH;
	}
	
	/**
	 * Get main layout path directory
	 * @return string the path of main layout directory
	 */
	public static function getLayoutPath(){
		$path = VIEW_PATH.DIRECTORY_SEPARATOR.'layouts';
		return $path;
	}

	/**
	 * Get application
	 * @return this application
	 */
	public static function getApplication(){
		return self::$_application;
	}
	
	/**
	 * Set application
	 * @param application
	 */
	public static function setApplication($application){
		Helpers::trace("Base", "setApplication");
		if(self::$_application === null || $application === null){
			self::$_application = $application;
		}
		else{
			echo 'Error creating application';
		}
	}

	/**
	 * Create application - it is called in the main index file
     * The first method to be called
     * @param String $arguments
	 * @return the class to create - called from renderApplication
	 */
	public static function createApplication($arguments = null){
		Helpers::trace("Base", "createApplication");
		return self::renderApplication('WebMain',$arguments);
	}

	/**
	 * Render application - private class called in the createApplication class
     * Creates a new class with the name passed as argument $class
     * Comment: this loads the WebMain class which has all framework functionality
     * 
     * @param type $class
     * @param type $arguments
     * @return a new class
     */
	private static function renderApplication($class,$arguments = null){
		Helpers::trace("Base", "renderApplication");
		return new $class($arguments);
	}

	/**
	 * Autoloader - automaticly loads classes to the application
	 * Also it loads all classes in the directories specified in $_loadClassDirectories
	 * List of classes is in the $_loadClasses below
	 * @param string $name - the name of the class to autoload
	 * @return true if class loaded successfully
	 */
	public static function autoloader($name){
		if(isset(self::$_loadClassDirectories)){
			foreach (self::$_loadClassDirectories as $directory) {
		    	if (file_exists(MAIN_PATH.$directory . $name . '.php')) {
		    		include(MAIN_PATH.$directory . $name . '.php');
		    	}
		    }
		}

		if(isset(self::$_loadClasses[$name])){
			include(MAIN_PATH.self::$_loadClasses[$name]);
		}
		
		return true;
	}
}

/**
 * Register autoloader
 */
spl_autoload_register(array('Base','autoloader'));

