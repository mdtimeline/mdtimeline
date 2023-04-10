<?PHP

ini_set('memory_limit', '1024M');
ini_set('max_execution_time', 5);

include_once ('../session.php');

$file = $_REQUEST['file'];

if(preg_match('/php$/', $file)){
    header("HTTP/1.1 403 Forbidden");
    exit;
}

if(!file_exists($file) || !is_readable($file)){
	header("HTTP/1.1 403 Forbidden");
	exit;
}

preg_match('/^[a-z]*/', $file, $matches);

if(isset($matches[0])){
	define('SITE',$matches[0]);
}else{
	header("HTTP/1.1 403 Forbidden");
	exit;
}

define('_GaiaEXEC', 1);
include_once ('../dataProvider/Site.php');
Site::setAllowSiteSwitch(true);
require_once(str_replace('\\', '/', dirname(dirname(__FILE__))) . '/registry.php');
$URL = URL;

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
	strpos($file, 'logo-voucher.png') ||
	strpos($file, 'portal_logo.png')
){
	$mine = mime_content_type($file);
	header("Content-type: $mine");
	print file_get_contents($file);
}else{
	header("HTTP/1.1 403 Forbidden");
}