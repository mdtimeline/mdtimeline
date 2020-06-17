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

			$table = '';

			$this->conn->beginTransaction();
			$this->conn->exec('SET FOREIGN_KEY_CHECKS = 0;');

			foreach($this->tables as $table){
				if($table == 'patient' || $table == 'patient_temp') continue;
				$this->conn->exec("UPDATE `{$table}` SET `pid` = '{$primaryPid}' WHERE `pid` = '{$transferPid}';");
			}

			$this->conn->exec("DELETE FROM `patient` WHERE `pid` = '{$transferPid}';");
			$this->conn->exec('SET FOREIGN_KEY_CHECKS = 0;');
			$this->conn->commit();

			if(file_exists(ROOT. '/modules/worklist/dataProvider/WorkListMerge.php')){
				include_once (ROOT. '/modules/worklist/dataProvider/WorkListMerge.php');
				$WorkListMerge = new modules\worklist\dataProvider\WorkListMerge();
				$WorkListMerge->MergePacsPatientByPid($transferPid, $primaryPid);
				unset($Dcm4Chee);
			}

			return true;
		}catch (Exception $e){
			error_log($e->getMessage());
			$this->conn->rollBack();
			return $e->getMessage() . 'Table: '. $table;
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
			error_log("Patient Merge - Primary PID: {$primary['pid']} - Transfer PID: {$transfer['pid']}");
			return $this->merge($primary['pid'], $transfer['pid']);

		}elseif($primary === false && $transfer !== false && $transferRecordIfPrimaryNotFound){
			error_log("Patient Transfer - Primary Record Number: {$primaryPubpid} Transfer PID: {$transfer['pid']}");
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
		$tables = $sth->fetchAll(PDO::FETCH_NUM);

		foreach($tables as $table){
			if($table[0] == 'patient') continue;
			$sth = $this->conn->prepare("SHOW COLUMNS FROM `{$table[0]}` where Field = 'pid'");
			$sth->execute();
			$column = $sth->fetch(PDO::FETCH_ASSOC);

			if($column !== false){
				$this->tables[] = $table[0];
			}
		}
	}

} 