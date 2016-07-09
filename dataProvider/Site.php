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
	public static function GetSite(){
		$site = 'default';
		if(isset($_SESSION['user']) && isset($_SESSION['user']['site'])){
			$site = $_SESSION['user']['site'];
		}elseif(isset($_REQUEST['site'])){
			$site = $_REQUEST['site'];
		}
		return $site;
	}

	/**
	 * @return bool|string
	 */
	public static function GetUserSite(){
		$site = false;
		if(isset($_SESSION['user']) && isset($_SESSION['user']['site'])){
			$site = $_SESSION['user']['site'];
		}
		return $site;
	}

	public static function GetRequestSite(){
		$site = 'default';
		if(isset($_REQUEST['site'])){
			$site = $_REQUEST['site'];
		}
		return $site;
	}

	/**
	 * @return bool
	 */
	public static function DoUserSiteValidation(){
		$userSite = self::GetUserSite();
		$requestSite = self::GetRequestSite();

		if( isset($_SESSION['user']) &&
			isset($_SESSION['user']['auth']) &&
			$_SESSION['user']['auth'] == true
		){
			if(self::$allowSiteSwitch){
				return true;
			}

			if($userSite !== false && $requestSite != $userSite){
				$_SESSION['user'] = null;
				return false;
			}
			return true;
		}

		return true;
	}
}