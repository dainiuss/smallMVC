<?php
/**
 * Console class
 *
 * @author dainius
 */
require_once('PageGenerator.php');

class Console {
    
    private $protected;
    private $currentDir;
    private $frameworkDir;
    private $controllerDir;
    private $viewDir;
    private $modelDir;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->currentDir = getcwd();
        $this->protected = $this->currentDir . "/protected/";
        $this->frameworkDir = dirname(dirname(__FILE__));
        $this->controllerDir = $this->protected . "controllers/";
        $this->viewDir = $this->protected . "views/";
        $this->modelDir = $this->protected . "models/";
    }
    
    /**
     * Run the menu
     */
    public function run(){
        $this->menu();
    }
    
    /** 
     * Menu with available operation display
     */
    private function menu(){
        while(true){
            echo "\n---------------------------\n";
            echo "MENU: \n";
            echo "1 - generate a new website\n";
            echo "2 - generate a new controller\n";
            echo "3 - generate a new model\n";
            echo "4 - list this directory\n";
            echo "0 - exit\n";
            echo "---------------------------\n";
            $ans = readline('Enter selection: ');
            switch ($ans) {
                case 0:
                    echo "Exiting...\n";
                    exit();
                case 1:
                    $this->newWebsite();
                    break;
                case 2:
                    if(!file_exists($this->protected) && !is_dir($this->protected)) {
                        echo "Website doesn't exist. Exiting...\n";
                        exit();
                    }
                    $this->newController();
                    break;
                case 3:
                    if(!file_exists($this->protected) && !is_dir($this->protected)) {
                        echo "Website doesn't exist. Exiting...\n";
                        exit();
                    }
                    $this->newModel();
                    break;
                case 4:
                    $this->listDirectory();
            }
        }
    }
    
    /**
     * Generate a new website
     */
    private function newWebsite(){
        echo "---> Trying to generate a new website...\n";
        echo $this->protected;
        echo "\n";
        if(!file_exists($this->protected) && !is_dir($this->protected)) {
            echo "---> Creating directory structure...\n";
            $this->generateDirStructure();
            $this->generateStaticFiles();
            $this->copyVendorLibraries();
            echo "---> DONE!\n";
        }
        else {
            echo "Directory structure already exists!!!\n";
            $ans = readline('Would you like to overwrite your existing site(y/n)?: ');
            if('n' == trim(strtolower($ans))) {
                echo "---> Your website was NOT overwritten...\n";
            }
            else {
                echo "---> Removing existing directory structure...\n";
                $this->removeDirs();
                $this->removeFiles();
                echo "---> Creating a NEW directory structure...\n";
                $this->generateDirStructure();
                $this->generateStaticFiles();
                $this->copyVendorLibraries();
                echo "---> DONE!\n";
            }
        }
    }
    
    /**
     * Generate a new constroller
     */
    private function newController(){
        $ans = readline('Enter controller name: ');
        $controller = ucfirst(trim($ans))."Controller";
        $viewDir = trim(strtolower($ans));
        $controllerFile = $controller.".php";
        if(!file_exists($this->controllerDir.$controllerFile)) {
            PageGenerator::createFile($this->controllerDir.$controllerFile, PageGenerator::dynamicData($controller,'controller'));
            mkdir($this->viewDir.$viewDir,0751,true);
            PageGenerator::createFile($this->viewDir.$viewDir."/index.php", PageGenerator::dynamicData("index.php",'view'));
            echo "\n---> Controller with a name " . $controller . " was generated ...\n";
        }
        else{
            echo "File with the name " . $controllerFile . " already exists\n";
            $ans = readline('Would you like to overwrite it(y/n)? ');
            if('n' == trim(strtolower($ans))) {
                echo "\n---> Controller was NOT overwritten...\n";
            }
            else{
                unlink($this->controllerDir.$controllerFile);
                $this->rrmdir($this->viewDir.$viewDir);
                PageGenerator::createFile($this->controllerDir.$controllerFile, PageGenerator::dynamicData($controller,'controller'));
                mkdir($this->viewDir.$viewDir,0751,true);
                PageGenerator::createFile($this->viewDir.$viewDir."/index.php", PageGenerator::dynamicData("index.php",'view'));
                echo "\n---> Controller with a name " . $controller . " was generated ...\n";
            }
        }
    }
    
    /**
     * Generate a new model
     */
    private function newModel(){
        $ans = readline('Enter model name: ');
        $model = ucfirst(trim($ans));
        $modelFile = $model.".php";
        if(!file_exists($this->modelDir.$modelFile)) {
            PageGenerator::createFile($this->modelDir.$modelFile, PageGenerator::dynamicData($model,'model'));
            echo "\n---> Model with a name " . $model . " was generated ...\n";
        }
        else{
            echo "File with the name " . $modelFile . " already exists\n";
            $ans = readline('Would you like to overwrite it(y/n)? ');
            if('n' == trim(strtolower($ans))) {
                echo "\n---> Model was NOT overwritten...\n";
            }
            else{
                unlink($this->modelDir.$modelFile);
                PageGenerator::createFile($this->modelDir.$modelFile, PageGenerator::dynamicData($model,'model'));
                echo "\n---> Model with a name " . $model . " was generated ...\n";
            }
        }
    }
    
    /**
     * Remove directory recursivelly
     * @param string $dir
     */
    private function rrmdir($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir."/".$object) == "dir"){
                        $this->rrmdir($dir."/".$object);
                    }
                    else{
                        unlink($dir."/".$object);
                    }
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }
    
    /**
     * Generate directory structure
     */
    private function generateDirStructure(){
        mkdir("css",0751,true);
        mkdir("images",0771,true);
        mkdir("js",0751,true);
        mkdir("lib",0751,true);
        mkdir("protected",0751,true);
        mkdir("protected/components",0751,true);
        mkdir("protected/config",0751,true);
        mkdir("protected/controllers",0751,true);
        mkdir("protected/data",0751,true);
        mkdir("protected/models",0751,true);
        mkdir("protected/views",0751,true);
        mkdir("protected/views/layouts",0751,true);
        mkdir("protected/views/web",0751,true);
    }
    
    /**
     * Generate base wesbite files
     */
    private function generateStaticFiles(){
        PageGenerator::createFile("protected/components/Constants.php", PageGenerator::staticData('constants'));
        PageGenerator::createFile("protected/config/config.php", PageGenerator::staticData('config'));
        PageGenerator::createFile("protected/controllers/WebController.php", PageGenerator::staticData('webcontroller'));
        PageGenerator::createFile("protected/data/database.php", PageGenerator::staticData('database'));
        PageGenerator::createFile("protected/models/Test.php", PageGenerator::staticData('testmodel'));
        PageGenerator::createFile("protected/views/layouts/main.php", PageGenerator::staticData('mainview'));
        PageGenerator::createFile("protected/views/web/about.php", PageGenerator::staticData('aboutview'));
        PageGenerator::createFile("protected/views/web/contact.php", PageGenerator::staticData('contactview'));
        PageGenerator::createFile("protected/views/web/error.php", PageGenerator::staticData('errorview'));
        PageGenerator::createFile("protected/views/web/index.php", PageGenerator::staticData('indexview'));
        PageGenerator::createFile("protected/.htaccess", PageGenerator::staticData('htaccess'));
        PageGenerator::createFile("index.php", PageGenerator::staticData('index'));
        PageGenerator::createFile("js/scripts.js", PageGenerator::staticData('js'));
        PageGenerator::createFile("js/scripts.min.js", PageGenerator::staticData('js-min'));
        PageGenerator::createFile("css/main.css", PageGenerator::staticData('css'));
        PageGenerator::createFile("css/main.min.css", PageGenerator::staticData('css-min'));
    }
    
    /**
     * Copy 3rd party libraries to a new website
     */
    private function copyVendorLibraries(){
        shell_exec("cp -r " . $this->frameworkDir . "/vendor/* lib/");
    }
    
    /**
     * Remove directories
     */
    private function removeDirs(){
        /* Just to be save - we will not iterate through dirs */
        $this->rrmdir("css");
        $this->rrmdir("images");
        $this->rrmdir("js");
        $this->rrmdir("lib");
        $this->rrmdir("protected");
    }
    
    /**
     * Remove files
     */
    private function removeFiles(){
        unlink("index.php");
    }
    
    /**
     * List directory
     */
    private function listDirectory(){
        echo "\nListing this directory:\n";
        echo shell_exec("ls -l");
    }
    
    
}


