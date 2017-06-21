<?php
/**
 * GaiaEHR (Electronic Health Records)
 * Copyright (C) 2013 Certun, LLC.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

include_once(ROOT . '/classes/Sessions.php');
include_once(ROOT . '/classes/Crypt.php');
include_once(ROOT . '/dataProvider/Patient.php');

class authProcedures {

	private $session;

	function __construct(){
		$this->session = new Sessions();
	}

	/**
	 * @param stdClass $params
	 * @return array
	 */
	public function login(stdClass $params){
		error_reporting(-1);

		// Check that the username do not pass
		// the maximum limit of the field.
		//
		// NOTE:
		// If this condition is met, the user did not
		// use the logon form. Possible hack.
		if(strlen($params->authUser) >= 26){
			return [
                'success' => false,
                'type' => 'error',
                'message' => 'Possible hack, please use the Logon Screen.'
            ];
		}

		// Check that the username do not pass
		// the maximum limit of the field.
		//
		// NOTE:
		// If this condition is met, the user did not
		// use the logon form. Possible hack.
		if(strlen($params->authPass) >= 15){
			return [
                'success' => false,
                'type' => 'error',
                'message' => 'Possible hack, please use the Logon Screen.'
            ];
		}
		// Simple check username
		if(!$params->authUser){
			return [
                'success' => false,
                'type' => 'error',
                'message' => 'The username field can not be in blank. Try again.'
            ];
		}
		// Simple check password
		if(!$params->authPass){
			return [
                'success' => false,
                'type' => 'error',
                'message' => 'The password field can not be in blank. Try again.'
            ];
		}


		if(isset($params->{'g-recaptcha-response'})){

			$recaptcha_response = $params->{'g-recaptcha-response'};
			$recaptcha_secret_ke = Globals::getGlobal('recaptcha_secret_key');
			$ch = curl_init('https://www.google.com/recaptcha/api/siteverify');
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS,
				http_build_query([
			        'secret' => $recaptcha_secret_ke,
			        'response' => $recaptcha_response
				])
			);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$server_validation = curl_exec ($ch);

			$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

			curl_close ($ch);

			if($httpcode === 200){

				$server_validation = json_decode($server_validation, true);

				if($server_validation['success'] == false){
					return [
						'success' => false,
						'type' => 'error',
						'message' => 'Unable to validate reCaptcha'
					];
				}
			}


		}

		// remove empty spaces single and double quotes from username and password
		$params->authUser = trim(str_replace(array('\'', '"'), '', $params->authUser));
		$params->authPass = trim(str_replace(array('\'', '"'), '', $params->authPass));

		// Username & password match
		// Only bring authorized and active users.
		$u = MatchaModel::setSenchaModel('App.model.administration.User');
		$user = $u->load(
			[
				'username' => $params->authUser,
				'authorized' => 1,
				'active' => 1
			],
			[
				'id',
				'code',
				'username',
				'title',
				'fname',
				'mname',
				'lname',
				'email',
				'facility_id',
				'npi',
				'password',
				'password_date'
			]
		)->one();

		$ldap_enabled = Globals::getGlobal('ldap_enabled');

		if($ldap_enabled){
			include_once (ROOT . '/dataProvider/LDAP.php');
			$LDAP = new LDAP();
			$ldap_response = $LDAP->Bind($params->authUser,$params->authPass);

			if(!$ldap_response['success']){
				return [
					'success' => false,
					'type' => 'error',
					'message' => $ldap_response['error']
				];
			}

			// LDAP auth ok but user not found in system
			if($user === false){
				return [
					'success' => false,
					'type' => 'error',
					'message' => 'LDAP user not found in application.'
				];
			}

			return $this->doAuth($params, $user);

		}elseif($user === false || $params->authPass != $user['password']){
			return [
				'success' => false,
				'type' => 'error',
				'message' => 'The username or password you provided is invalid.'
			];
		}else{
			return $this->doAuth($params, $user);
		}
	}

	public function doAuth($params, $user){

		$user = (array)$user;

		// Change some User related variables and go
		$_SESSION['user']['id'] = $user['id'];
		$_SESSION['user']['code'] = $user['code'];
		$_SESSION['user']['username'] = $user['username'];
		$_SESSION['user']['name'] = trim($user['title'] . ' ' . $user['lname'] . ', ' . $user['fname'] . ' ' . $user['mname']);
		$_SESSION['user']['title'] = $user['title'];
		$_SESSION['user']['fname'] = $user['fname'];
		$_SESSION['user']['lname'] = $user['lname'];
		$_SESSION['user']['mname'] = $user['mname'];
		$_SESSION['user']['email'] = $user['email'];
		$_SESSION['user']['facility'] = (!isset($params->facility) || $params->facility == 0) ? $user['facility_id'] : $params->facility;
		$_SESSION['user']['localization'] = isset($params->lang) ? $params->lang : 'en_US';
		$_SESSION['user']['npi'] = $user['npi'] ;
		$_SESSION['user']['site'] = site_name;
		$_SESSION['user']['auth'] = true;
		$_SESSION['user']['acl_groups'] = ACL::getUserGroups();
		$_SESSION['user']['acl_roles'] = ACL::getUserRoles();
		$_SESSION['site']['localization'] = $_SESSION['user']['localization'];
		$_SESSION['site']['checkInMode'] = isset($params->checkInMode) ? $params->checkInMode: false;
		$_SESSION['timeout'] = time();
		$_SESSION['user']['token'] = MatchaUtils::__encrypt('{"uid":' . $user['id'] . ',"sid":' . $this->session->loginSession() . ',"site":"' . site_name . '"}');
		$_SESSION['inactive']['timeout'] = time();

		unset($db);

		session_regenerate_id();

		$password_exp_flag = Globals::getGlobal('password_expiration');
		if($password_exp_flag && $password_exp_flag != ''){

			if(is_numeric($password_exp_flag)){
				$password_exp_flag .= 'D';
			}

			$threshold = new DateTime();
			$threshold->sub(new DateInterval("P{$password_exp_flag}"));
			$password_exp = new DateTime($user['password_date']);

			$_SESSION['user']['password_expired'] = $password_exp < $threshold;
		}else{
			$_SESSION['user']['password_expired'] = false;
		}

		return [
			'success' => true,
			'token' => $_SESSION['user']['token'],
			'user' => $_SESSION['user']
		];
	}

	/**
	 * unAuth
	 * A method executed from GaiaEHR to logout the user and destroys the session
	 * @static
	 * @return mixed
	 */
	public function unAuth(){
		try
		{
			$this->session->logoutSession();
			session_regenerate_id();
			session_unset();
			session_destroy();
			return;
		}
		catch(Exception $ErrorObject)
		{
			// TODO: Configure a way to return the Exceptions to the GaiaEHR Client
			return;
		}
	}

	/**
	 * @static
	 * @return array
	 */
	public function ckAuth(){

		if(isset($_SESSION['session_id']) &&
            isset($_SESSION['user']) &&
            isset($_SESSION['user']['auth']) &&
            $_SESSION['user']['auth']){
			$this->session->updateSession();
			return array('authorized' => true, 'user' => $_SESSION['user']);
		} elseif(isset($_SESSION['session_id']) && (isset($_SESSION['user']) && isset($_SESSION['user']['auth']) && !$_SESSION['user']['auth'])){
			$this->unAuth();
			return array('authorized' => false);
		}else{
			return array('authorized' => false);
		}
	}

	public function getSites(){
		$rows = array();
		foreach($_SESSION['sites']['sites'] as $row){
			$site['site_id'] = $row;
			$site['site'] = $row;
			array_push($rows, $site);
		}
		return $rows;
	}

}
