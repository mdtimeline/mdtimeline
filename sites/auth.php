<?PHP

ini_set('memory_limit', '1024M');
ini_set('max_execution_time', 5);

session_cache_limiter('private');
session_name('mdTimeLine');
session_start();

define('_GaiaEXEC', 1);
require_once(str_replace('\\', '/', dirname(dirname(__FILE__))) . '/registry.php');
include_once (ROOT . '/dataProvider/Site.php');
Site::setAllowSiteSwitch(true);
$URL = URL;
$file = $_REQUEST['file'];

if(!file_exists($file) || !is_readable($file)){
	header("HTTP/1.1 403 Forbidden");
	exit;
}

if (
	(
		isset($_SESSION['user']) &&
		(
			(isset($_SESSION['user']['auth']) && $_SESSION['user']['auth'] == true) ||
			(isset($_SESSION['user']['portal_authorized']) && $_SESSION['user']['portal_authorized'] == true)
		)
	) ||
	strpos($file, 'logo-dark.png') ||
	strpos($file, 'logo-email.png') ||
	strpos($file, 'logo-light.png') ||
	strpos($file, 'portal_logo.png')
){
	$mine = mime_content_type($file);
	header("Content-type: $mine");
	print file_get_contents($file);
}else{
	header("HTTP/1.1 403 Forbidden");
}