<?php
/**
 * Session class
 * 
 * @method __construct
 * @method init
 * @method open
 * @method close
 * @method destroy
 * @method isSessionStarted
 * @method getSessionID
 * @method getSessionName
 * @method regenerateID
 * @method getValue
 * @method addValue
 * @method getMultiValue
 * @method addMultiValue
 * @method removeKey
 * @method clearVariables
 * @method toArray
 * @method unsetKey
 *
 * @author Dainius
 * @since 0.1
 */
 
class Session {

	/**
	 * Constructor:
	 */
	public function __construct(){
	}

	public function init(){
        Helpers::trace("Session", "init");
		$this->open();
		register_shutdown_function(array($this,'close'));
	}

	/**
	 * Start session
	 */
	public function open(){
		Helpers::trace("Session", "open");
		@session_start();
	}

	/** 
	 * Close session
	 */
	public function close(){
		Helpers::trace("Session", "close");
		if(session_id()!==''){
			@session_write_close();
		}
	}

	/**
	 * Destroy session data
	 */
	public function destroy(){
		Helpers::trace("Session", "destroy");
		if(session_id()!==''){
			@session_regenerate_id(true); 
			@session_unset();
			@session_destroy();
			@setcookie(session_name(), '', time() - 42000,
		        $params["path"], $params["domain"],
		        $params["secure"], $params["httponly"]
		    );
		}
	}

	/**
	 * Check if session started
	 * @return true if session started
	 */
	public function isSessionStarted(){
		Helpers::trace("Session", "isSessionStarted");
		return session_id()!=='';
	}

	/**
	 * Get session ID
	 * @return session id - wrapper method for PHP function session_id()
	 */
	public function getSessionID(){
		Helpers::trace("Session", "getSessionID");
		return session_id();
	}

	/**
	 * Get session name
	 * @return session name - wrapper method for PHP function session_name()
	 */
	public function getSessionName(){
		Helpers::trace("Session", "getSessionName");
		return session_name();
	}
	
	/**
	 * Regenerate session ID
	 * wrapper method for PHP function session_regenerate_id()
	 */
	public function regenerateID($delete_old_session = false){
        Helpers::trace("Session", "regenerateID");
		session_regenerate_id($delete_old_session);
	}
    
    /**
     * Get session value
     * @param type $key
     * @param type $default
     * @return variable
     */
    public function getValue($key,$default = null){
        return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
    }
    
    /**
     * Adds value to the key
     * @param type $key
     * @param type $value
     */
    public function addValue($key,$value){
        $_SESSION[$key] = $value;
    }
    
    public function getMultiValue($key1,$key2,$default = null){
        return isset($_SESSION[$key1][$key2]) ? $_SESSION[$key1][$key2] : $default;
    }
    
    /**
     * Adds value to the multidimentional key
     * @param type $key
     * @param type $value
     */
    public function addMultiValue($key1,$key2,$value){
        $_SESSION[$key1][$key2] = $value;
    }
    
    /**
     * Unset key
     * @param type $key
     * @return null pr the value removed
     */
    public function removeKey($key){
        if(isset($_SESSION[$key])){
            $value = $_SESSION[$key];
            unset($_SESSION[$key]);
            return $value;
        }
        else{
            return null;
        }
    }

    /**
     * Clears Session variables
     */
    public function clearVariables(){
        foreach(array_keys($_SESSION) as $key)
        unset($_SESSION[$key]);
    }
    
    /**
     * Return an array of session values
     * @return array or session values
     */
    public function toArray(){
        return $_SESSION;
    }
    
    /**
     * Unset session key
     * @param type $key
     */
    public function unsetKey($key){
        unset($_SESSION[$key]);
    }

}
