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

class Facilities {

	/**
	 * @var MatchaCUP
	 */
	private $f;

	/**
	 * @var MatchaCUP
	 */
	private $c;

	/**
	 * @var MatchaCUP
	 */
	private $d;


	private function setFacilityModel(){
		if(!isset($this->f)){
			$this->f = MatchaModel::setSenchaModel('App.model.administration.Facility');
		}
	}

	private function setFacilityConfigModel(){
		if(!isset($this->c)){
			$this->c = MatchaModel::setSenchaModel('App.model.administration.FacilityStructure');
		}
	}

	private function setDepartmentModel(){
		if(!isset($this->d)){
			$this->d = MatchaModel::setSenchaModel('App.model.administration.Department');
		}
	}

    //------------------------------------------------------------------------------------------------------------------
    // Main Sencha Model Getter and Setters
    //------------------------------------------------------------------------------------------------------------------
	/**
	 * @param $params
	 * @return array
	 */
	public function getFacilities($params){
		$this->setFacilityModel();
		$rows = [];
		foreach($this->f->load($params)->all() as $row){
			$row['pos_code'] = str_pad($row['pos_code'], 2, '0', STR_PAD_LEFT);
			array_push($rows, $row);
		}
		return $rows;
	}

	/**
	 * @param $params
	 * @return mixed
	 */
	public function getFacility($params){
		$this->setFacilityModel();
		return $this->f->load($params)->one();
	}

	/**
	 * @param stdClass $params
	 * @return array
	 */
	public function addFacility($params){
		$this->setFacilityModel();
		return $facility = $this->f->save($params);
	}

	/**
	 * @param $params
	 * @return array
	 */
	public function updateFacility($params){
		$this->setFacilityModel();
		return $this->f->save($params);
	}

	/**
	 * @param $params
	 * @return mixed
	 */
	public function deleteFacility($params){
		$this->setFacilityModel();
		return $this->f->destroy($params);
	}

	/**
	 * @param $facilityId
	 * @return mixed
	 */
	public function setFacility($facilityId){
		return $_SESSION['user']['facility'] = $facilityId;
	}

	/**
	 * @param bool $getData
	 * @return mixed
	 */
	public function getCurrentFacility($getData = false){
		$this->setFacilityModel();
		$foo = isset($_SESSION['user']['facility']) ? $_SESSION['user']['facility'] : '1';
		if($getData) return $this->f->load($foo)->one();
		return $_SESSION['user']['facility'];
	}

	//------------------------------------------------------------------------------------------------------------------
	// Extra methods
	// This methods are used by the view to gather extra data from the store or the model
	//------------------------------------------------------------------------------------------------------------------
	/**
	 * @param $fid
	 * @return string
	 */
	public function getFacilityInfo($fid){
		$this->setFacilityModel();
		$resultRecord = $this->f->load(['id' => $fid], ['name', 'phone', 'street', 'city', 'state', 'postal_code'])->one();
		return 'Facility: ' . $resultRecord['name'] . ' ' . $resultRecord['phone'] . ' ' . $resultRecord['street'] . ' ' . $resultRecord['city'] . ' ' . $resultRecord['state'] . ' ' . $resultRecord['postal_code'];
	}

	/**
	 * @return mixed
	 */
	public function getActiveFacilities(){
		$this->setFacilityModel();
		return $this->f->load(['active' => '1'])->all();
	}
	/**
	 * @return mixed
	 */
	public function getActiveFacility(){
		$this->setFacilityModel();
		return $this->f->load(['active' => '1'])->one();
	}

	/**
	 * @param $facilityId
	 * @return mixed
	 */
	public function getActiveFacilitiesById($facilityId){
		$this->setFacilityModel();
		return $this->f->load(['active' => '1', 'id' => $facilityId])->one();
	}

	/**
	 * @param $facilityId
	 * @return mixed
	 */
	public function getFacilityById($facilityId){
		$this->setFacilityModel();
		return $this->f->load(['id' => $facilityId])->one();
	}

	/**
	 * @return mixed
	 */
	public function getBillingFacilities(){
		$this->setFacilityModel();
		return $this->f->load(['active' => '1', 'billing_location' => '1'])->one();
	}

	///////////////////////////////////////////

	/**
	 * @param $params
	 * @param $tree
	 * @return mixed
	 */
	public function getFacilityConfigs($params, $tree = true){
		$this->setFacilityConfigModel();
		$records = [];

		$facilities = $this->getFacilities(['active' => 1]);

		foreach($facilities as $facility){
			$facility = (object) $facility;

			$sql = "SELECT f.*, d.title as `text`, false AS `leaf`, true AS `expanded`, false AS `expandable`, true AS `loaded`
					 FROM `facility_structures` AS f
				LEFT JOIN `departments` AS d ON f.foreign_id = d.id
				    WHERE  f.foreign_type = 'D' AND f.parentId = 'f{$facility->id}'";

			$departments = $this->c->sql($sql)->all();

			$departmentsRecords = [];
			foreach($departments as $i => $department){
				$department = (object) $department;
				$sql = "SELECT f.*, s.title as `text`, true AS `leaf`, true AS `expanded`, true AS `loaded`
					 FROM `facility_structures` AS f
				LEFT JOIN `specialties` AS s ON f.foreign_id = s.id
				    WHERE  f.foreign_type = 'S' AND f.parentId = '{$department->id}'";

				$specialties = $this->c->sql($sql)->all();




				if($tree){
					$department->children = $specialties;
					$departmentsRecords[] = $department;
				}else{

					foreach($specialties as $specialty){
						$department->specialties[$specialty['foreign_id']] = $specialty;
					}
					$departmentsRecords[$department->foreign_id] = $department;
				}
			}

			if($tree){
				$records[] = [
					'id' => 'f' . $facility->id,
					'text' => $facility->name,
					'leaf' => false,
					'expanded' => true,
					'expandable' => false,
					'children' => $departmentsRecords
				];
			}else{
				$facility->departments = $departmentsRecords;
				$records[$facility->id] = $facility;
			}
		}

		return $records;
	}

