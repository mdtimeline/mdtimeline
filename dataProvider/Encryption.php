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

include_once(ROOT .'/classes/Crypt.php');

class Encryption
{

	function Encrypt($data){
		return Crypt::encrypt($data);
	}

	function Decrypt($data){
		return Crypt::decrypt($data);
	}

	function Convert($column, $table, $to_openssl){

		$conn = Matcha::getConn();

		$sth = $conn->prepare("SHOW KEYS FROM `{$table}` WHERE Key_name = 'PRIMARY'");
		$sth->execute();
		$primary = $sth->fetch(PDO::FETCH_ASSOC);
		$primary_comun = $primary['Column_name'];


		$sth = $conn->prepare("SELECT `{$primary_comun}`, `{$column}` FROM `{$table}`");
		$sth->execute();
		$records = $sth->fetchAll(PDO::FETCH_ASSOC);

		foreach($records as $record){
			$value = $record[$column];

			if(!isset($value)) continue;

			$pk_value = $record[$primary_comun];

			if($to_openssl){
				$new_value = MatchaUtils::__openssl_encrypt(MatchaUtils::__mcrypt_decrypt($value));
			}else{
				$new_value = MatchaUtils::__mcrypt_encrypt(MatchaUtils::__openssl_decrypt($value));
			}

			$sth = $conn->prepare("UPDATE `{$table}` SET `{$column}` = :new_value WHERE `{$primary_comun}` = :pk_value");
			$sth->execute([':new_value' => $new_value, ':pk_value' => $pk_value]);
		}

	}

}