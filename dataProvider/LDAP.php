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

		$filter = "(&(objectCategory=group) (cn={$this->ldap_app_group}))";
		$attr = ["member"];

		$this->log("LDAP: SEARCH: DN: {$this->ldap_dn} FILTER: {$filter}");

		$search = @ldap_search($this->ldap, $this->ldap_dn, $filter, $attr);

		if($search === false){
			$this->log("LDAP: Unable to search LDAP server");
			return [
				'success' => false,
				'error' => 'LDAP: Unable to search LDAP server'
			];
		}

		$groups = @ldap_get_entries($this->ldap, $search);

		$this->log("LDAP: SEARCH GROUPS:");
		$this->log(print_r($groups, true));

		if($groups['count'] == 0){
			@ldap_unbind($this->ldap);

			$this->log("LDAP: Group not found");

			return [
				'success' => false,
				'error' => 'LDAP: Group not found'
			];
		}

		include_once (ROOT . '/dataProvider/User.php');
		$User = new User();

		$new_users = 0;

		foreach ($groups as $group){

			if(!is_array($group)) continue;

			foreach($group['member'] as $member){

				//$this->log("LDAP: MEMBER:");
				//$this->log(print_r($member, true));

				$filter = "(&(objectCategory=user) (distinguishedName={$member}))";
				$attr = ["sAMAccountName","name","givenName","initials","SN","title"."userPrincipalName","objectClass","objectCategory","mail","gender","mobile"];

				//$this->log("LDAP: SEARCH: DN: {$this->ldap_dn} FILTER: {$filter}");
				$search = @ldap_search($this->ldap, $this->ldap_dn, $filter, $attr);


				if($search === false){
					$this->log("LDAP: Member search error");
					continue;
				}

				$user = @ldap_get_entries($this->ldap, $search);

				if($user['count'] == 0){
					continue;
				}

				$user_object = new stdClass();
				$user_object->code = strtoupper(isset($user[0]['samaccountname'][0]) ? $user[0]['samaccountname'][0] : '');
				$user_object->username = strtolower(isset($user[0]['samaccountname'][0]) ? $user[0]['samaccountname'][0] : '');
				$user_object->title = '';
				$user_object->fname = isset($user[0]['givenname'][0]) ? $user[0]['givenname'][0] : '';
				$user_object->mname = isset($user[0]['initials'][0]) ? $user[0]['initials'][0] : '';
				$user_object->lname = isset($user[0]['sn'][0]) ? $user[0]['sn'][0] : '';
				$user_object->sex = isset($user[0]['gender'][0]) ? $user[0]['gender'][0] : '';
				$user_object->email = isset($user[0]['mail'][0]) ? $user[0]['mail'][0] : '';
				$user_object->mobile = isset($user[0]['mobile'][0]) ? $user[0]['mobile'][0] : '';
				$user_object->authorized = 0;
				$user_object->active = 0;
				$user_object->direct_address = '';
				$user_object->notes = 'Imported from LDAP';

				if($user_object->username === ''){
					$this->log("LDAP: UNABLE TO SYNC USER:");
					$this->log(print_r($user_object, true));
					continue;
				}

				$user_record = $User->getUserUsername($user_object->username);
				if($user_record === false){
					$this->log("LDAP: NEW USER: {$user_object->username}");
					$User->addUser($user_object);
					$new_users++;
				}
				unset($user_object, $user, $user_record);

			}
		}

		@ldap_unbind($this->ldap);

		return [
			'success' => true,
			'message' => sprintf('%s New Users Synced', $new_users)
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

			$this->log("LDAP: SEARCH: DN: {$this->ldap_dn} FILTER: {$filter}");

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

				$this->log("LDAP: User Not Authorized (not found)");

				return [
					'success' => false,
					'error' => 'LDAP: User Not Authorized'
				];
			}

			foreach ($entries as $entry){

				if(!is_array($entry)) continue;

				$in_group = false;

				if(!isset($entry['memberof'])){
					$this->log("LDAP: memberof not set");
					$this->log(print_r($entry, true));
				}

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

				$this->log("LDAP: User Not Authorized (not in group)");

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