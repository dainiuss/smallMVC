<?php
/**
 * Command class - has some basic SQL command wrappers
 * 
 * @param string $_connection
 * @param array  $_fetchMode
 * @param string $_text
 * @param string $_query
 * @param string $_statement
 * @param array  $params
 * 
 * @method __construct
 * @method setText
 * @method getText
 * @method queryAll
 * @method queryRow
 * @method cancel
 * @method getConnection
 * @method queryDatabase
 * @method prepare
 * @method executeQuery
 * @method getLastInsertId
 *
 * @author Dainius
 * @since 0.1
 */

class Command{
	
	private $_connection;
	private $_fetchMode = array(PDO::FETCH_ASSOC);
	private $_text;
	private $_query;
	private $_statement;
	private $params = array();
	
	/**
	 * Constructor
	 */
	public function __construct(DbConnection $connection,$query=null){
		Helpers::trace("Command", "__construct");
		$this->_connection = $connection;
		$this->setText($query);
	}
	
	/**
	 * Set text
	 */
	public function setText($value){
		Helpers::trace("Command", "setText");
		$this->_text = $value;
		$this->cancel();
		return $this;
	}
	
	/**
	 * Get text
	 */
	public function getText(){
		Helpers::trace("Command", "getText");
		return $this->_text;
	}
	
	/**
	 * Query all rows 
	 * @return query result
	 */
	public function queryAll($fetchAssociative=true,$params=array()){
		Helpers::trace("Command", "queryAll");
		return $this->queryDatabase('fetchAll',$fetchAssociative ? $this->_fetchMode : PDO::FETCH_NUM, $params);
	}
	
	/**
	 * Query one row
	 * @return query result
	 */
	public function queryRow($fetchAssociative=true,$params=array()){
		Helpers::trace("Command", "queryRow");
		return $this->queryDatabase('fetch',$fetchAssociative ? $this->_fetchMode : PDO::FETCH_NUM, $params);
	}

    /**
	 * Cancel statement
	 */
	public function cancel(){
		Helpers::trace("Command", "cancel");
		$this->_statement=null;
	}
	
	/**
	 * Get connection
	 * @return connection object
	 */
	public function getConnection(){
		Helpers::trace("Command", "getConnection");
		return $this->_connection;
	}
	
	/**
	 * Query database - used in queryRow and queryAll
	 * @return query result
	 */
	public function queryDatabase($method, $mode, $params = array()){
		Helpers::trace("Command", "queryDatabase");
		$params = array_merge($this->params,$params);
		$par = '';
		try{
			$this->prepare();
			if($params === array()){
				$this->_statement->execute();
			}
			else{
				$this->_statement->execute($params);
			}

			$mode   = (array)$mode;
			$result = call_user_func_array(array($this->_statement, $method), $mode);
			$this->_statement->closeCursor();

			return $result;
		}
		catch(Exception $e){
            echo $e->getMessage();
		}
	}
	
	/**
	 * PHP prepare wrapper
	 */
	public function prepare(){
		Helpers::trace("Command", "prepare");
		if($this->_statement == null){
			try{
				$this->_statement = $this->getConnection()->getPdoInstance()->prepare($this->getText());
			}
			catch(Exception $e){
				$e->getMessage();
			}
		}
	}
    
    /**
     * Execute query - this is used for insert and delete
     * @param array $parameters
     */
    public function executeQuery($parameters = array()){
        
        try{
            $this->prepare();
            
            if($parameters===array()){
                //Helpers::print_rr($this->_statement); die();
                $this->_statement->execute();
            }
            else{
				//Helpers::print_rr($this->_statement); die();
                $this->_statement->execute($parameters);
            }
        }catch(Exception $e){
            echo $e->getMessage();
        }
        $this->_statement->closeCursor();
    }
    
    /**
     * Get id of the last insert into the database
     * @return int last insert id
     */
    public function getLastInsertId(){
        return $this->getConnection()->getPdoInstance()->lastInsertId();
    }
	
	
	
	
}