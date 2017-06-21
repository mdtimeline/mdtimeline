<?php
/**
 * Created by IntelliJ IDEA.
 * User: ernesto
 * Date: 6/4/17
 * Time: 6:20 PM
 */
class LDAP {

	private $ldap;
	private $ldap_host;
	private $ldap_port;
	private $ldap_dn;
	private $ldap_user_domain;
	private $ldap_app_group;

	function __construct(){
		$this->ldap_host = Globals::getGlobal('ldap_host'); //'server.clinic.example.com';
		$this->ldap_port = Globals::getGlobal('ldap_port'); //389;
		$this->ldap_dn = Globals::getGlobal('ldap_dn'); //'OU=mditimeline,DC=clinic,DC=example,DC=com';
		$this->ldap_user_domain = Globals::getGlobal('ldap_user_domain'); //'@clinic.example.com';
		$this->ldap_app_group = Globals::getGlobal('ldap_app_group'); //'mdtimeline';]
	}

	/**
	 * @return bool
	 */
	private function Connect(){
		$this->ldap = ldap_connect($this->ldap_host, (int) $this->ldap_port);

		if($this->ldap === false) return false;

		ldap_set_option($this->ldap,LDAP_OPT_PROTOCOL_VERSION,3);
		ldap_set_option($this->ldap,LDAP_OPT_REFERRALS,0);
		return true;
	}

	/**
	 * @param $user
	 * @param $password
	 *
	 * @return array
	 */
	public function Bind($user, $password){

		$success = $this->Connect();

		if($success === false){
			return [
				'success' => false,
				'error' => 'LDAP: Unable to connect to LDAP server'
			];
		}

		if(isset($this->ldap_user_domain)){
			$username = $user . $this->ldap_user_domain;
		}else{
			$username = $user;
		}

		$bind = @ldap_bind($this->ldap, $username, $password);

		if(!$bind){
			// invalid name or password
			return [
				'success' => false,
				'error' => 'LDAP: Invalid username or password'
			];
		}

		try{

			// valid
			// check presence in groups
			$filter = "(sAMAccountName=".$user.")";
			$attr = array("memberof");

			$result = ldap_search($this->ldap, $this->ldap_dn, $filter, $attr);

			if($result === false){
				return [
					'success' => false,
					'error' => 'LDAP: Unable to search LDAP server'
				];
			}

			$entries = ldap_get_entries($this->ldap, $result);
			ldap_unbind($this->ldap);

			$valid = false;

			// check groups
			if(!isset($entries[0]) || !isset($entries[0]['memberof'])){
				return [
					'success' => false,
					'error' => 'LDAP: User rights not found'
				];
			}


			foreach($entries[0]['memberof'] as $grps) {
				if(strpos($grps, $this->ldap_app_group)) {
					$valid = true;
					break;
				}
			}

			if($valid) {
				return [
					'success' => true
				];
			} else {
				return [
					'success' => false,
					'error' => 'LDAP: User has no rights'
				];
			}

		}catch(Exception $e){
			return [
				'success' => false,
				'error' => 'LDAP: ' . $e->getMessage()
			];
		}
	}
}