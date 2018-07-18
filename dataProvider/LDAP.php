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
	private $ldap_user_domains;
	private $ldap_app_group;

	function __construct(){
		$this->ldap_host = Globals::getGlobal('ldap_host'); //'server.clinic.example.com';
		$this->ldap_port = Globals::getGlobal('ldap_port'); //389;
		$this->ldap_dn = Globals::getGlobal('ldap_dn'); //'OU=mditimeline,DC=clinic,DC=example,DC=com';
		$this->ldap_user_domains = explode(',', Globals::getGlobal('ldap_user_domains')); //'user@clinic.example.com';
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
	 * @param $username
	 * @param $password
	 * @param $user
	 *
	 * @return array
	 */
	public function Bind($username, $password, $user){

		$success = $this->Connect();

		if(!isset($_ENV['ldap_service_account_username']) || !isset($_ENV['ldap_service_account_password'])){
			return [
				'success' => false,
				'error' => 'LDAP: Service Account Error'
			];
		}

		if($user === false || !isset($user['ldap_domain'])){
			// invalid name or password
			return [
				'success' => false,
				'error' => 'LDAP: User domain not defined'
			];
		}

		if(array_search($user['ldap_domain'], $this->ldap_user_domains) === false){
			// invalid name or password
			return [
				'success' => false,
				'error' => 'LDAP: User domain invalid'
			];
		}

		if($success === false){
			return [
				'success' => false,
				'error' => 'LDAP: Unable to connect to LDAP server'
			];
		}

		$bind = @ldap_bind($this->ldap, $_ENV['ldap_service_account_username'], $_ENV['ldap_service_account_password']);

		if(!$bind){
			// invalid name or password
			return [
				'success' => false,
				'error' => 'LDAP: Couldn\'t bind to LDAP as application user'
			];
		}

		try{

			// valid
			// check presence in groups
			$filter = "(sAMAccountName=".$username.")";
			$attr = ["memberof"];

			$search = ldap_search($this->ldap, $this->ldap_dn, $filter, $attr);

			if($search === false){
				return [
					'success' => false,
					'error' => 'LDAP: Unable to search LDAP server'
				];
			}

			$entries = ldap_get_entries($this->ldap, $search);
			ldap_unbind($this->ldap);

			$valid = false;

			foreach($entries[0]['memberof'] as $grps) {
				if(strpos($grps, $this->ldap_app_group)) {
					$valid = true;
					break;
				}
			}

			if($valid){

				if ((int) @$entries['count'] > 0) {

					foreach ($entries as $entry){

						$userdn = $entry['dn'];
						$auth_status = ldap_bind($this->ldap, $userdn, $password);

						if ($auth_status === false) {
							continue;
						}

						$valid = true;
						break;

					}

					$auth_status = ldap_bind($this->ldap, $this->ldap_dn, $password);
					if ($auth_status === FALSE) {
						die("Couldn't bind to LDAP as user!");
					}

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