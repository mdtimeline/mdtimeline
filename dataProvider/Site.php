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

//				error_log('******************************************************');
//				error_log('************** DoUserSiteValidation ******************');
//				error_log('******************************************************');
//				error_log('$userSite = ' . $userSite);
//				error_log('$requestSite = ' . $requestSite);
//				error_log('------------------------------------------------------');
//				if(isset($_SERVER)) error_log('$_SERVER = ' . print_r($_SERVER, true));
//				error_log('------------------------------------------------------');
//				if(isset($_REQUEST)) error_log('$_REQUEST = ' . print_r($_REQUEST, true));
//				error_log('------------------------------------------------------');
//				if(isset($_SESSION)) error_log('$_SESSION = ' . print_r($_SESSION, true));
//				error_log('******************************************************');
//				error_log('******************************************************');
//				error_log('******************************************************');

				$_SESSION['user'] = null;
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


	public static function migrate($main_site, $merge_site){









	}
	public static function create_guid_col(){


		$conn = Matcha::getConn();

		$sth = $conn->prepare('SHOW TABLES');
		$sth->execute();
		$tables = $sth->fetchAll(PDO::FETCH_NUM);

		$response = '';


		foreach($tables as $table){

			$table_name = $table[0];

			// skip audit_transaction_log table
			if($table_name === 'audit_transaction_log' || $table_name[0] === '_'){
				continue;
			}



			// get primary key
			$sth = $conn->prepare("SHOW KEYS FROM `{$table_name}` WHERE Key_name = 'PRIMARY'");
			$sth->execute();
			$result = $sth->fetch(PDO::FETCH_ASSOC);

			if($result === false){
				error_log("NO PRIMARY KEY FOUND ON TABLE {$table_name}}");
				continue;
			}

			$primary_key = $result['Column_name'];


			// check if global_id exist
			$sth = $conn->prepare("SHOW COLUMNS FROM `{$table_name}` where Field = 'global_id'");
			$sth->execute();
			$column = $sth->fetch(PDO::FETCH_ASSOC);

			if($column === false){
				$sql = "ALTER TABLE `{$table_name}` ADD COLUMN `global_id` VARCHAR(40) NULL DEFAULT NULL AFTER `{$primary_key}`, ADD UNIQUE INDEX `IK_global_id` (`global_id` ASC);";
				$sth = $conn->prepare($sql);
				$sth->execute();
			}


			$conn->exec("DROP TRIGGER IF EXISTS `global_id`;");
			$conn->exec("CREATE DEFINER=`root`@`%` TRIGGER `global_id` 
BEFORE INSERT ON `{$table_name}` 
FOR EACH ROW 
BEGIN 
	IF NEW.`global_id` IS NULL OR NEW.`global_id` = '' THEN 
		SET NEW.`global_id` = UUID(); 
	END IF; 
	SET @last_global_id = NEW.`global_id`;
	
END");

		}




		return $response;

	}

}
