# smc-wp-downoald

<h2>Name: smcstylus WP Downloader v.1.0.0</h2>

<b>Description:</b> Simple script for WordPress to download files ('png', 'gif', 'tiff', 'jpeg', 'jpg','bmp','svg','pdf','zip','ppt', 'doc') based on the ID stored in the DB.

<b>HOW TO:</b> 
- In your pages: <b>&lt;a href="yourdomain.com/smc-dl.php?_u=ID_GOES_HERE" target="_blank" rel="noindex, nofollow" title="Download">Download&lt;/a></b>
- On server: copy this file on your WordPress root and edit $domainList and $fileList:

<pre>// Allowed domains to do the request - edit the list of allowed domains from where the request can be done
$domainList = array('yourdomain.com', 'allowed3rdpartydomain.com', 'localhost');

// Supported files for download - edit the list of allowed files that can be downloaded
$fileList = array('png', 'gif', 'tiff', 'jpeg', 'jpg','bmp','svg','pdf','zip','ppt', 'doc');

// Just for debug. Please set to false in production.
if ( !defined('SMC__DEBUG_LOCAL') )
	define('SMC__DEBUG_LOCAL',  false);
 </pre>
 
Note: You can rename this file how do you like but remember to change in link too: from "<b>yourdomain.com/smc-dl.php?_u=ID_GOES_HERE</b>" to "<b>yourdomain.com/new-file-name.php?_u=ID_GOES_HERE</b>"

| Author: Mihai Calin SIMION 
 | Website: https://www.smcstylus.com/
 | Date: February 2020 |
