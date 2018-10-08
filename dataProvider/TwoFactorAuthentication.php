<?php
/**
GaiaEHR (Electronic Health Records)
Copyright (C) 2013 Certun, LLC.

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

include_once (ROOT. '/lib/Authy/AuthyApi.php');
include_once (ROOT. '/lib/Authy/AuthyFormatException.php');
include_once (ROOT. '/lib/Authy/AuthyResponse.php');
include_once (ROOT. '/lib/Authy/AuthyToken.php');
include_once (ROOT. '/lib/Authy/AuthyUser.php');

class TwoFactorAuthentication
{

	private $AuthyApi;

	/**
	 * TwoFactorAuthentication constructor.
	 * @throws Exception
	 */
    function __construct()
    {

	    include (ROOT . '/lib/Authy/authy-autoloader.php');

//    	$authy_enable = Globals::getGlobal('authy_2fa_enable');
//
//	    if($authy_enable == '0'){
//		    throw new Exception("Authy Disabled");
//	    }

    	$api_key = Globals::getGlobal('authy_api_key');

    	if($api_key == ''){
			throw new Exception("Authy API Key not defined");
	    }

    	$this->AuthyApi = new \Authy\AuthyApi($api_key);
    }

	/**
	 * @param $user_id
	 * @param $user_type
	 * @param $token
	 * @return array|bool
	 * @throws Exception
	 */
    public function verifyTokenByUserIdAndType($user_id, $user_type, $token){
	    $authy_id = $this->getUserAuthyId($user_id, $user_type);

	    if($authy_id === false){
		    return false;
	    }

	    $response = $this->AuthyApi->verifyToken($authy_id,$token);

	    if(!$response->ok()){
		    return [
			    'success' => false,
			    'errors' => $this->getErrors($response)
		    ];
	    }

	    return [
		    'success' => true,
		    'message' => $response->message()
	    ];
    }

	/**
	 * @param $user_id
	 * @param $user_type
	 * @return bool| array
	 * @throws Exception
	 */
	public function requestSmsByUserIdAndType($user_id, $user_type){

		$authy_id = $this->getUserAuthyId($user_id, $user_type);

		if($authy_id === false){
			return false;
		}

		$sms = $this->AuthyApi->requestSms($authy_id, array("action" => "login", "action_message" => "Login code"));

		if(!$sms->ok()){
			return [
				'success' => false,
				'errors' => $this->getErrors($sms)
			];
		}else{
			return [
				'success' => true
			];
		}

	}

	/**
	 * @param $user_id
	 * @param $user_type
	 * @return \Authy\AuthyResponse|bool
	 * @throws Exception
	 */
	public function getUserStatusByUserIdAndType($user_id, $user_type)
	{
		$authy_id = $this->getUserAuthyId($user_id, $user_type);

		if($authy_id === false){
			return false;
		}

		return $this->AuthyApi->userStatus($authy_id);
	}

	/**
	 * @param int $user_id
	 * @param string $user_type
	 * @param string $email
	 * @param string $cellphone
	 * @param int $country_code
	 * @return array
	 * @throws Exception
	 */
    public function registerUserByIdAndType ($user_id, $user_type, $email, $cellphone, $country_code = 1){

    	try{
		    $authy_user = $this->AuthyApi->registerUser($email, $cellphone, $country_code);

		    if(!$authy_user->ok()){
			    return [
				    'success' => false,
				    'errors' => $this->getErrors($authy_user)
			    ];
		    }

		    if($user_type === 'application'){
			    $sql = 'UPDATE users SET authy_id = ? WHERE id = ?';
		    }elseif($user_type === 'referring'){
			    $sql = 'UPDATE referring_providers  SET authy_id = ? WHERE id = ?';
		    }

		    if (!isset($sql)){
			    throw new Exception('Invalid User Type');
		    }

		    $conn = Matcha::getConn();
		    $sth = $conn->prepare($sql);
		    $sth->execute([$authy_user->id(), $user_id]);

		    return [
			    'success' => true,
			    'authy_id' =>  $authy_user->id()
		    ];
	    }catch (Exception $e){
		    return [
			    'success' => false,
			    'errors' =>  $e->getMessage()
		    ];
	    }
	}

	/**
	 * @param $user_id
	 * @param $user_type
	 * @return bool
	 * @throws Exception
	 */
	private function getUserAuthyId($user_id, $user_type){

		if($user_type === 'application'){
			$sql = 'SELECT authy_id FROM users WHERE id = ?';
		}elseif($user_type === 'referring'){
			$sql = 'SELECT authy_id FROM referring_providers WHERE id = ?';
		}

		if (!isset($sql)){
			throw new Exception('Invalid User Type');
		}

		$conn = Matcha::getConn();
		$sth = $conn->prepare($sql);
		$sth->execute([$user_id]);
		$user = $sth->fetch(PDO::FETCH_ASSOC);
		if($user === false || !isset($user['authy_id']) || $user['authy_id'] == ''){
			return false;
		}

		return $user['authy_id'];

	}

	private function getErrors(&$response){
		$errors = [];
		foreach($response->errors() as $field => $message) {
			$errors[] = "$field: $message";
		}
		return implode(', ', $errors);
	}

}
