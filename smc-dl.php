<?php
/*
smcstylus WP Downloader v.1.0.0

Description: Simple script to download files ('png', 'gif', 'tiff', 'jpeg', 'jpg','bmp','svg','pdf','zip','ppt', 'doc').

HOW TO: 
- In front-end: <a href="yourdomain.com/smc-dl.php?_u=ID_GOES_HERE" target="_blank" rel="noindex, nofollow" title="Download">Download</a>
- In back-end: copy this file on your root WordPress. Edit $domainList and $fileList.
Note: You can rename this file how do you like but remember to change in link too: from "yourdomain.com/smc-dl.php?_u=ID_GOES_HERE" to "yourdomain.com/new_file_name.php?_u=ID_GOES_HERE"

Author: Mihai Calin SIMION 
Website: https://www.smcstylus.com/
February 2020
*/


/****************** EDIT next lines ******************/
// Allowed domains to do the request - edit the list of allowed domains from where the request can be done
$domainList = array('yourdomain.com', 'allowed3rdpartydomain.com', 'localhost');

// Supported files for download - edit the list of allowed files that can be downloaded
$fileList = array('png', 'gif', 'tiff', 'jpeg', 'jpg','bmp','svg','pdf','zip','ppt', 'doc');

// Just for debug. Please set to false in production.
if ( !defined('SMC__DEBUG_LOCAL') )
	define('SMC__DEBUG_LOCAL',  false);
	
/****************** DO NOT EDIT ******************/
// Check if the request is coming from  allowed domains
// Apply this check only if at least 1 domain is defined

// Variant of die() 
if(!function_exists('smc__die')){
	function smc__die($out=''){
		if(SMC__DEBUG_LOCAL)
			 die($out); 	
		die();
	}
}
// Variant of var_dump
if(!function_exists('smc__debug')){
	function smc__debug($out='',$die=true){
		var_dump($out);
		if($die === true)
			 die(); 
	}
}
// Check for refferer
if(!isset($_SERVER['HTTP_REFERER'])) smc__die('No refferer !');
// Check for allowed domains who can request the file
if(count($domainList) > 0){
	$ref = $_SERVER['HTTP_REFERER'];
	$refData = parse_url($ref);
	if(!in_array(str_replace('www.','',$refData['host']), $domainList)) {
		die("Hotlinking not permitted!"); // Stop execution - "Hotlinking not permitted!"
	}
}

// Load WP core in fastest way
/** Sets up WordPress vars and included files. */
if ( !defined('ABSPATH') )
  define('ABSPATH', dirname(__FILE__) . '/');
if ( !defined('SHORTINIT') )
	define('SHORTINIT', true);
require_once('wp-load.php');
// Load some of WordPress.
require( ABSPATH . WPINC . '/class-wp-query.php' );
require( ABSPATH . WPINC . '/query.php' );
require( ABSPATH . WPINC . '/post.php' );

// Get the request
$req = NULL;
// $_POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['_u'])) {
  $req = $_POST['_u'];
// $_GET
}elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['_u'])){
	$req = $_GET['_u'];
// STOP
}else{
	smc__die('Invalid request !'); // Stop execution - "Invalid request !"
}

$req = stripslashes(trim($req));
// $req must be set and not empty
if(isset($req) && $req != ''){
	// Check if ID is a number inside the string and convert to integer
	$req = (ctype_digit((string)$req))? intval($req) : smc__die('ID must be an integer!');
	
	// ID must be an integer and < 2
	if($req < 2){
		smc__die('Invalid ID !'); // Stop execution - "Invalid ID"
	}
	// Get file location on server
	$file = get_attached_file( $req );
	// Get file info:
	$file_info = pathinfo($file);
	// - extension
	$fileExt = $file_info['extension'];
	// - name
	$fileNameNoExt = $file_info['filename'];
	// Simple security check - allow only some types of files to be downloaded
	$fileNameExt = strtolower($file);
	if(!in_array(end(explode('.', $fileNameExt)), $fileList)){
		smc__die('Invalid file !'); // Stop execution - "Invalid file!"
	}
	// Double check for *.php
	if(strpos( $file , '.php' ) == true){
		smc__die('Invalid file !'); // Stop execution - "Invalid file!"
	}
	
	// Check if file exist on server
	if(file_exists( $file  ) === false){
		smc__die('File not found !'); // Stop execution - "File not found!"
	}
	// Rename served file
	$fileNewName = $fileNameNoExt.'-'.time().".".$fileExt;
	// New name if set, must be alphanumeric
	if(isset($newName) && !empty($newName) && preg_match('/^[a-zA-Z0-9]+$/', $newName))  {
		$fileNewName = $newName.".".$fileExt;
	}	
	// Set content type in Headers based on file extension
	switch( $fileExt){
		case "jpeg"	:
        case "jpg"  : $ctype="image/jpg"; break;
        case "png"  : $ctype="image/png"; break;
        case "tiff" : $ctype="image/tiff"; break;
        case "gif"  : $ctype="image/gif"; break;
        case "pdf"  : $ctype="application/pdf"; break;
        case "zip"  : $ctype="application/zip"; break;
        case "doc"  : $ctype="application/msword"; break;
        case "ppt"  : $ctype="application/vnd.ms-powerpoint"; break;
        //case "xls"  : $ctype="application/vnd.ms-excel"; break;
        default     : $ctype="application/force-download";
    }
	
	// Set headers and serve the file
	smc__nocache_headers();
	header("Content-type: {$ctype}");
	header("Content-Disposition:attachment; filename={$fileNewName}");
	header('Content-Type: application/force-download');
	header('Content-Transfer-Encoding: binary');
	header("Content-Length: ".filesize($file));

	ob_clean();
    flush();
    readfile($file);
	die(); // Stop execution
}else{
	smc__die('Invalid request !'); // Stop execution - "Invalid request !"
}

// Nocache headers function
function smc__nocache_headers(){
	if ( headers_sent() ) {
        return;
    }
	header_remove( 'Last-Modified' );
	header('Expires: Wed, 11 Jan 1984 05:00:00 GMT');
	header('Cache-Control: no-cache, no-store, max-age=0'); 
	header('Cache-Control: must-revalidate,  pre-check=0, post-check=0', false); 
	header('Pragma: no-cache');	
}