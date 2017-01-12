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


class LoincCodes {

	/**
	 * @var bool|MatchaCUP
	 */
	private $lr;

	function __construct() {
		$this->lr = MatchaModel::setSenchaModel('App.model.administration.LoincRadiologyCode', true);
	}

	public function getLoincRadiologyCodes($params){

		if(isset($params->query)){
			return $this->searchLoincRadiologyCodes($params);
		}

		return $this->lr->load($params)->all();
	}

	public function getLoincRadiologyCode($params){
		return $this->lr->load($params)->one();
	}

	public function addLoincRadiologyCode($params){
		return $this->lr->save($params);
	}

	public function updateLoincRadiologyCode($params){
		return $this->lr->save($params);
	}

	public function deleteLoincRadiologyCode($params){
		return $this->lr->destroy($params);
	}

	public function searchLoincRadiologyCodes($params){

		if(!isset($params->query)) {
			return [
				'total' => 0,
				'data' => []
			];
		}

		$params->query = trim($params->query);

		if($params->query == '') {
			return [
				'total' => 0,
				'data' => []
			];
		}

		$queries = explode(' ', $params->query);

		$where = [];
		$whereData = [];

		$where[] = "PartTypeName = 'Rad.Modality.Modality type'";

		foreach ($queries as $index => $query){

			$hasNumber = preg_match('/\d/', $query);

			$buff = "(LongCommonName LIKE :LongCommonName_{$index}";
			$whereData['LongCommonName_' . $index] = "%{$query}%";

			if($hasNumber){
				$buff .= " OR LoincNumber LIKE :LoincNumber_{$index}";
				$whereData['LoincNumber_' . $index] = "{$query}%";
			}

			$buff .= ')';

			$where[] = $buff;
		}

		$where = implode(' AND ', $where);

		$sql = "SELECT * FROM codes_loinc_radiology WHERE {$where} GROUP BY LoincNumber";

		return $this->lr->sql($sql)->all($whereData);
	}


	private function getImportSql($csv_file){

		$sql = "LOAD DATA LOCAL INFILE '{$csv_file}'
		        INTO TABLE codes_loinc_radiology
                FIELDS TERMINATED BY ','
                LINES TERMINATED BY '\r\n'
                IGNORE 1 LINES
				(
					LoincNumber,
					LongCommonName,
					PartNumber,
					PartTypeName,
					PartName,
					PartSequenceOrder,
					RID,
					PreferredName,
					RPID,
					LongName
			    );";

	}


}
