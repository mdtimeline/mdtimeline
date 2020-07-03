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
	 * @return array
	 */
	private function Connect(){

		if(!isset($_ENV['ldap_service_account_username']) || !isset($_ENV['ldap_service_account_password'])){
			$this->log("LDAP: Service Account Error");
			return [
				'success' => false,
				'error' => 'LDAP: Service Account Error'
			];
		}

		$this->log("LDAP: CONNECT: HOST: {$this->ldap_host} PORT: {$this->ldap_port}");

		$this->ldap = ldap_connect($this->ldap_host, (int) $this->ldap_port);

		if($this->ldap === false) {
			$this->log("LDAP: Unable to connect to LDAP server");
			return [
				'success' => false,
				'error' => 'LDAP: Unable to connect to LDAP server'
			];
		}

		ldap_set_option($this->ldap,LDAP_OPT_PROTOCOL_VERSION,3);
		ldap_set_option($this->ldap,LDAP_OPT_REFERRALS,0);

		if(isset($_ENV['ldap_service_tls']) && $_ENV['ldap_service_tls']){
			$tls = @ldap_start_tls($this->ldap);
			if(!$tls){
				$this->log("LDAP: Could not bind to LDAP TSL as application user");
				return [
					'success' => false,
					'error' => 'LDAP: Could not bind to LDAP TSL as application user'
				];
			}
		}

		$this->log("LDAP: BINDING: USER: {$_ENV['ldap_service_account_username']} PASS: {$_ENV['ldap_service_account_password']}");

		$bind = @ldap_bind($this->ldap, $_ENV['ldap_service_account_username'], $_ENV['ldap_service_account_password']);

		if(!$bind){
			// invalid name or password
			$this->log("LDAP: Could not bind to LDAP as application user");
			return [
				'success' => false,
				'error' => 'LDAP: Could not bind to LDAP as application user'
			];
		}

		return [
			'success' => true,
		];

	}

	public function Sync(){

		$response = $this->Connect();

		if(!$response['success']){
			return $response;
		}

		$filter = "(groupName={$this->ldap_app_group})";
		$attr = ["memberof"];

		$search = @ldap_search($this->ldap, $this->ldap_dn, $filter, $attr);

		if($search === false){
			$this->log("LDAP: Unable to search LDAP server");
			return [
				'success' => false,
				'error' => 'LDAP: Unable to search LDAP server'
			];
		}

		$entries = @ldap_get_entries($this->ldap, $search);

		print_r($entries);

		@ldap_unbind($this->ldap);

		return [
			'success' => true
		];
	}

	/**
	 * @param $username
	 * @param $password
	 * @param $user
	 *
	 * @return array
	 */
	public function Bind($username, $password, $user){

		$response = $this->Connect();

		if(!$response['success']){
			return $response;
		}

		try{

			// valid
			// check presence in groups
			$filter = "(sAMAccountName=".$username.")";
			$attr = ["memberof"];

			$this->log("LDAP: SEARCH: DN: {$this->ldap_dn} FILTER: {$username}");

			$search = @ldap_search($this->ldap, $this->ldap_dn, $filter, $attr);

			if($search === false){

				$this->log("LDAP: Unable to search LDAP server");

				return [
					'success' => false,
					'error' => 'LDAP: Unable to search LDAP server'
				];
			}

			$entries = @ldap_get_entries($this->ldap, $search);

			$valid = false;

			if($entries['count'] == 0){
				@ldap_unbind($this->ldap);

				$this->log("LDAP: User Not Authorized");

				return [
					'success' => false,
					'error' => 'LDAP: User Not Authorized'
				];
			}

			foreach ($entries as $entry){

				if(!is_array($entry)) continue;

				$in_group = false;

				foreach($entry['memberof'] as $grps) {
					if(strpos($grps, $this->ldap_app_group)) {
						$in_group = true;
						break;
					}
				}

				if(!$in_group) continue;

				if ($entry['count'] == 0 || !isset($entry['dn'])) continue;

				$userdn = $entry['dn'];
				$auth_status = @ldap_bind($this->ldap, $userdn, $password);

				if ($auth_status !== false) {
					$valid = true;
					break;
				}
			}

			@ldap_unbind($this->ldap);

			if($valid) {
				return [
					'success' => true
				];
			} else {
				return [
					'success' => false,
					'error' => 'LDAP: User Not Authorized'
				];
			}

		}catch(Exception $e){
			return [
				'success' => false,
				'error' => 'LDAP: ' . $e->getMessage()
			];
		}
	}

	private function log($message){
		if(isset($_ENV['ldap_log']) && $_ENV['ldap_log'] === true){
			error_log($message);
		}
	}
}