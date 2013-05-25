<?php
/**
 * Authentication class
 * 
 * @method __construct
 * @method encrypt
 * @method verify
 * 
 * @author dainius
 * @since 0.1
 */

class Authentication{
    
    private $_username;
    private $_password;
    private $_table;
    
    /**
	 * Constructor
	 */
	public function __construct($username, $password){
		Helpers::trace("Authentication", "__construct");
        $this->_username = Helpers::clean($username);
        $this->_password = $this->encrypt(Helpers::clean($password));
        $this->_table = Constants::USER_TABLE;
	}
    
    /**
     * Encrypt the string
     * @param type $string
     * @return string hash string
     */
    private static function encrypt($string="") {
        Helpers::trace("Authentication", "encrypt");
		$hash = Constants::HASH;
		if ($hash=="sha512")
			return crypt($string, Constants::SALT);
		if ($hash=="md5")
			return md5($string);
		if ($hash=="sha1")
			return sha1($string);
		else
			return hash($hash,$string);
	}
    
    /**
     * Verify user
     * @return int 1 - if user is verified or 0 otherwise
     */
    public function verify(){
        Helpers::trace("Authentication", "verify");
        $user = "";
        $pass = "";
        $code = 0;
        $db = Main::getApplication()->db();
        $sql = '
            SELECT id, username, password, permissions, status
            FROM ' . $this->_table . '
            WHERE username = "'. $this->_username.'"
        ';
        $command = $db->createCommand($sql);
        $result = $command->queryRow();

        $user = Helpers::clean($result['username']);
        $pass = Helpers::clean($result['password']);
        
        if($user!==null){
            if($this->_password===$pass){
                $code = 1;
            }
        }
        return $code;
	}
}