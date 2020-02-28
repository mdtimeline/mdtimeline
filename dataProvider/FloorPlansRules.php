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

class FloorPlansRules {

	/**
	 * @var bool|MatchaCUP
	 */
	private $fpr;


	function __construct(){
		$this->fpr = MatchaModel::setSenchaModel('App.model.areas.FloorPlansRule');
	}

	public function getFloorPlansRules($params){

		$values = [];

		$sql = "SELECT par.id,
					   pa.id AS pool_area_id,
       				   pa.`title` as pool_area,
					   u.id AS provider_user_id,
					   fpz.id AS zone_id,
        			   fpz.`title` AS zone,				      
					   f.`name` AS facility,
	  				   CONCAT(u.lname, ', ', u.fname) as provider,
	  				   pa.floor_plan_id as floor_plan_id
				FROM users AS u
				LEFT JOIN pool_areas AS pa ON pa.id IS NOT NULL
				LEFT JOIN pool_areas_rules AS par ON par.provider_user_id = u.id AND par.pool_area_id = pa.id
				INNER JOIN facility AS f ON f.id = pa.facility_id
				LEFT JOIN floor_plans_zones AS fpz ON fpz.id = par.zone_id
				WHERE u.is_attending = '1' AND pa.floor_plan_id IS NOT NULL";

		if(isset($params->facility_id)){
			$values[':facility_id'] = $params->facility_id;
			$sql .= ' AND f.id = :facility_id';
		}

		return $this->fpr->sql($sql)->all($values);
	}

	public function getFloorPlansRule($params){
		return $this->fpr->load($params)->one();
	}

	public function addFloorPlansRule($params){
		return $this->fpr->save($params);
	}

	public function updateFloorPlansRule($params){
		return $this->fpr->save($params);
	}

	public function destroyFloorPlansRule($params){
		return $this->fpr->destroy($params);
	}

	public function getFloorPlansRuleByFloorPlanRecord($floor_plan_record){

		$provider_uid = null;
		$conn = Matcha::getConn();

		if(isset($floor_plan_record->eid) && $floor_plan_record->eid > 0){
			$sql = "SELECT provider_uid FROM encounters WHERE eid = :eid";
			$sth = $conn->prepare($sql);
			$sth->execute([':eid' => $floor_plan_record->eid]);
			$encounter = $sth->fetch(PDO::FETCH_ASSOC);
			if($encounter !== false){
				$provider_uid = $encounter['provider_uid'];
			}
		}

		if(!$provider_uid && isset($floor_plan_record->appointment_id) && $floor_plan_record->appointment_id > 0){
			$sql = "SELECT provider_id FROM appointments_events WHERE id = :appointment_id";
			$sth = $conn->prepare($sql);
			$sth->execute([':appointment_id' => $floor_plan_record->appointment_id]);
			$appointment = $sth->fetch(PDO::FETCH_ASSOC);
			if($appointment !== false){
				$provider_uid = $appointment['provider_id'];
			}
		}

		if(!isset($provider_uid)){
			return false;
		}

		return $this->fpr->load([
			'pool_area_id' => $floor_plan_record->area_id,
			'provider_user_id' => $provider_uid
		])->leftJoin(['title' => 'zone'],'floor_plans_zones', 'zone_id', 'id')->one();
	}


}
