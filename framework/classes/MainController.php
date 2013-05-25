<?php
/**
 * MainController class
 *
 * @param $layout - the default layout of the website. It's in views/layouts
 * @param $defaultAction - default action to call if no action specified
 * @param $_id
 * @param $_action
 * @param $_pageTitle
 * @param $_cachingStack
 * @param $_clips
 * @param $_dynamicOutput
 * @param $_pageStates
 * @param $_module
 * 
 * @method __constructor
 * @method run
 * @method runAction
 * @method getActionParams
 * @method invalidActionParams
 * @method createAction
 * @method getAction
 * @method setAction
 * @method getId
 * @method getRoute
 * @method getViewPath
 * @method getViewFile
 * @method getLayoutFile
 * @method resolveViewFile
 * @method render
 * @method renderFile
 * @method renderInternal
 * @method renderPartial
 * @method getPageTitle
 * @method setPageTitle
 * @method redirect
 * 
 * @author Dainius
 * @since 0.1
 */

class MainController {

	public $layout = 'main';
	public $defaultAction='index';

	private $_id;
	private $_action;
	private $_pageTitle;
	private $_cachingStack;
	private $_clips;
	private $_dynamicOutput;
	private $_pageStates;
	private $_module;

	/**
     * Constructor
     * Open the session here
     * @param type $id
     * @param type $module
     */
	public function __construct($id, $module=null){
		Helpers::trace("MainController", "__construct");
		Main::getApplication()->getSession()->open();
		$this->_id=$id;
	}

	/**
	 * Run action wrapper method 
     * Create action and run it
	 * @param String $actionID
	 */
	public function run($actionID){
		if(($action = $this->createAction($actionID)) !== null){
			Helpers::trace("MainController", "run", array("Action", $actionID));			
			$this->runAction($action);
		}
		else {
			$action = $this->createAction("error");
			$this->runAction($action);
		}
	}
	
	/**
	 * Run action in the controller 
     * Check if action has any parameters and run with those parameters
	 * @param String $action
	 */
	public function runAction($action){
		Helpers::trace("MainController", "runAction");
		$priorAction   = $this->_action;
		$this->_action = $action;
		$action->runWithParams($this->getActionParams());
		$this->_action = $priorAction;
	}

	/**
	 * Get action parameters from the GET request
	 * @return action parameters
	 */
	public function getActionParams(){
		Helpers::trace("MainController", "getActionParams");
		return $_GET;
	}

	/**
	 * Show error if action parameters are invalid
	 */
	public function invalidActionParams($action){
		echo 'Error: Your request is invalid.';
	}

	/**
	 * Creates action, if there is no action - assign default action
	 * @return $actionID
	 */
	public function createAction($actionID){
		Helpers::trace("MainController", "createAction", array("Action", $actionID));
		if($actionID === ''){
			$actionID=$this->defaultAction;
		}
		if(method_exists($this,'action'.$actionID) && strcasecmp($actionID,'s')){
			Helpers::trace("MainController", "getActionParams", array("Method Exists", "action".ucfirst($actionID)));
			return new MainAction($this,$actionID);
		}
		else{
			// Handle error in $this->run()
		}
	}

	/**
	 * Action getter
	 * @return action
	 */
	public function getAction(){
		Helpers::trace("MainController", "getAction");
		return $this->_action;
	}

	/**
	 * Action setter - set action
	 */
	public function setAction($value){
		Helpers::trace("MainController", "setAction");
		$this->_action = $value;
	}

	/**
	 * Get controller id
	 * @return controller ID
	 */
	public function getId(){
		Helpers::trace("MainController", "getId", array("Controller Name", ucfirst($this->_id)."Controller"));
		return $this->_id;
	}

	/**
	 * Get route, if we have action - append action id to the route
	 * @return the route from the URL
	 */
	public function getRoute(){
		Helpers::trace("MainController", "getRoute");
		if(($action=$this->getAction())!==null){
			return $this->getId().'/'.$action->getId();
		}
		else{
			return $this->getId();
		}
	}

	/**
	 * Get this controller's view path in protected/views
	 * @return view path
	 */
	public function getViewPath(){
		Helpers::trace("MainController", "getViewPath");
		return Base::getViewPath().DIRECTORY_SEPARATOR.$this->getId();
	}

	/**
	 * Gets view file
	 * @return view file
	 */
	public function getViewFile($viewName){
		$basePath = Main::getViewPath();
		$file = $this->resolveViewFile($viewName,$this->getViewPath(),$basePath);
		Helpers::trace("MainController", "getViewFile", array("View File", $file));
		return $file;
	}