	/**
	 * @param stdClass $params
	 * @return array
	 */
	public function getFacilityConfig($params){
		$this->setFacilityConfigModel();
		return $this->c->load($params)->one();
	}

	/**
	 * @param $params
	 * @return array
	 */
	public function addFacilityConfig($params){
		$this->setFacilityConfigModel();
		return $this->c->save($params);
	}

	/**
	 * @param $params
	 * @return array
	 */
	public function updateFacilityConfig($params){
		$this->setFacilityConfigModel();
		return $this->c->save($params);
	}

	/**
	 * @param $params
	 * @return mixed
	 */
	public function deleteFacilityConfig($params){
		$this->setFacilityConfigModel();
		return $this->c->destroy($params);
	}

	///////////////////////////////////////////

	/**
	 * @param $params
	 * @return mixed
	 */
	public function getDepartments($params){
		$this->setDepartmentModel();
		return $this->d->load($params)->all();
	}

	/**
	 * @param $params
	 * @return array
	 */
	public function getDepartment($params){
		$this->setDepartmentModel();
		return $this->d->load($params)->one();
	}

	/**
	 * @param $params
	 * @return array
	 */
	public function addDepartment($params){
		$this->setDepartmentModel();
		return $this->d->save($params);
	}

	/**
	 * @param $params
	 * @return array
	 */
	public function updateDepartment($params){
		$this->setDepartmentModel();
		return $this->d->save($params);
	}

	/**
	 * @param $params
	 * @return mixed
	 */
	public function deleteDepartment($params){
		$this->setDepartmentModel();
		return $this->d->destroy($params);
	}


	//////////////////////////////////////////////////////

	public function geFacilitiesStructure(){
		return $this->getFacilityConfigs(null, false);
    }


	public function getFacilityTokenById($facility_id){
        $facility = $this->getFacilityById($facility_id);
        $tokens = [];

        if($facility === false){
            return $tokens;
        }

        $tokens['[FACILITY_ID]'] = $facility['id'];
        $tokens['[FACILITY_CODE]'] = $facility['code'];
        $tokens['[FACILITY_NAME]'] = $facility['name'];
        $tokens['[FACILITY_LEGAL_NAME]'] = isset($facility['legal_name']) ? $facility['legal_name'] : '';
        $tokens['[FACILITY_ATTN]'] = $facility['attn'];
        $tokens['[FACILITY_NPI]'] = $facility['npi'];
        $tokens['[FACILITY_EMAIL]'] = $facility['email'];
        $tokens['[FACILITY_PHONE_NUMBER]'] = $facility['phone'];
        $tokens['[FACILITY_FAX_NUMBER]'] = $facility['fax'];

        $tokens['[FACILITY_POSTAL_ADDRESS]'] = trim(sprintf('%s %s, %s, %s %s', $facility['postal_address'],$facility['postal_address_cont'],$facility['postal_city'], $facility['postal_state'], $facility['postal_zip_code']));
        $tokens['[FACILITY_POSTAL_ADDRESS_LINE_ONE]'] = isset($facility['postal_address']) ? $facility['postal_address'] : '';
        $tokens['[FACILITY_POSTAL_ADDRESS_LINE_TWO]'] = isset($facility['postal_address_cont']) ? $facility['postal_address_cont'] : '';
        $tokens['[FACILITY_POSTAL_CITY]'] = isset($facility['postal_city']) ? $facility['postal_city'] : '';
        $tokens['[FACILITY_POSTAL_STATE]'] = isset($facility['postal_state']) ? $facility['postal_state'] : '';
        $tokens['[FACILITY_POSTAL_ZIP]'] = isset($facility['postal_zip_code']) ? $facility['postal_zip_code'] : '';
        $tokens['[FACILITY_POSTAL_COUNTRY]'] = isset($facility['postal_country_code']) ? $facility['postal_country_code'] : '';

        $tokens['[FACILITY_PHYSICAL_ADDRESS]'] = trim(sprintf('%s %s, %s, %s %s', $facility['address'],$facility['address_cont'],$facility['city'], $facility['state'], $facility['postal_code']));
        $tokens['[FACILITY_PHYSICAL_ADDRESS_LINE_ONE]'] = isset($facility['address']) ? $facility['address'] : '';
        $tokens['[FACILITY_PHYSICAL_ADDRESS_LINE_TWO]'] = isset($facility['address_cont']) ? $facility['address_cont'] : '';
        $tokens['[FACILITY_PHYSICAL_CITY]'] = isset($facility['city']) ? $facility['city'] : '';
        $tokens['[FACILITY_PHYSICAL_STATE]'] = isset($facility['state']) ? $facility['state'] : '';
        $tokens['[FACILITY_PHYSICAL_ZIP]'] = isset($facility['postal_code']) ? $facility['postal_code'] : '';
        $tokens['[FACILITY_PHYSICAL_COUNTRY]'] = isset($facility['country_code']) ? $facility['country_code'] : '';

        return $tokens;
    }
}
