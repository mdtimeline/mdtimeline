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

class DecisionAids {

	/**
	 * @var MatchaCUP
	 */
	private $d;

	function __construct(){
        if($this->d == NULL)
            $this->d = MatchaModel::setSenchaModel('App.model.administration.DecisionAids', true);
	}

	public function getDecisionAids($params){
		return $this->d->load($params)->all();
	}

	public function getDecisionAid($params){
		return $this->d->load($params)->one();
	}

	public function addDecisionAid($params){
		return $this->d->save($params);
	}

	public function updateDecisionAid($params){
		return $this->d->save($params);
	}

	public function destroyDecisionAid($params){
		return $this->d->destroy($params);
	}

	/**
	 * @param $codes array
	 * @return array
	 */
	public function getDecisionAidsByTriggerCodes($codes){

		$where = [];
		$where_data = [];

		$where[] = '`trigger_code` = :trigger_code';
		$where_data[':trigger_code'] = '*';

		foreach ($codes as $index => $code){
			$where[] = '`trigger_code` = :trigger_code_' . $index;
			$where_data[':trigger_code_'. $index] = $code;
		}

		$where = implode(' OR ', $where);
		$sql = "SELECT * FROM `decision_aids_instructions` WHERE `active` = 1 AND ($where)";
		return $this->d->sql($sql)->all($where_data);
	}



}