	/**
	 * Get layout file in protected/views/layouts
	 * @return layout file
	 */
	public function getLayoutFile($layoutName){
		if($layoutName === false){
			return false;
		}
		if(empty($layoutName)){
			$layoutName=$this->layout;
		}
		$layoutPath = Base::getLayoutPath();
		$viewPath   = Base::getViewPath();		
		$layoutFile = $this->resolveViewFile($layoutName,$layoutPath,$viewPath);
		Helpers::trace("MainController", "getViewFile", array("Layout File", $layoutFile));
		return $layoutFile;
	}

	/**
	 * Finds a view file based on its name.
	 * @return view file
	 */
	public function resolveViewFile($viewName,$viewPath,$basePath){
		Helpers::trace("MainController", "resolveViewFile");
		if(empty($viewName)){
			return false;
		}
		
		$extension = '.php';
		
		if($viewName[0]==='/'){
			if(strncmp($viewName,'//',2)===0){
				$viewFile=$basePath.$viewName;
			}
			else{
				$viewFile=$viewName;
			}
		}
		else{
			$viewFile = $viewPath.DIRECTORY_SEPARATOR.$viewName;
		}
		
		if(is_file($viewFile.$extension)){
			Helpers::trace("MainController", "resolveViewFile", array("Is it a file?", "YES"));
			return $viewFile.$extension;
		}
		else if($extension!=='.php' && is_file($viewFile.'.php')){
			return $viewFile.'.php';
		}
		else{
			return false;
		}
	}
	

	/**
	 * Render a view
	 * @return output
	 */
	public function render($view,$data=null,$return=false){
		Helpers::trace("MainController", "render");

		$output = $this->renderPartial($view,$data,true);
		if(($layoutFile = $this->getLayoutFile($this->layout)) !== false){
			$output = $this->renderFile($layoutFile,array('content'=>$output),true);
		}

		if($return){
			return $output;
		}
		else{
			echo $output;
		}
	}

	/**
	 * Wrapper method for renderInternal()
	 * @return $content
	 */
	public function renderFile($viewFile,$data=null,$return=false){
		Helpers::trace("MainController", "renderFile");
		$content = $this->renderInternal($viewFile,$data,$return);
		return $content;
	}
	
	/**
	 * Renders a view file
	 */
	private function renderInternal($_viewFile_,$_data_ = null, $_return_ = false){
		Helpers::trace("MainController", "renderInternal");
		if(is_array($_data_)){
			extract($_data_,EXTR_PREFIX_SAME,'data');
		}
		else{
			$data = $_data_;
		}
		if($_return_){
			ob_start();
			ob_implicit_flush(false);
			require($_viewFile_);
			return ob_get_clean();
		}
		else{
			require($_viewFile_);
		}
	}


	/**
	 * Renders a partial view - used for rendering without layout, example AJAX
	 */
	public function renderPartial($view,$data=null,$return=false,$processOutput=false){
		Helpers::trace("MainController", "renderPartial");

		if(($viewFile = $this->getViewFile($view))!==false){
			$output = $this->renderFile($viewFile,$data,true);

			if($return){
				return $output;
			}
			else{
				echo $output;
			}
		}
		else{
			echo "Error: Can not render a partial view.";
		}
	}

	/**
	 * Get page title
	 * @return string the page title.
	 */
	public function getPageTitle(){
		if($this->_pageTitle!==null){
			return $this->_pageTitle;
		}
		else
		{
			$name = ucfirst(basename($this->getId()));
			if($this->getAction()!==null && strcasecmp($this->getAction()->getId(),$this->defaultAction)){
				return $this->_pageTitle = Main::getApplication()->website_name.' - '.ucfirst($this->getAction()->getId()).' '.$name;
			}
			else{
				return $this->_pageTitle = Main::getApplication()->website_name.' - '.$name;
			}
		}
	}

	/**
	 * @param string $value the page title.
	 */
	public function setPageTitle($value){
		Helpers::trace("MainController", "setPageTitle");
		$this->_pageTitle = $value;
	}

	/**
	 * Redirects the browser to the specified index.php?r=controller/action or URL
	 * @param $url -  the URL to be redirected to
	 * @param $terminate - true if to terminate this application after calling the method
	 * @param $statusthe  - HTTP status code
	 */
	public function redirect($url,$terminate=true,$status=302){
		Helpers::trace("MainController", "redirect", array("Redirect to URL", $url));
		$request = new HttpRequest;
		$request->redirect($url,$terminate,$status);
	}

}
