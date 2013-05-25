<?php
/**
 * PageGenerator
 *
 * @author dainius
 */

class PageGenerator {

    public static function createFile($fileName, $str){
        if(!file_exists($fileName)) {
            if(touch($fileName)) {
                $fh = fopen($fileName, 'w');
                fwrite($fh, $str);
                fclose($fh);
            }
            else{
                echo "CANNOT CREATE FILE!";
            }
        }
    }
    
public static function dynamicData($name, $type){
    
    $controller = <<<EOF
<?php
/**
 * $name
 * 
 * @method __construct
 * @method actionIndex
 * @method showError
 * 
 */

class $name extends MainController {

    /**************************************************************************
     * Constructor
     **************************************************************************/
    public function __construct(\$id, \$module = null) {
        parent::__construct(\$id);
    }

    /***************************************************************************
     * Index action - this is the front page
     ***************************************************************************/
    public function actionIndex() {
        \$this->render('index', array());
    }

    /***************************************************************************
     * Show custom error message
     * @param type \$error
     ***************************************************************************/
    public function showError(\$error = ""){
        if(!\$error){
            \$this->redirect('index.php?r=web/error', array());    
        }
        \$this->redirect('index.php?r=web/error&error='.\$error, array());
    }
}
EOF;
    
    $model = <<<EOF
<?php
/**
 * $name model
 * 
 */

class $name extends MainModel {

    public \$id;
    
    public \$requiredFields = array();

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Required fields
     * @return an array of required fields
     */
    public function required() {
        return array(

        );
    }

    /**
     * Validates form inputs
     * @return array - errors array
     */
    public function validate() {
        \$r = \$this->validationRegex();
        \$errors = array();
        foreach (\$this->required() as \$k => \$v) {

        }
        return \$errors;
    }

    /**
     * Validation function - checks if all required fields were passed
     * @return true or false
     */
    public function allPassed() {
        \$localArray = \$this->required();
        \$value = true;
        foreach (\$localArray as \$k => \$v) {
            if (!\$v) {
                \$value = false;
                array_push(\$this->requiredFields, \$k);
            }
        }
        return \$value;
    }

    /**
     * Pass some parameters when saving a new data
     */
    public function savingNew() {

    }

    /**
     * Get required fields array
     * @return requiredFields array
     */
    public function getRequiredFields() {
        return \$this->requiredFields;
    }

    /**
     * Get an array of values corespondint to the database columns
     * We not inserting into id as that suppose to be AUTO_INCREMENT
     * @return array of values
     */
    public function getValuesArray() {
        return array(

        );
    }

    /**
     * Load the model
     * @param type \$id
     * @return Model|null
     */
    public function loadModel(\$id) {

    }
}
EOF;
    
    $view = <<<EOF
<?php \$this->setPageTitle("smallMVC - index view"); ?>

<h1>Index view</h1>
EOF;
    
    switch ($type) {
        case 'controller':
            return $controller;
            break;
        case 'model':
            return $model;
            break;
        case 'view':
            return $view;
            break;
        default:
            return "";
    }
}
    
/**
 * This is all generated data file
 * @param string $file
 * @return string data
 */
public static function staticData($file){
    $protected = dirname(dirname(__FILE__))."/Main.php";
    
    $constants = <<<EOF
<?php
/**
 * Constants Class defined all the Constant value of the website.
 * 
 */

abstract class Constants {	

	const ROUTE_VAR           = 'r';
	const DEFAULT_CONTROLLER  = 'web';
	const TBL_PREFIX          = 'tbl_';
	const SALT                = '$$$$$$'; 
    const HASH                = 'md5';
    const USER_TABLE          = 'tbl_user';
}
EOF;
    
    $config = <<<EOF
<?php
/**
 * Configuration file
 */
return array(

	/* Database connection */
	'dsn' 			 => 'mysql:host=localhost;dbname=database_name',
	'emulatePrepare' => true,
	'username'       => 'user_name',
	'password' 		 => 'password',
	'charset' 		 => 'utf8',	
	'prefix'         => 'tbl_',
);
EOF;
    
    $webcontroller = <<<EOF
<?php
/**
 * WebController
 * 
 * @method __construct
 * @method actionError
 * @method actionIndex
 * @method loadTestById
 * @method showError
 * 
 */

class WebController extends MainController {

    /**************************************************************************
     * Constructor
     **************************************************************************/
    public function __construct(\$id, \$module = null) {
        parent::__construct(\$id);
    }

    /**************************************************************************
     * Deafult page error. 
     * In case the page is not found this action will be displayed
     **************************************************************************/
    public function actionError(\$error = "") {
        if(!\$error){
            \$error = 'Error: page cannot be found.';
        }
        \$this->render('error', array('error' => \$error));
    }

    /***************************************************************************
     * Index action - this is the front page
     ***************************************************************************/
    public function actionIndex() {
        \$this->render('index', array());
    }
    
    /***************************************************************************
     * Index action - this is the front page
     ***************************************************************************/
    public function actionAbout() {
        \$this->render('about', array());
    }
    
    /***************************************************************************
     * Index action - this is the front page
     ***************************************************************************/
    public function actionContact() {
        \$this->render('contact', array());
    }

    /***************************************************************************
     * Loads test model based on id
     ***************************************************************************/
    public function loadTestById(\$id) {
        \$test = new Test;
        return \$test->loadModel(\$id);
    }

    /***************************************************************************
     * Show custom error message
     * @param type \$error
     ***************************************************************************/
    public function showError(\$error = ""){
        if(!\$error){
            \$this->redirect('index.php?r=web/error', array());    
        }
        \$this->redirect('index.php?r=web/error&error='.\$error, array());
    }

}
EOF;
    
    $database = <<<EOF
DROP TABLE IF EXISTS tbl_user;

--
-- Table structure for table `tbl_users`
--
CREATE TABLE `tbl_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(20) NOT NULL,
  `password` varchar(128) NOT NULL,
  `email` varchar(128) NOT NULL,
  `permissions` int(1) NOT NULL DEFAULT '0',
  `status` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `tbl_users`
--
INSERT INTO `tbl_user` (`id`, `username`, `password`, `email`, `permissions`, `status`) VALUES
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'admin@smallMVC.com', 1, 1);
EOF;
    
    $testmodel = <<<EOF
<?php
/**
 * Test model
 * 
 */

class Test extends MainModel {

    public \$id;
    public \$title;
    public \$post;
    public \$author;
    
    public \$requiredFields = array();

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Required fields
     * @return an array of required fields
     */
    public function required() {
        return array(
            'title' => \$this->title,
            'post' => \$this->post,
            'author' => \$this->author,
        );
    }

    /**
     * Validates form inputs
     * @return array - errors array
     */
    public function validate() {
        \$r = \$this->validationRegex();
        \$errors = array();
        foreach (\$this->required() as \$k => \$v) {
            if (\$k == 'title') {
                if (!\$this->title) {
                    array_push(\$errors, "Title is required");
                }
            }
            if (\$k == 'post') {
                if (!\$this->post) {
                    array_push(\$errors, "Post is required");
                }
            }
            if (\$k == 'author') {
                if (!\$this->location) {
                    array_push(\$errors, "author");
                }
            }
        }
        return \$errors;
    }

    /**
     * Validation function - checks if all required fields were passed
     * @return true or false
     */
    public function allPassed() {
        \$localArray = \$this->required();
        \$value = true;
        foreach (\$localArray as \$k => \$v) {
            if (!\$v) {
                \$value = false;
                array_push(\$this->requiredFields, \$k);
            }
        }
        return \$value;
    }

    /**
     * Pass some parameters when saving a new data
     */
    public function savingNew() {

    }

    /**
     * Get required fields array
     * @return requiredFields array
     */
    public function getRequiredFields() {
        return \$this->requiredFields;
    }

    /**
     * Get an array of values corespondint to the database columns
     * We not inserting into id as that suppose to be AUTO_INCREMENT
     * @return array of values
     */
    public function getValuesArray() {
        return array(
            \$this->id,
            \$this->title,
            \$this->post,
            \$this->author,
        );
    }

    /**
     * Load the model
     * @param type \$id
     * @return Incident|null
     */
    public function loadModel(\$id) {
        \$postsArray = \$this->loadModelData("id", \$id);
        if (\$postsArray) {
            \$this->id = \$postsArray['id'];
            \$this->title = \$postsArray['title'];
            \$this->description = \$postsArray['post'];
            \$this->date = \$postsArray['author'];
            return \$this;
        } else {
            return null;
        }
    }
}
EOF;
    
    $mainview = <<<EOF
<?php \$public = Main::getApplication()->getPublicUrl(); ?>
<!DOCTYPE html>
<html class="no-js" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php if(DEBUG==1): ?>
    <title>Website in Debug Mode</title>
    <link rel="stylesheet" type="text/css" href="<?php echo \$public; ?>/lib/bootstrap/css/bootstrap.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo \$public; ?>/css/main.css" />
    <?php else: ?>
    <title><?php echo Helpers::encode(\$this->getPageTitle()); ?></title>
    <link rel="stylesheet" type="text/css" href="<?php echo \$public; ?>/lib/bootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo \$public; ?>/css/main.min.css" />
	<?php endif; ?>
</head>
<body>
<div id="wrapper">
	<div id="header">
		<div id="logo">
            <a href="index.php">
                <span class="green">small</span><span class="blue">MVC</span>
            </a>
		</div>
        <div id="menu">
            <div id="menu-in">
                <ul>
                    <li><a href="index.php" class="menu-first">Home</a></li>
                    <li><a href="index.php?r=web/about">About</a></li>
                    <li><a href="index.php?r=web/contact">Contact</a></li>
                </ul>
            </div>
        </div>
	</div>
	<div class="clear"></div>
	<div id="content">
		<div id="content-in">
			<?php echo \$content; ?>
		</div>
	</div>
    <div class="clear"></div>
	<div id="footer" class="mobile-hidden">
		<div id="powered">
            &copy; <?php echo date('Y'); ?> 
			smallMVC. All rights reserved.
		</div>
	</div>
</div>
<?php if(DEBUG==1): ?>
<script src="<?php echo \$public; ?>/lib/jquery/jquery.js"></script>
<script src="<?php echo \$public; ?>/lib/bootstrap/js/bootstrap.js"></script>
<script src="<?php echo \$public; ?>/lib/bootstrap/js/bootstrap.file-input.js"></script>
<script src="<?php echo \$public; ?>/js/scripts.js"></script>
<?php else: ?>
<script src="<?php echo \$public; ?>/lib/jquery/jquery.min.js"></script>
<script src="<?php echo \$public; ?>/lib/bootstrap/js/bootstrap.min.js"></script>
<script src="<?php echo \$public; ?>/lib/bootstrap/js/bootstrap.file-input.min.js"></script>
<script src="<?php echo \$public; ?>/js/scripts.min.js"></script>
<?php endif; ?>
</body>
</html>
EOF;
    
    $aboutview = <<<EOF
<?php \$this->setPageTitle("smallMVC - about view"); ?>

<h1>About view</h1>
EOF;
    
    $contactview = <<<EOF
<?php \$this->setPageTitle("smallMVC - contact view"); ?>

<h1>Contact view</h1>
EOF;
    
    $errorview = <<<EOF
<?php \$this->setPageTitle("smallMVC - error view"); ?>

<h1>Error page</h1>
EOF;
    
    $indexview = <<<EOF
<?php \$this->setPageTitle("smallMVC - index view"); ?>

<h1>Index view</h1>
EOF;
    
    $htaccess = <<<EOF
deny from all
EOF;
    
    $index = <<<EOF
<?php
/**
 * This index file initializes the application
 * @author Dainius 
 */

\$main   = dirname(__FILE__).'/framework/Main.php';
// Remove this line if framework dir is in the same directory as protected
\$main   = "$protected";
\$config = dirname(__FILE__).'/protected/config/config.php';

defined('DEBUG') or define('DEBUG',true);
defined('TRACE') or define('TRACE',false);

require_once(\$main);
Main::createApplication(\$config);
EOF;
    
    $css = <<<EOF

/*******************************************************************************
 * INDEX
 * 
 * 1. Default styles
 * 2. Debugging styles
 * 3. Main styles
 * 4. Header styles
 * 5. Top menu styles
 * 6. Main Body styles
 *
 * @author Dainius
 *
 *
 ******************************************************************************/

/*******************************************************************************
 * 1. Default styles
 ******************************************************************************/
body{
    color: #555555;
}
:focus, :active {
    outline: none;
}
strong{
    font-weight: bold;
}
.hidden {
    display: none;
}
a{
    color: #555555;
}
a:hover {
    color: #666666;
}
.clear{
    clear: both;
}
.red{
    color: #FF0000;
}
.blue{
    color: #377AD0;
}
.green{
    color: #5EAA36;
}
a,a:visited,
a:hover,
a:focus {
    text-decoration: none;
}

/*******************************************************************************
 * 2. Debugging styles
 ******************************************************************************/
.trace {
    display: block;
    clear: both;
    margin: 3px 0;
    padding: 5px;
    border: solid 1px #cccccc;
    background: #efefef;
}

/*******************************************************************************
 * 3. Main styles
 ******************************************************************************/
#wrapper, #header, #content, #content-in, #footer, #powered {
    display: block;
    margin: 0;
    padding: 0;
}
#wrapper, #header, #content, #content-in, #footer, #powered {
    overflow: hidden;
}
#wrapper {
    width: 960px;
    margin: 0 auto;
}
#header {
    position: relative;
    height: 100px;
    width: 960px;
    margin: 0 auto;
}
#content {
    width: 956px;
    min-height: 450px;
    border-top: solid 1px #efefef;
    border-left: solid 1px #efefef;
    border-right: solid 1px #efefef;
    border-bottom: solid 1px #efefef;
    margin: 0 auto;
}
#footer {
    height: 50px;
    margin: 0 auto;
    font-size: 11px;
    padding: 5px 10px;
    color: #aaaaaa;
}

/*******************************************************************************
 * 4. Header styles
 ******************************************************************************/
#logo {
    position: absolute;
    bottom: 8px;
    left: 20px;
    width: 195px;
    height: 35px;
    cursor: pointer;
    font-family: helvetica, arial, sans-serif;
    font-stretch: ultra-expanded;
    text-align: left;
    font-size: 30px;
}
#logo a:hover {
    text-decoration: none;  
}

/*******************************************************************************
 * 5. Top menu styles
 ******************************************************************************/
#menu {
    position: absolute;
    width: 500px;
    height: 25px;
    bottom: 8px;
    left: 220px;
    text-transform: uppercase;
    font-family: helvetica, arial, sans-serif;
    font-size: 12px;
    font-weight: bold;
}
#menu-in {
    position: absolute;
    width: 100%;
    top: 0;
    left: 0;
    height: 30px;
}
#menu ul {
    margin: 0 0 10px 0;
}
#menu ul, #menu li {
    list-style: none;
}
#menu li {
    float: left;
}
#menu a:hover {
    color: #666666;
}
#menu ul li a {
    float: left;
    display: block;
    font-size: 13px;
    line-height: 13px;
    font-weight: normal;
    text-align: center;
    padding: 0 25px;
    border-left: 1px solid #cccccc;
    border-right: 0px solid #cccccc;
}
#menu ul li a.menu-first {
    border-left: 0;
}
#menu ul li a:hover {
    text-decoration: none;
}

/*******************************************************************************
 * 6. Main Body styles
 ******************************************************************************/
#content {

}
#content-in {
    padding: 10px 20px 40px 20px;	
}
EOF;
    
    switch ($file) {
        case 'constants':
            return $constants;
            break;
        case 'config':
            return $config;
            break;
        case 'webcontroller':
            return $webcontroller;
            break;
        case 'database':
            return $database;
            break;
        case 'testmodel':
            return $testmodel;
            break;
        case 'mainview':
            return $mainview;
            break;
        case 'aboutview':
            return $aboutview;
            break;
        case 'contactview':
            return $contactview;
            break;
        case 'errorview':
            return $errorview;
            break;
        case 'indexview':
            return $indexview;
            break;
        case 'htaccess':
            return $htaccess;
            break;
        case 'index':
            return $index;
            break;
        case 'css':
            return $css;
            break;
        default:
            return "";
    }
    
}

}

