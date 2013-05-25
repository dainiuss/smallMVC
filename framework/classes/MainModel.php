<?php
/**
 * MainModel class
 * 
 * @param $_newRecord
 * @param $_valid
 * @param $_lastInsertId
 * 
 * @method __construct
 * @method __call
 * @method setAttributes
 * @method setIsValid
 * @method setIsNewRecord
 * @method getIsNewRecord
 * @method tableName
 * @method getDatabaseName
 * @method getDatabaseName
 * @method attributeNames
 * @method save
 * @method insertData
 * @method updateData
 * @method setLastInsertId
 * @method getLastInsertId
 * @method loadModelData
 * @method validationRegex
 * @method invalidValue
 *
 * @author Dainius
 * @since 0.1
 */

class MainModel {

    private $_newRecord;
    private $_valid;
    private $_lastInsertId;

    /**
     * Constructor
     */
    public function __construct() {
        $this->setIsNewRecord(true);
        $this->_valid = false;
    }

    /**
     * Magic method - generates all setters and getters
     * 
     * @param type $name
     * @param type $args
     * @return type
     */
    public function __call($name, $args) {
        // Get 1st 3 characters from getProperty
        $prefix = substr($name, 0, 3);

        // Get the rest characters from getProperty
        $property = strtolower($name[3]) . substr($name, 4);
        switch ($prefix) {
            case "get":
                return $this->$property;
                break;
            case "set":
                if (count($args) == 1) {
                    $this->$property = $args[0];
                } else {
                    echo 'Only 1 argument accepted.';
                    die();
                }
                break;
            default:
                echo 'Should be set/get property only.';
                die();
        }
    }

    /**
     * Generates setters and sets attributes to all parameters
     * @param type $array
     */
    public function setAttributes($array) {
        Helpers::trace("MainModel", "setAttributes");
        foreach ($array as $k => $v) {
            $param = ucfirst($k);
            $funct = 'set' . $param;
            $this->$funct($v);
        }
    }

    /**
     * Set is valid to true/false - used for form validation
     * @param type $value
     */
    public function setIsValid($value) {
        $this->_valid = $value;
    }

    /**
     * isValid getter, checks if the form fields are all valid
     * @return type
     */
    public function isValid() {
        return $this->_valid;
    }

    /**
     * Set is new record to true or false
     * 
     * @param type $value
     */
    public function setIsNewRecord($value) {
        $this->_newRecord = $value;
    }

    /**
     * Get is new record value
     * @return true or false
     */
    public function getIsNewRecord() {
        return $this->_newRecord;
    }

    /**
     * Get table name
     * @return table name - the name of the model prefixed with the table prefix
     */
    public function tableName() {
        return Constants::TBL_PREFIX . strtolower(get_class($this));
    }

    /**
     * Get database name - wrapper method for DbConnection getDatabaseName()
     */
    public function getDatabaseName() {
        $db = Main::getApplication()->db()->getDatabaseName();
        return $db;
    }

    /**
     * Return the array of column names
     * @return attributes
     */
    public function attributeNames() {
        $attributes = array();
        $db = Main::getApplication()->db();
        $sql = "
			SELECT `COLUMN_NAME` 
			FROM `INFORMATION_SCHEMA`.`COLUMNS` 
			WHERE `TABLE_SCHEMA` = '" . $db->getDatabaseName() . "' 
			AND `TABLE_NAME` = '" . $this->tableName() . "'
		";
        $command = $db->createCommand($sql);
        $result = $command->queryAll();
        foreach ($result as $k => $v) {
            array_push($attributes, $v['COLUMN_NAME']);
        }
        return $attributes;
    }

    /**
     * Save the record to the database - call insert or update
     * @param array $parameters
     * @return status
     */
    public function save($parameters = array()) {
        $status = "";
        if ($this->getIsNewRecord()) {
            $status = $this->insertData($parameters);
        } else {
            $status = $this->updateData($parameters);
        }
        return $status;
    }

