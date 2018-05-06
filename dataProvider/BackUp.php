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

include_once (ROOT. '/dataProvider/Globals.php');
include (ROOT. '/dataProvider/AWS.php');

class BackUp {

	/**
	 * @var MatchaCUP
	 */
	private $a;

	/**
	 * @var AWS
	 */
	private $AWS;

	private $ignore_tables = [
		'audit_transaction_log',
		'documents_data_%'
	];

	private $backup_warning_hours = 24;

	function __construct() {
		set_time_limit(0);
		$this->AWS = new AWS();
		$this->a = MatchaModel::setSenchaModel('App.model.administration.AuditLog');
	}

	function doBackUp($do_s3_upload = true){
		$db_result = $this->doDatabaseBackup($do_s3_upload);

		if($do_s3_upload){
			$this->doDocumentsBackup();
		}

		return [
			'success' => $db_result['success']
		];

	}


	function doDatabaseBackup($do_s3_upload){
		$options = '--compact --single-transaction --max_allowed_packet=1G --triggers ';
		$bk_hosname = site_db_host;
		$bk_username = site_db_username;
		$bk_password = site_db_password;
		$bk_database = site_db_database;
		$bk_tables = $this->getTables();
		$bk_tables = implode(' ', $bk_tables);
		$bk_directory = $this->getBackupDirectory();
		$bk_directory = rtrim($bk_directory, '/');
		$bk_filename = $this->getBackupFileName();
		$bk_file = "{$bk_directory}/{$bk_filename}.gz";

		$cmd = "mysqldump --host={$bk_hosname} --user={$bk_username} --password={$bk_password} {$options} {$bk_database} {$bk_tables} | gzip > {$bk_file}";

		$success = shell_exec($cmd);

		return [
			'success' => $success !== null
		];
	}

	function doDocumentsBackup(){

		$mysql_bk_directory = $this->getBackupDirectory();
		$this->AWS->uploadDirectory($mysql_bk_directory, 'mysql-backups');

		include (ROOT . '/dataProvider/FileSystem.php');
		$FileSystem = new FileSystem();
		$file_systems = $FileSystem->getFileSystems(null);

		foreach($file_systems as $file_system){
			$key_prefix = 'file_system-' . $file_system['id'] . '/';
			$this->AWS->uploadDirectory($file_system['dir_path'], "documents-backups/{$key_prefix}");
		}

		return;
	}


	function getBackUps($params){
		$bk_directory = $this->getBackupDirectory();
		$bk_directory = rtrim($bk_directory, '/');

		$backups = scandir($bk_directory);
		$backup_files = [];
		foreach($backups as $i => $backup){

			if($backup == '.' || $backup == '..' || $backup == '.DS_Store') continue;

			$file = $bk_directory .'/'. $backup;

			$backup_files[] = [
				'id' => $i + 1,
				'filename' => $file,
				'filesize' => $this->human_filesize(filesize($file), 1),
				'filemtime' => date("Y-m-d H:i:s", filemtime($file))
			];
		}

		return $backup_files;
	}

	function doBackupCheck(){
		$bk_directory = $this->getBackupDirectory();
		$bk_directory = rtrim($bk_directory, '/');

		$valid = false;
		$time_threshold = strtotime("-{$this->backup_warning_hours} hours");

		$backups = scandir($bk_directory);

		foreach($backups as $i => $backup){
			if($backup == '.' || $backup == '..' || $backup == '.DS_Store') continue;

			$file = $bk_directory .'/'. $backup;
			if(filemtime($file) > $time_threshold){
				$valid = true;
				break;
			}
		}

		return $valid;
	}

	function getTables(){
		$bk_tables = [];
		$db_name = 'Tables_in_'. site_db_database;
		$where = "{$db_name} NOT LIKE '" . implode("' OR {$db_name} NOT LIKE '", $this->ignore_tables) . "'";
		$sql = 'SHOW TABLES WHERE ' . $where;

		$conn = Matcha::getConn();
		$sth = $conn->prepare($sql);
		$sth->execute();
		$db_tables = $sth->fetchAll(PDO::FETCH_NUM);

		foreach($db_tables as $db_table){
			$bk_tables[] = $db_table[0];
		}
		return $bk_tables;

	}

	function getBackupDirectory(){
		return Globals::getGlobal('bakup_directory');
	}

	function getBackupFileName(){
		include_once (ROOT.'/classes/Tokens.php');
		$filename =  Globals::getGlobal('bakup_filename');
		return Tokens::StringReplace($filename);
	}

	function human_filesize($bytes, $decimals = 2) {
		$sz = 'BKMGTP';
		$factor = floor((strlen($bytes) - 1) / 3);
		return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
	}
}
