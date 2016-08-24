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
class Merge {

	/**
	 * @var PDO
	 */
	private $conn;

	/**
	 * @var array
	 */
	private $tables = [];

	function __construct(){
		$this->conn = Matcha::getConn();
		$this->setTablesWithPids();

	}

	/**
	 * @param $primaryPid
	 * @param $transferPid
	 *
	 * @return bool
	 */
	public function merge($primaryPid, $transferPid){
		try{
			$this->conn->beginTransaction();
			foreach($this->tables as $t){
				$this->conn->exec("UPDATE `$t` SET `pid` = '$primaryPid' WHERE `pid` = '$transferPid'");
			}
			$this->conn->exec("DELETE FROM `patient` WHERE `pid` = '$transferPid'");
			$this->conn->commit();
			return true;
		}catch (Exception $e){
			error_log($e->getMessage());
			$this->conn->rollBack();
			return false;
		}
	}

	/**
	 * @param $primaryPubpid
	 * @param $transferPubpid
	 * @param bool $transferRecordIfPrimaryNotFound
	 * @return bool
	 */
	public function mergeByPubpid($primaryPubpid, $transferPubpid, $transferRecordIfPrimaryNotFound = false){
		$sth = $this->conn->prepare('SELECT pid FROM patient WHERE pubpid = ?');
		$sth->execute(array($primaryPubpid));
		$primary = $sth->fetch(PDO::FETCH_ASSOC);
		$sth->execute(array($transferPubpid));
		$transfer = $sth->fetch(PDO::FETCH_ASSOC);
		unset($sth);
		if($primary !== false && $transfer !== false){
			return $this->merge($primary['pid'], $transfer['pid']);
		}elseif($primary === false && $transfer !== false && $transferRecordIfPrimaryNotFound){
			$sth = $this->conn->prepare('UPDATE `patient` SET `pubpid` = ? WHERE `pid` = ?');
			$sth->execute(array($primaryPubpid, $transfer['pid']));
			return true;
		}else{
			return false;
		}
	}

	private function  setTablesWithPids(){

		$this->tables = [];
		$sth = $this->conn->prepare('SHOW TABLES');
		$sth->execute();
		$tables = $sth->fetchAll(PDO::FETCH_ASSOC);

		foreach($tables as $table){
			$sth = $this->conn->prepare("SHOW COLUMNS FROM ? where Field = 'pid'");
			$sth->execute([$table]);
			$column = $sth->fetch(PDO::FETCH_ASSOC);

			if($column !== false){
				$this->tables[] = $table;
			}
		}
	}

} 