    /**
     * Insert record into the database - set last insert id on insert
     * 
     * @param array $parameters
     * @param boolean $escape - to escape the insert or not
     * @return last insert id - can be used for JOIN tables
     */
    private function insertData($parameters = array(), $escape = true) {
        $db = Main::getApplication()->db();

        $dataArray = array();

        /* No data escape */
        if (!$escape) {
            /* Shift attributes to get rid of first column - it's AUTO_INCREMENT id */
            array_shift($parameters);
            $dataArray = $parameters;
            $data = implode('","', $dataArray);
            $data = '"' . $data . '"';
            $params = array(); // this will be asked later if it's array - needs to be array if no escape
        }
        /* Data escape */ 
        else {
            /* Shift attributes to get rid of first column - it's AUTO_INCREMENT id */
            array_shift($parameters);

            foreach ($parameters as $name => $value) {
                $dataArray[] = ':' . $name;
                $params[':' . $name] = $value;
            }
            $data = implode(',', $dataArray);
        }

        $attributes = $this->attributeNames();

        /* Shift attributes to get rid of first column */
        array_shift($attributes);
        $columns = implode(',', $attributes);
        $sql = '
            INSERT INTO ' . $this->tableName() . '(' . $columns . ')
            VALUES(' . $data . ')
        ';
        $command = $db->createCommand($sql);

        $this->setText($sql);
        $command->executeQuery($params);
        $lastInsert = $command->getLastInsertId();
        $this->setLastInsertId($lastInsert);
        return $lastInsert;
    }

    /**
     * Update data
     * 
     * @param array $parameters
     * @param boolean $escape - to escape the insert or not
     * @return boolean
     */
    private function updateData($parameters = array(), $escape = true) {
        $db = Main::getApplication()->db();

        $primary_key = $parameters[0];
        $primary_key_name;
        $values_string = "";

        $dataArray = array();

        /* No data escape */
        if (!$escape) {
            /* Shift attributes to get rid of first column - it's AUTO_INCREMENT id */
            array_shift($parameters);
            $dataArray = $parameters;
            $data = $dataArray;
            $params = array(); // this will be asked later if it's array - needs to be array if no escape
        }
        /* Data escape */ else {
            /* Shift attributes to get rid of first column - it's AUTO_INCREMENT id */
            array_shift($parameters);

            foreach ($parameters as $name => $value) {
                $dataArray[] = ':' . $name;
                $params[':' . $name] = $value;
            }
            $data = $dataArray;
        }

        $attributes = $this->attributeNames();
        $primary_key_name = $attributes[0];

        /* Shift attributes to get rid of first column */
        array_shift($attributes);
        /* Get column - value string */
        for ($i = 0; $i < sizeof($attributes); $i++) {
            $values_string .= $attributes[$i] . ' = ' . $data[$i] . ', ';
        }
        $values_string = rtrim($values_string, ', ');

        $sql = '		
			UPDATE ' . $this->tableName() . ' 
			SET ' . $values_string . ' 
			WHERE ' . $primary_key_name . ' = ' . $primary_key . '
        ';

        $command = $db->createCommand($sql);

        $this->setText($sql);
        //Helpers::print_rr($params);
        //Helpers::print_rr($this->getText($sql)); die();

        $command->executeQuery($params);
        return true;
    }

    /**
     * Set last insert id when inserting data into the databse
     * @param String $value
     */
    public function setLastInsertId($value) {
        $this->_lastInsertId = $value;
    }

    /**
     * Get last insert id
     * @return int last insert id
     */
    public function getLastInsertId() {
        return $this->_lastInsertId;
    }

    /**
     * Load model data
     * @param type $whereColumn - WHERE in the SQL statement
     * @param type $whereValue  - VALUE in SQL statement
     * @return type
     */
    public function loadModelData($whereColumn, $whereValue) {
        $db = Main::getApplication()->db();
        $attributes = $this->attributeNames();
        $columns = implode(',', $attributes);
        $sql = '
			SELECT ' . $columns . '
			FROM ' . $this->tableName() . ' 
			WHERE ' . $whereColumn . ' = "' . $whereValue . '"
		';
        $command = $db->createCommand($sql);
        $result = $command->queryRow();
        return $result;
    }

    /**
     * Validation regexes
     * @return an array of regexes
     */
    public function validationRegex() {
        return array(
            'date'     => '/\d{4}-\d{2}-\d{2}/',
            'sentence' => '([a-zA-Z].*?){5,}',
        );
    }

    /**
     * Check if value is invalid
     * @param type $value the value to check
     * @param type $regularExp - regular expression
     * @return true is invalid, false if valid
     */
    public function invalidValue($value, $regularExp) {
        $isInvalid = true;
        if (filter_var($value, FILTER_VALIDATE_REGEXP, array(
                    "options" => array(
                        "regexp" => $regularExp
                    )
                        )
                )
        ) {
            $isInvalid = false;
        }
        return $isInvalid;
    }

}

