<?php
/**
 * MainAction class
 * 
 * @param $_id
 * @param $_controller
 * 
 * @method __constructor
 * @method getController
 * @method getId
 * @method run
 * @method runWithParams
 * @method runWithParamsInternal
 *
 * @author Dainius
 * @since 0.1
 */
 
class MainAction {
    
	private $_id;
	private $_controller;
	
	/**
     * Constructor
     * @param Object $controller
     * @param string $id
     */
	public function __construct($controller,$id){
		Helpers::trace("MainAction", "__construct");
		$this->_controller = $controller;
		$this->_id = $id;
	}

	/**
	 * Get controller
	 * @return controller
	 */
	public function getController(){
		Helpers::trace("MainAction", "getController");
		return $this->_controller;
	}

	/**
	 * Get action id
	 * @return action id
	 */
	public function getId(){
		Helpers::trace("MainAction", "getId", array("Action",$this->_id));
		return $this->_id;
	}
	
	/**
	 * Run the action for this controller
	 */
	public function run(){
		Helpers::trace("MainAction", "run");
		$method = 'action'.$this->getId();
		$this->getController()->$method();
	}

	/**
	 * Run action with parameters passed to them
	 */
	public function runWithParams($params){
		Helpers::trace("MainAction", "runWithParams");
		$methodName = 'action'.$this->getId();
		$controller = $this->getController();
		$method     = new ReflectionMethod($controller, $methodName);
		if($method->getNumberOfParameters()>0){
			return $this->runWithParamsInternal($controller, $method, $params);
		}
		else{
			return $controller->$methodName();
		}
	}
	
	/**
	 * Helper method for the runWithParameters method
	 * @return true or false
	 */
	private function runWithParamsInternal($object, $method, $params){
		Helpers::trace("MainAction", "runWithParamsInternal");
		$ps = array();
		foreach($method->getParameters() as $i=>$param){
			$name = $param->getName();
			if(isset($params[$name])){
				if($param->isArray()){
					$ps[]=is_array($params[$name]) ? $params[$name] : array($params[$name]);
				}
				else if(!is_array($params[$name])){
					$ps[]=$params[$name];
				}
				else{
					return false;
				}
			}
			else if($param->isDefaultValueAvailable()){
				$ps[] = $param->getDefaultValue();
			}
			else{
				return false;
			}
		}
		$method->invokeArgs($object,$ps);
		return true;
	}

}
