<?php

if(!isset($_SESSION)){
	session_cache_limiter('private');
	//session_cache_expire(1);
	session_name('mdTimeLine');
	session_start();
//	if(session_status() == PHP_SESSION_ACTIVE) session_regenerate_id(false);
//	setcookie(session_name(),session_id(),time()+86400, '/', "mdapp.com", false, true);
}

/**
 * Created by IntelliJ IDEA.
 * User: ernesto
 * Date: 4/29/16
 * Time: 3:40 PM
 */


if(!defined('_GaiaEXEC'))
	define('_GaiaEXEC', 1);

$authorized = isset($_SESSION['user']) && (
	(isset($_SESSION['user']['auth']) && $_SESSION['user']['auth'] == true)
);

if(!$authorized) die();
if(!isset($_SESSION['user']['site'])) die();
$site = $_SESSION['user']['site'];
require_once('../registry.php');
require_once(ROOT . '/sites/'.$site.'/conf.php');
require_once(ROOT . '/dataProvider/Disclosure.php');
new \MatchaHelper();
$Disclosure = new \Disclosure();

if(isset($_POST['disclosure'])){
	if($authorized){
		$params = json_decode($_POST['disclosure']);
		$Disclosure->downloadDisclosureDocuments($params);
	}
}