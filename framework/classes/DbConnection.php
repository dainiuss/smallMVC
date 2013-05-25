<?php
/**
 * DbConnection class - has connection to the database functionality
 * @param boolean $_active
 * @param Object $_pdo
 * @param string $dsn
 * @param string $username
 * @param string $password
 * @param string $emulatePrepare	
 * @param string $charset
 * 
 * @method __construct
 * @method init
 * @method getDatabaseName
 * @method getActive
 * @method setActive
 * @method open
 * @method initialize
 * @method close
 * @method createPdoInstance
 * @methos createCommand
 * @method getConnectionStatus
 * @method getPdoInstance
 *
 * @author Dainius
 * @since 0.1
 */

class DbConnection{

	private $_active = false;
	private $_pdo;
	private $dsn;
	private $username;
	private $password;	
	public $emulatePrepare;		
	public $charset;
	
	/**
	 * Constructor
	 */
	public function __construct($dsn='',$username='',$password=''){
		Helpers::trace("DbConnection", "__construct");
		$this->dsn      = $dsn;
		$this->username = $username;
		$this->password = $password;
	}

	/**
	 * Initialize the connection
	 */
	public function init(){
		Helpers::trace("DbConnection", "init");
		$this->setActive(true);
	}
	
	/**
	 * Get database name
	 * @return database name
	 */
	public function getDatabaseName(){
		$dbName = explode('=', $this->dsn);
		return $dbName[2];
	}

	/**
	 * Check if connection is active
	 * @return true if active
	 */
	public function getActive(){
		Helpers::trace("DbConnection", "getActive");
		return $this->_active;
	}

	/**
	 * Set active connection
	 */
	public function setActive($value){
		Helpers::trace("DbConnection", "setActive");
		if($value != $this->_active){
			if($value){
				$this->open();
			}
			else{
				$this->close();
			}
		}
	}

	/**
	 * Opens database connection
	 */
	protected function open(){
		Helpers::trace("DbConnection", "open");
		if($this->_pdo === null){
			if(empty($this->dsn)){
				echo "Error: connection string can not be empty";
			}
			try{
				$this->_pdo=$this->createPdoInstance();
				$this->initialize($this->_pdo);
				$this->_active=true;
			}
			catch(PDOException $e){
				$e->getMessage();
			}
		}
	}
	
	/**
	 * Initializes active connection parameters
	 */
	protected function initialize($pdo){
		Helpers::trace("DbConnection", "initialize");
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		if($this->emulatePrepare !== null && constant('PDO::ATTR_EMULATE_PREPARES')){
			$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES,$this->emulatePrepare);
		}
		if($this->charset!==null){
			$driver = strtolower($pdo->getAttribute(PDO::ATTR_DRIVER_NAME));
			if(in_array($driver,array('mysql','mysqli'))){
				$pdo->exec('SET NAMES '.$pdo->quote($this->charset));
			}
		}
	}

	/**
	 * Close database connection
	 */
	protected function close(){
		Helpers::trace("DbConnection", "close");
		$this->_pdo    = null;
		$this->_active = false;
		$this->_schema = null;
	}

	/**
	 * Create PDO instance
	 */
	protected function createPdoInstance(){
		Helpers::trace("DbConnection", "createPdoInstance");
		return new PDO($this->dsn,$this->username,$this->password);
	}

	/**
	 * Create SQL command
	 * @return new command
	 */
	public function createCommand($query=null){
		Helpers::trace("DbConnection", "createCommand");
		$this->setActive(true);
		return new Command($this,$query);
	}
	
	/**
	 * Get connection status
	 */
	public function getConnectionStatus(){
		Helpers::trace("DbConnection", "getConnectionStatus");
		return $this->getAttribute(PDO::ATTR_CONNECTION_STATUS);
	}
	
	/**
	 * Get PDO instance
	 */
	public function getPdoInstance(){
		Helpers::trace("DbConnection", "getPdoInstance");
		return $this->_pdo;
	}

}