<?php
/**
 * Helpers class - collection of helpful static methods
 * 
 * @method traceCount
 * @method print_rr
 * @method mergeArray
 * @method encode
 * @method decode
 * @method clean
 * @method trace
 * @method trim_text
 * @method userIP
 * @method curlPageURL
 * @method curDomainURL
 * @method webURL
 * @method sendEmail
 * @method htmlEmail
 *
 * @author Dainius
 * @since 0.1
 */

class Helpers {

	/** 
	 * Count trace lines
	 * @return count
	 */
	public static function traceCount() {
    	static $count = 0;
    	$count++;
    	return $count;
    }	
	/** 
    * Display readable array/object - good for debugging
    * @param array or object
    * @return readable array/object
    */
	public static function print_rr($arr){
		echo "<hr/>";
		echo "<pre>".print_r($arr,true)."</pre>";
		echo "<hr/>";
	}
	
	/**
	 * Merge 2,3,... arrays into one
	 * @param array $a array to be merged to
	 * @param array $b array to be merged from
	 * @return array the merged array
	 */
	public static function mergeArray($a,$b){
		$args = func_get_args();
		$res  = array_shift($args);
		while(!empty($args)){
			$next = array_shift($args);
			foreach($next as $k => $v){
				if(is_integer($k)){
					isset($res[$k]) ? $res[]=$v : $res[$k]=$v;
				}
				else if(is_array($v) && isset($res[$k]) && is_array($res[$k])){
					$res[$k]=self::mergeArray($res[$k],$v);
				}
				else{
					$res[$k]=$v;
				}
			}
		}
		return $res;
	}
	
	/**
	 * Encodes to HTML entities
	 * @return encoded string
	 */
	public static function encode($text){
		return htmlspecialchars($text,ENT_QUOTES);
	}

	/**
	 * Decodes HTML entities 
	 * @return decoded string
	 */
	public static function decode($text){
		return htmlspecialchars_decode($text,ENT_QUOTES);
	}
	
	/**
	 * Get view path - protected/view directory
	 * @return string the path of view directory
	 */
	public static function clean($value){	
		return htmlspecialchars(htmlentities(stripslashes(trim($value))));
	}
	
	/**
	 * Trace the code - outputs classes and methods in the order they are processed in nicelly rendered output
	 * @param $class - the name of the class
	 * @param $method - the name of the method
	 * @param $argument - optional argument an array of 2 values: name of the argument and it's value
	 */
	public static function trace($class, $method, $argument=null){
		if(defined('TRACE')){
			if(TRACE){
				$counter = Helpers::traceCount();
				if($argument){
					echo "<div class='trace'><strong>Running " . $counter . ":</strong> " . $class . " ---> " . $method . "()";
					echo ". Arguments ---> " . $argument[0] . ": " . $argument[1] . "</div>";
				}
				else{
					echo "<div class='trace'><strong>Running " . $counter . ":</strong> " . $class . " ---> " . $method . "()</div>";	
				}
					
			}
		}
	}
    
    /**
     * trims text to a space then adds ellipses if desired
     * @param string $input text to trim
     * @param int $length in characters to trim to
     * @param bool $ellipses if ellipses (...) are to be added
     * @param bool $strip_html if html tags are to be stripped
     * @return string
     */
    public static function trim_text($input, $length, $ellipses = true, $strip_html = true) {
       //strip tags, if desired
       if ($strip_html) {
           $input = strip_tags($input);
       }

       //no need to trim, already shorter than trim length
       if (strlen($input) <= $length) {
           return $input;
       }

       //find last space within length
       $last_space = strrpos(substr($input, 0, $length), ' ');
       $trimmed_text = substr($input, 0, $last_space);

       //add ellipses (...)
       if ($ellipses) {
           $trimmed_text .= '...';
       }

       return $trimmed_text;
    }
	
    /**
     * Get user's IP address
     * @return String user ip address
     */
	public static function userIP(){
		return	$_SERVER['REMOTE_ADDR'];
	}
    
