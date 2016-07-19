<?php

/**
 * Created by IntelliJ IDEA.
 * User: ernesto
 * Date: 7/9/16
 * Time: 12:26 PM
 */
class Site {

	public static $allowSiteSwitch = false;

	/**
	 * @return string
	 */
	public static function GetSite() {
		$site = 'default';
		if(isset($_SESSION['user']) && isset($_SESSION['user']['site'])){
			$site = $_SESSION['user']['site'];
		} elseif(isset($_REQUEST['site'])) {
			$site = $_REQUEST['site'];
		}
		return $site;
	}

	/**
	 * @return bool|string
	 */
	public static function GetUserSite() {
		$site = false;
		if(isset($_SESSION['user']) && isset($_SESSION['user']['site'])){
			$site = $_SESSION['user']['site'];
		}
		return $site;
	}

	public static function GetRequestSite() {
		$site = 'default';
		if(isset($_REQUEST['site'])){
			$site = $_REQUEST['site'];
		}
		return $site;
	}

	/**
	 * @return bool
	 */
	public static function DoUserSiteValidation() {
		$userSite = self::GetUserSite();
		$requestSite = self::GetRequestSite();
		$authorized = isset($_SESSION['user']) && (
				(isset($_SESSION['user']['auth']) && $_SESSION['user']['auth'] == true) ||
				(isset($_SESSION['user']['portal_authorized']) && $_SESSION['user']['portal_authorized'] == true)
			);

		if($authorized){
			if(self::getAllowSiteSwitch()){
				return true;
			}
			if($userSite !== false && $requestSite != $userSite){

				error_log('******************************************************');
				error_log('************** DoUserSiteValidation ******************');
				error_log('******************************************************');
				error_log('$userSite = ' . $userSite);
				error_log('$requestSite = ' . $requestSite);
				error_log('------------------------------------------------------');
				if(isset($_SERVER)) error_log('$_SERVER = ' . print_r($_SERVER, true));
				error_log('------------------------------------------------------');
				if(isset($_REQUEST)) error_log('$_REQUEST = ' . print_r($_REQUEST, true));
				error_log('------------------------------------------------------');
				if(isset($_SESSION)) error_log('$_SESSION = ' . print_r($_SESSION, true));
				error_log('******************************************************');
				error_log('******************************************************');
				error_log('******************************************************');

				//$_SESSION['user'] = null;
				return false;
			}
			return true;
		}

		return true;
	}

	public static function setAllowSiteSwitch($allowSiteSwitch){
		self::$allowSiteSwitch = $allowSiteSwitch;
	}

	public static function getAllowSiteSwitch(){
		return self::$allowSiteSwitch;
	}
}