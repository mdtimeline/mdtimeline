<?php

include_once ('../session.php');

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

header('Content-Type: application/zip');
header('Content-Transfer-Encoding: Binary');

if(isset($_POST['disclosure'])){
	if($authorized){
		$params = json_decode($_POST['disclosure']);
		$response = $Disclosure->downloadDisclosureDocuments($params);

		if(isset($response->success) && $response->success === false && isset($response->errorMsg)){
			print 'Error: ' . $response->errorMsg;
		}
	}
}