    /**
     * Get current page URL
     * @return string page url
     */
    public static function curPageURL() {
       $pageURL = 'http';

       if ( isset( $_SERVER["HTTPS"] ) && strtolower( $_SERVER["HTTPS"] ) == "on" ) {
           $pageURL .= "s";
       }

       $pageURL .= "://";

       if ($_SERVER["SERVER_PORT"] != "80") {
           $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
       } 
       else {
           $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
       }

       return $pageURL;
    }

    /**
     * Get current domain URL
     * @return string page url
     */
    public static function curDomainURL() {
       $pageURL = 'http';

       if ( isset( $_SERVER["HTTPS"] ) && strtolower( $_SERVER["HTTPS"] ) == "on" ) {
           $pageURL .= "s";
       }

       $pageURL .= "://";

       if ($_SERVER["SERVER_PORT"] != "80") {
           $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
       } 
       else {
           $pageURL .= $_SERVER["SERVER_NAME"];
       }

       return $pageURL;
    }
    
    /**
     * Get web url
     * @return string web url
     */
    public static function webURL() {
       $pageURL = 'http';

       if ( isset( $_SERVER["HTTPS"] ) && strtolower( $_SERVER["HTTPS"] ) == "on" ) {
           $pageURL .= "s";
       }

       $pageURL .= "://";

       if ($_SERVER["SERVER_PORT"] != "80") {
           $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
       } 
       else {
           $pageURL .= $_SERVER["SERVER_NAME"];
       }
       
       $pageURL = $pageURL . $_SERVER["SCRIPT_NAME"];

       return $pageURL;
    }
    
    
    /**
     * sendEmail
     */
    public static function sendEmail($to,$subject,$body,$type='plain',$fromname=Constants::FROM_NAME){

        ini_set("sendmail_from",Constants::FROM_ADDRESS);

        $name=$fromname;
        if($type=='plain'){
            $headersTEXT="From: $fromname <".Constants::FROM_ADDRESS.">\n".
                "Reply-To: ".Constants::REPLY_ADDRESS."\n".
                "MIME-Version: 1.0\n".
                "Content-type: text/plain; charset=\"UTF-8\"";
        }
        elseif($type=='html'){
            $headersTEXT="From: $fromname <".Constants::FROM_ADDRESS.">\n".
                "Reply-To: ".Constants::REPLY_ADDRESS."\n".
                "MIME-Version: 1.0\n".
                "Content-type: text/html; charset=\"UTF-8\"";
        }

        if($type=='plain'||$type=='html'){
            mail($to,$subject,$body,$headersTEXT);	
        }
        elseif($type=='none'){
            mail($email,$subject,$body);
        }
    }

    /**
     * htmlEmail
     * @param array of parameters passed to the email
     * @return email body
     */
    public static function htmlEmail($info = array()){
        //$filePath,$link="",$title="",$text="",$linkText="Click here to post an incident"
        if(!$info){
            return null;
        }
        $filePath         = $info['filePath'];
        $link             = $info['link'];
        $update_link      = $info['update_link'];
        $title            = $info['title'];
        $text             = $info['text'];
        $linkText         = $info['link_text'];
        $update_link_text = $info['update_link_text'];
        $update_text      = $info['update_text'];
        $update_link_text = $info['update_link_text'];
        
        
        $doc = new DOMDocument();
        $doc->loadHTMLFile($filePath);
        
        $aTag = $doc->getElementById("emailLink");
        $aTag->setAttribute("href", $link);
        $linkTextTag = $doc->getElementById("linkText");
        $linkTextTag->appendChild($doc->createTextNode($linkText));
        $textTag = $doc->getElementById("emailTitle");
        $textTag->appendChild($doc->createTextNode($title));
        $textTag = $doc->getElementById("emailText");
        $textTag->appendChild($doc->createTextNode($text));
        
        $aTag = $doc->getElementById("emailUpdateLink");
        $aTag->setAttribute("href", $update_link);
        $linkTextTag = $doc->getElementById("updateLinkText");
        $linkTextTag->appendChild($doc->createTextNode($update_link_text));
        $textTag = $doc->getElementById("emailUpdateText");
        $textTag->appendChild($doc->createTextNode($update_text));
        
        return $doc->saveHTML();
    }
		
}

