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

include_once(ROOT . '/classes/MatchaHelper.php');
include_once(ROOT . '/classes/Crypt.php');
include_once(ROOT . '/classes/Network.php');
include_once(ROOT . '/dataProvider/User.php');

class Sessions {
	/**
	 * @var bool|MatchaCUP
	 */
	private $s;

	private function setModel(){
		if(!isset($this->s)){
			$this->s = MatchaModel::setSenchaModel('App.model.administration.UserSessions');
		}
	}

	public function loginSession(){
		$this->setModel();
		$data = new stdClass();
		$date = time();
		$data->sid = session_id();
		$data->uid = $_SESSION['user']['id'];
		$data->login = $date;
		$data->last_request = $date;
		$data->ip = Network::getIpAddress();
		$record = (object) $this->s->save($data);
		unset($data);
		return $_SESSION['session_id'] =  $record->id;
	}

	public function setSessionByToken($token){
		$this->setModel();
		$s = json_decode(Crypt::decrypt($token));
		$session = $this->s->load($s->sid)->one();
		if($session === false){
			$User = new User();
			$user = $User->getUser($session['uid']);
			$_SESSION['user']['name'] = $user['title'] . ' ' . $user['lname'] .  ', '  . $user['fname'] . ' ' . $user['mname'];
			$_SESSION['user']['id'] = $user['id'];
			$_SESSION['user']['email'] = $user['email'];
			$_SESSION['user']['site'] = $s->site;
			$_SESSION['user']['auth'] = true;
			unset($User, $user);
			return true;
		} else{
			return false;
		}
	}

	public function isActiveSession(){
		$conn = Matcha::getConn();
        $now = time();
        $last_request = $now - 120;
		$sth = $conn->prepare("SELECT * FROM `users_sessions` WHERE `id` = :id AND logout IS NULL AND last_request > :last_request;");
		$sth->execute([':id' => $_SESSION['session_id'], ':last_request' => $last_request]);
		$result = $sth->fetch(PDO::FETCH_ASSOC);
		return $result !== false;
	}

	public function updateSession(){
		$id = $_SESSION['session_id'];
		$last_request = $_SESSION['inactive']['timeout'] = time();
		Matcha::pauseLog(true);
		$conn = Matcha::getConn();
		$conn->exec("UPDATE `users_sessions` SET `last_request` = '{$last_request}' WHERE `id` = '{$id}'");
		Matcha::pauseLog(false);
		return true;
	}

	public function logoutSession(){
		$this->setModel();
		$logout = time();
		$this->s->sql("UPDATE `users_sessions` SET `logout` = '{$logout}' WHERE `id` = :id")->exec([ ':id' => $_SESSION['session_id'] ]);
		return true;
	}

	public function logoutInactiveUsers(){
		$this->setModel();
		$now = time();
		$last_request = $now - 60;
		$sql = 'UPDATE `users_sessions` SET `logout` = :now  WHERE last_request < :last_request AND logout IS NULL;';
		return $this->s->sql($sql)->exec([ ':now' => $now, ':last_request' => $last_request ]);
	}

	public function hasOpenSessionByUid($uid){
		$this->setModel();
		$now = time();
		$last_request = $now - 60;
		$sql = 'SELECT * FROM `users_sessions` WHERE uid = :uid AND last_request > :last_request AND logout IS NULL;';
		$result = $this->s->sql($sql)->one([ ':uid' => $uid, ':last_request' => $last_request ]);
		return $result !== false;
	}

	public function logoutSessionsByUid($uid){
		$this->setModel();
		$logout = time();
		$result = $this->s->sql("UPDATE `users_sessions` SET `logout` = '{$logout}' WHERE `uid` = :uid AND logout IS NULL;")->exec([ ':uid' => $uid ]);
		return true;
	}

}
//$s = new Sessions();
//$s->setSessionByToken("uzUc7qJ4YHc6F76WfoRnJwSycND+CLaUVmL2AcdEyHniHzONcq2C70wo7A+oA8aw\/C\/Q8UrRPZ7rrrmNut482w==");
