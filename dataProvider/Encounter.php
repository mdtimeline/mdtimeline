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

include_once(ROOT . '/dataProvider/Patient.php');
include_once(ROOT . '/dataProvider/User.php');
include_once(ROOT . '/dataProvider/Vitals.php');
include_once(ROOT . '/dataProvider/PoolArea.php');
include_once(ROOT . '/dataProvider/Allergies.php');
include_once(ROOT . '/dataProvider/Medications.php');
include_once(ROOT . '/dataProvider/ActiveProblems.php');
include_once(ROOT . '/dataProvider/Immunizations.php');
include_once(ROOT . '/dataProvider/Services.php');
include_once(ROOT . '/dataProvider/DiagnosisCodes.php');
include_once(ROOT . '/dataProvider/FamilyHistory.php');

class Encounter {
	/**
	 * @var PDO
	 */
	private $conn;

	/**
	 * @var Patient
	 */
	private $patient;
	/**
	 * @var
	 */
	private $eid;
	/**
	 * @var PoolArea
	 */
	private $poolArea;

	/**
	 * @var DiagnosisCodes
	 */
	private $diagnosis;

	/**
	 * @var
	 */
	private $EncounterHistory;

	/**
	 * @var bool|MatchaCUP
	 */
	private $e;
	/**
	 * @var bool|MatchaCUP
	 */
	private $ros;
	/**
	 * @var bool|MatchaCUP
	 */
	private $soap;
	/**
	 * @var bool|MatchaCUP
	 */
	private $d;
	/**
	 * @var bool|MatchaCUP
	 */
	private $hcfa;

	/**
	 * @var bool|MatchaCUP
	 */
	private $edx;

	function __construct() {
		$this->conn = Matcha::getConn();
		$this->patient = new Patient();
		$this->poolArea = new PoolArea();
		$this->diagnosis = new DiagnosisCodes();
		$this->FamilyHistory = new FamilyHistory();

        if(!isset($this->e)) $this->e = MatchaModel::setSenchaModel('App.model.patient.Encounter');
        if(!isset($this->ros)) $this->ros = MatchaModel::setSenchaModel('App.model.patient.ReviewOfSystems');
        if(!isset($this->soap)) $this->soap = MatchaModel::setSenchaModel('App.model.patient.SOAP');
        if(!isset($this->d)) $this->d = MatchaModel::setSenchaModel('App.model.patient.Dictation');
        if(!isset($this->hcfa)) $this->hcfa = MatchaModel::setSenchaModel('App.model.patient.HCFAOptions');
        if(!isset($this->edx)) $this->edx = MatchaModel::setSenchaModel('App.model.patient.EncounterDx');
	}

	private function setEid($eid) {
		$this->eid = $eid;
		/**
		 * This is a temporary variable to comfort the certification needed by GaiaEHR
		 * GAIAEH-177 GAIAEH-173 170.302.r Audit Log (core)
		 * Added by: Gino Rivera Falu
		 * Web Jul 31 2013
		 */
		$_SESSION['encounter']['id'] = $eid; // Added by Gino Rivera
	}

	/**
	 * @param $pid
	 * @return array
	 */
	public function checkOpenEncountersByPid($pid) {
		$params = new stdClass();
		$params->filter[0] = new stdClass();
		$params->filter[0]->property = 'pid';
		$params->filter[0]->value = $pid;
		$params->filter[1] = new stdClass();
		$params->filter[1]->property = 'close_date';
		$params->filter[1]->value = null;
		$records = $this->e->load($params)->all();
		unset($params);
		if(count($records['encounter']) > 0){
			return ['encounter' => true];
		} else {
			return ['encounter' => false];
		}
	}

	/**
	 * @param stdClass $params
	 * @return array
	 *  Naming: "createPatientEncounters"
	 */
	public function createEncounter(stdClass $params) {
		$record = $this->e->save($params);
		$encounter = (array)$record['encounter'];
		unset($record);
		$default = [
			'pid' => $encounter['pid'],
			'eid' => $encounter['eid'],
			'uid' => $encounter['open_uid'],
			'date' => date('Y-m-d H:i:s')
		];

		if($_SESSION['globals']['enable_encounter_review_of_systems']){
			$this->addReviewOfSystems((object)$default);
		}

		if($_SESSION['globals']['enable_encounter_family_history']){
			$familyHistory = $this->FamilyHistory->getFamilyHistoryByPid($encounter['pid']);
		}

		// TODO: Matcha Model
		if($_SESSION['globals']['enable_encounter_review_of_systems_cks']){

		}

		if($_SESSION['globals']['enable_encounter_soap']){
			$this->addSoap((object)$default);
		}

		if($_SESSION['globals']['enable_encounter_dictation']){
			$this->addDictation((object)$default);
		}

		if($_SESSION['globals']['enable_encounter_hcfa']){
			$this->addHCFA((object)$default);
		}

		$this->poolArea->updateCurrentPatientPoolAreaByPid([
			'eid' => $encounter['eid'],
			'priority' => $encounter['priority']
		], $encounter['pid']);
		$this->setEid($encounter['eid']);

		return [
			'success' => true,
			'encounter' => $encounter
		];
	}

	/**
	 * @param $params
	 * @param bool $relations
	 *
	 * @return array
	 */
	public function getEncounters($params, $relations = true) {
		$records = $this->e->load($params)->all();
		$encounters = (array)$records['encounter'];
		$relations = isset($params->relations) ? $params->relations : $relations;

		foreach($encounters as $i => $encounter){
			$encounters[$i]['status'] = ($encounter['close_date'] == null) ? 'open' : 'close';
			if($relations)
			    $encounters[$i] = $this->getEncounterRelations($encounters[$i]);
		}
		return $encounters;
	}

	/**
	 * @param $pid
	 * @param null $start
	 * @param null $end
	 * @return mixed
	 */
	public function getEncountersByPidAndDates($pid, $start = null, $end = null) {

		$this->e->addFilter('pid', $pid);

		if(isset($start)){
			$this->e->addFilter('service_date', $start, '>=');
		}
		if(isset($end)) {
			$this->e->addFilter('service_date', $end, '<=');
		}

		return $this->e->load()->all();;
	}

	/**
	 * @param $eid
	 * @param bool $relations
	 *
	 * @return array
	 */
	public function getEncounterByEid($eid, $relations = true) {

		$this->e->addFilter('eid', $eid);
		$encounter = $this->e->load()->one();

		if($encounter !== false && $relations){
			$encounter = $this->getEncounterRelations($encounter);
		}

		return $encounter;
	}

	/**
	 * @param stdClass|int $params
	 * @param bool $relations
	 * @param bool $allVitals include all patient vitals
	 *
	 * @return array|mixed
	 */
	public function getEncounter($params, $relations = true, $allVitals = true) {

		if(is_string($params) || is_int($params)){
			$filters = new stdClass();
			$filters->filter[0] = new stdClass();
			$filters->filter[0]->property = 'eid';
			$filters->filter[0]->value = $params;
			$record = $this->e->load($filters)->one();
		} else {
			$record = $this->e->load($params)->one();
		}

		if($record === false) return [];
		$encounter = (array)$record['encounter'];
		$this->setEid($encounter['eid']);
		unset($record);

		$relations = isset($params->relations) ? $params->relations : $relations;
		if($relations == false) return ['encounter' => $encounter];
		$encounter = $this->getEncounterRelations($encounter, $allVitals);

		unset($filters);
		return ['encounter' => $encounter];
	}

	private function getEncounterRelations($encounter, $allVitals = true) {
		$filters = new stdClass();
		$filters->filter[0] = new stdClass();
		$filters->filter[0]->property = 'eid';
		$filters->filter[0]->value = $encounter['eid'];

		$Vitals = new Vitals();
		if($_SESSION['globals']['enable_encounter_vitals']){
			$encounter['vitals'] = $allVitals ? $Vitals->getVitalsByPid($encounter['pid']) : $Vitals->getVitalsByEid($encounter['eid']);
		}
		unset($Vitals);

		if($_SESSION['globals']['enable_encounter_review_of_systems']){
			$encounter['reviewofsystems'][] = $this->getReviewOfSystems($filters);
		}

		if($_SESSION['globals']['enable_encounter_family_history']){
			$encounter['familyhistory'] = $this->FamilyHistory->getFamilyHistoryByPid($encounter['pid']);
		}

		//		//TODO: Matcha Model
		//		if($_SESSION['globals']['enable_encounter_review_of_systems_cks']){
		//
		//		}

		if($_SESSION['globals']['enable_encounter_soap']){
			$encounter['soap'][] = $this->getSoapByEid($encounter['eid']);
		}

		if($_SESSION['globals']['enable_encounter_dictation']){
			$encounter['speechdictation'][] = $this->getDictation($filters);
		}

		if($_SESSION['globals']['enable_encounter_hcfa']){
			$encounter['hcfaoptions'][] = $this->getHCFA($filters);
		}

		$encounter['services'] = $this->getEncounterServiceCodesByEid($encounter['eid']);

		unset($filters);
		return $encounter;
	}

	public function getEncounterSummary(stdClass $params) {
		$this->setEid($params->eid);
		$record = $this->getEncounter($params);
		$encounter = (array)$record['encounter'];
		$encounter['patient'] = $this->patient->getPatientDemographicDataByPid($encounter['pid']);
		if(!empty($e)){
			return [
				'success' => true,
				'encounter' => $e
			];
		} else {
			return [
				'success' => false,
				'error' => "Encounter ID $params->eid not found"
			];
		}
	}

	public function updateEncounterPriority($params) {
		$this->updateEncounter($params);
		$this->poolArea->updateCurrentPatientPoolAreaByPid([
			'eid' => $params->eid,
			'priority' => $params->priority
		], $params->pid);
	}

	/**
	 * @param stdClass $params
	 * @return array|mixed
	 */
	public function updateEncounter($params) {
		return $this->e->save($params);
	}

	/**
	 * @param stdClass $params
	 * @return array
	 */
	public function signEncounter(stdClass $params) {
		$this->setEid($params->eid);

		/** verify permissions (sign encounter and supervisor) */
		if(!ACL::hasPermission('sign_enc') || ($params->isSupervisor && !ACL::hasPermission('sign_enc_supervisor'))){
			return [
				'success' => false,
				'error' => 'access_denied'
			];
		}

		$user = new User();
		if($params->isSupervisor){
			if($params->supervisor_uid != $_SESSION['user']['id']){
				unset($user);
				return [
					'success' => false,
					'error' => 'supervisor_does_not_match_user'
				];
			}
			if(!$user->verifyUserPass($params->signature, $params->supervisor_uid)){
				unset($user);
				return [
					'success' => false,
					'error' => 'incorrect_password'
				];
			}
		} else {
			if(!$user->verifyUserPass($params->signature)){
				unset($user);
				return [
					'success' => false,
					'error' => 'incorrect_password'
				];
			}
		}
		unset($user);

		if($params->isSupervisor){
			$params->close_date = date('Y-m-d H:i:s');
		} else {
			$params->provider_uid = $_SESSION['user']['id'];
			if(!ACL::hasPermission('require_enc_supervisor'))
				$params->close_date = date('Y-m-d H:i:s');
		}

		$data = $this->updateEncounter($params);
		return [
			'success' => true,
			'data' => $data
		];

	}

	/**
	 * @param $eid
	 * @return array
	 */
	public function getSoapByEid($eid) {
		$filters = new stdClass();
		$filters->filter[0] = new stdClass();
		$filters->filter[0]->property = 'eid';
		$filters->filter[0]->value = $eid;
		$soap = $this->getSoap($filters);
		$soap['dxCodes'] = $this->getEncounterDxs($filters);
		return $soap;
	}

	/**
	 * @param stdClass $params
	 * @return array
	 */
	public function getSoapHistory($params) {
		$filters = new stdClass();
		$filters->filter[0] = new stdClass();
		$filters->filter[0]->property = 'eid';
		$filters->filter[0]->operator = '!=';
		$filters->filter[0]->value = $params->eid;
		$filters->filter[1] = new stdClass();
		$filters->filter[1]->property = 'pid';
		$filters->filter[1]->operator = '=';
		$filters->filter[1]->value = $params->pid;

		$filters->sort[0] = new stdClass();
		$filters->sort[0]->property = 'service_date';
		$filters->sort[0]->direction = 'DESC';

		$encounters = $this->getEncounters($filters);

		unset($filters->sort[0], $filters->sort);

		// switch the operator to =
		$filters->filter[0]->operator = '=';
		// remove the pid filter we don't need it
		unset($filters->filter[1]);

		foreach($encounters AS $i => &$encounter){
			$filters->filter[0]->value = $encounter['eid'];
			$soap = $this->getSoap($filters);
			$encounter['service_date'] = date($_SESSION['globals']['date_time_display_format'], strtotime($encounter['service_date']));
			$icds = '';
			foreach($this->diagnosis->getICDByEid($encounter['eid'], true) as $code){
				$icds .= '<li><span style="font-weight:bold; text-decoration:none">' . $code['code'] . '</span> - ' . $code['long_desc'] . '</li>';
			}
			$encounter['subjective'] = $soap['subjective'];
			$encounter['objective'] = $soap['objective'] . $this->getObjectiveExtraDataByEid($encounter['eid']);
			$encounter['assessment'] = $soap['assessment'] . '<ul  class="ProgressNote-ul">' . $icds . '</ul>';
			$encounter['plan'] = $soap['plan'] . $this->getPlanExtraDataByEid($encounter['eid']);
			unset($soap);
		}
		unset($filters);
		return $encounters;
	}

	/**
	 * TODO: get all codes CPT/CVX/HCPCS/ICD9/ICD10 encounter
	 * @param $params
	 * @return array
	 */
	public function getEncounterCodes($params) {
		if(isset($params->eid))
			return $this->getEncounterServiceCodesByEid($params->eid);
		return [];
	}

	public function getEncounterServiceCodesByEid($eid) {
		$Services = new Services();
		$records = $Services->getEncounterServicesByEid($eid);
		unset($Services);
		return $records;
	}

	/**
	 * @param $eid
	 * @return array
	 */
	public function getDictationByEid($eid) {
		$sth = $this->conn->prepare("SELECT * FROM `encounter_dictation` WHERE eid = ? ORDER BY `date` DESC");
		$sth->execute([$eid]);
		return $sth->fetch(PDO::FETCH_ASSOC);
	}

	/**
	 * @param $eid
	 * @return array
	 *  Naming: "closePatientEncounter"
	 */
	public function getProgressNoteByEid($eid) {

		$record = $this->getEncounter($eid, true, false);
		$encounter = (array)$record['encounter'];
		$user = new User();
		$encounter['service_date'] = date('F j, Y, g:i a', strtotime($encounter['service_date']));
		$encounter['patient_name'] = $this->patient->getPatientFullNameByPid($encounter['pid']);
		$encounter['open_by'] = $user->getUserNameById($encounter['open_uid']);
		$encounter['signed_by'] = $user->getUserNameById($encounter['provider_uid']);
		unset($user);

		/**
		 * Add vitals to progress note
		 */
		unset($encounter['vitals']);
//		if($_SESSION['globals']['enable_encounter_vitals']){
//			if(count($encounter['vitals']) == 0)
//				unset($encounter['vitals']);
//		}

		/**
		 * Add Review of Systems to progress note
		 */
		if($_SESSION['globals']['enable_encounter_review_of_systems']){
			$foo = [];
			foreach($encounter['reviewofsystems'] as $key => $value){
				if($key != 'id' && $key != 'pid' && $key != 'eid' && $key != 'uid' && $key != 'date'){
					if($value != null && $value != 'null'){
						$value = ($value == 1 || $value == '1') ? 'Yes' : 'No';
						$foo[] = [
							'name' => $key,
							'value' => $value
						];
					}
				}
			}
			if(!empty($foo)){
				$encounter['reviewofsystems'] = $foo;
			}
		}

		/**
		 * Add SOAP to progress note
		 */
		if($_SESSION['globals']['enable_encounter_soap']){
			$dxCodes = $this->diagnosis->getICDByEid($eid, true);

			if(count($dxCodes) > 0){
				$dxOl = '';
				$dxGroups = [];
				foreach($dxCodes as $dxCode){
					$dxGroups[$dxCode['dx_group']][] = $dxCode;
				}

				foreach($dxGroups as $dxGroup){
					$dxOl .= '<p>Dx Group ' . $dxGroup[0]['dx_group'] . '</p>';
					$dxOl .= '<ol  class="ProgressNote-ol">';
					foreach($dxGroup as $dxCode){
						$dxOl .= '<li><span style="font-weight:bold; text-decoration:none">' . $dxCode['code'] . '</span> - ' . $dxCode['long_desc'] . '</li>';
					}
					$dxOl .= '</ol>';
				}
			}

			$soap = $this->getSoapByEid($eid);
			$soap['assessment'] = isset($soap['assessment']) ? $soap['assessment'] : '';
			$soap['objective'] = (isset($soap['objective']) ? $soap['objective'] : '') . $this->getObjectiveExtraDataByEid($eid, $encounter);
			$soap['assessment'] = $soap['assessment'] . (isset($dxOl) ? $dxOl : '');
			$soap['plan'] = (isset($soap['plan']) ? $soap['plan'] : '') . $this->getPlanExtraDataByEid($eid);
			$encounter['soap'] = $soap;
		}

		/**
		 * Add Dictation to progress note
		 */
		if($_SESSION['globals']['enable_encounter_dictation']){
			$speech = $this->getDictationByEid($eid);
			if($speech['dictation']){
				$encounter['speechdictation'] = $speech;
			}
		}

		return $encounter;
	}

	private function getObjectiveExtraDataByEid($eid, $encounter = null) {

		if(!isset($encounter)){
			$record = $this->getEncounter($eid, true, false);
			$encounter = (array)$record['encounter'];
		}

		$str_buff = '';
		$Vitals = new Vitals();
		$vitals = $Vitals->getVitalsByEid($eid);

		$str_buff .= '<div class="indent">';
		if(!empty($vitals)){
			$str_buff .= '<p><b>Vitals:</b></p>';
			$vitals_buff = '';
			foreach($vitals as $foo){

				$vitals_buff .= '<p class="indent">';

				if(isset($foo['date'])){
					$date = strtotime($foo['date']);
					$vitals_buff .= '<u>Date:</u> ' . date($_SESSION['globals']['date_time_display_format'], $date) . '<br>';
				}

				/**
				 * Blood Pressure
				 */
				if(isset($foo['bp_systolic']) || isset($foo['bp_diastolic'])){
					$buff = [];
					$buff[0] = isset($foo['bp_systolic']) ? $foo['bp_systolic'] : '';
					$buff[1] = isset($foo['bp_diastolic']) ? $foo['bp_diastolic'] : '';
					$vitals_buff .= '<u>BP:</u> ' . implode('/', $buff) . '<br>';
					unset($buff);
				}

				$is_metric = $_SESSION['globals']['units_of_measurement'] == 'metric';

				/**
				 * Temperature
				 */
				if($is_metric){
					if(isset($foo['temp_c']) && $foo['temp_c'] != ''){
						$vitals_buff .= '<u>Temp:</u> ' . $foo['temp_c'] . ' &deg;C<br>';
					}
				}else{
					if(isset($foo['temp_f']) && $foo['temp_f'] != ''){
						$vitals_buff .= '<u>Temp:</u> ' . $foo['temp_f'] . ' &deg;F<br>';
					}
				}

				if(isset($foo['temp_location']) && $foo['temp_location'] != ''){
					$vitals_buff .= '<u>Temp Loc:</u> ' . $foo['temp_location'] . '<br>';
				}

				/**
				 * Height
				 */
				if($is_metric){
					if(isset($foo['height_cm']) && $foo['height_cm'] != ''){
						$vitals_buff .= '<u>Height:</u> ' . $foo['height_cm'] . 'cm<br>';
					}
				}else{
					if(isset($foo['height_in']) && $foo['height_in'] != ''){
						$vitals_buff .= '<u>Height:</u> ' . $foo['height_in'] . '"<br>';
					}
				}

				/**
				 * Weight
				 */
				if($is_metric){
					if(isset($foo['weight_kg']) && $foo['weight_kg'] != ''){
						$vitals_buff .= '<u>Weight:</u> ' . $foo['weight_lbs'] . 'Kg<br>';
					}
				}else{
					if(isset($foo['weight_lbs']) && $foo['weight_lbs'] != ''){
						$vitals_buff .= '<u>Weight:</u> ' . $foo['weight_lbs'] . 'Lbs<br>';
					}
				}

				/**
				 * BMI
				 */
				if(isset($foo['bmi']) && $foo['bmi'] != ''){
					$vitals_buff .= '<u>BMI: ' . $foo['bmi'] . '<br>';
				}
				if(isset($foo['bmi_status']) && $foo['bmi_status'] != ''){
					$vitals_buff .= '<u>BMI Status:</u> ' . $foo['bmi_status'] . '<br>';
				}

				/**
				 * Pulse
				 */
				if(isset($foo['pulse']) && $foo['pulse'] != ''){
					$vitals_buff .= '<u>Pulse:</u> ' . $foo['pulse'] . '<br>';
				}

				/**
				 * Administer Name
				 */
				if(isset($foo['administer_by']) && $foo['administer_by'] != ''){
					$vitals_buff .= '<u>Administer By:</u> ' .$foo['administer_by'];
				}

				$vitals_buff .= '</p>';


			}

			$str_buff .= $vitals_buff;

		}else{
			$str_buff .= '<p>Vitals: No Vitals Recorded</p>';
		}
		$str_buff .= '</div>';


		if(
			isset($encounter['reviewofsystems']) &&
			count($encounter['reviewofsystems']) > 0 &&
			is_array($encounter['reviewofsystems'][0])
		){

			$conn = Matcha::getConn();
			$sql = 'SELECT o.options FROM forms_fields as f INNER JOIN forms_field_options as o ON o.field_id = f.id WHERE f.form_id = 8;';
			$sth = $conn->prepare($sql);
			$sth->execute();
			$options = $sth->fetchAll(PDO::FETCH_ASSOC);
			$fields = [];

			foreach($options as $option){
				$buff = json_decode($option['options'], true);
				if(!isset($buff['name'])) continue;
				$fields[$buff['name']] = $buff['fieldLabel'];
			}

			$str_buff .= '<div class="indent">';
			$str_buff .= '<p><b>Review of Systems:</b></p>';
			$str_buff .= '<div class="indent">';
			foreach($encounter['reviewofsystems'][0] as $key => $value){
				if(!array_key_exists($key, $fields)) continue;
				if(!isset($value)) continue;
				$str_buff .= $fields[$key] . ': ' . ($value == 1 ? 'Yes' : $value) .'<br>';
			}

			$str_buff .= '</div>';
			$str_buff .= '</div>';
		}

		/**
		 * Active Medications
		 */
		if($encounter['review_medications']){
			$ActiveMedications = new Medications();
			$active_medications = $ActiveMedications->getPatientActiveMedicationsByPid($eid);
			$str_buff .= '<div class="indent">';
			if(!empty($active_medications)){
				$lis = '';
				foreach($active_medications as $foo){
					$lis .= '<li>' . $foo['STR'] . '</li>';
				}
				$str_buff .= '<p><b>Active Medications:</b></p>';
				$str_buff .= '<ul class="ProgressNote-ul">' . $lis . '</ul>';
			} else {
				$str_buff .= '<p><b>Active Medications:</b> No Active Medications</p>';
			}
			$str_buff .= '</div>';
			unset($ActiveMedications, $active_medications);
		}

		/**
		 * Active Problems found in this Encounter
		 */
		$ActiveProblems = new ActiveProblems();
		$active_problems = $ActiveProblems->getPatientActiveProblemByEid($eid);
		$str_buff .= '<div class="indent">';
		if(!empty($active_problems)){
			$lis = '';
			foreach($active_problems as $foo){
				$lis .= '<li>[' . $foo['code'] . '] - ' . $foo['code_text'] . ' </li>';
			}
			$str_buff .= '<p><b>Active Problems:</b></p>';
			$str_buff .= '<ul class="ProgressNote-ul">' . $lis . '</ul>';
		}else{
			if($encounter['review_active_problems']){
				$str_buff .= '<p><b>Active Problems:</b> No Active Problems</p>';
			}
		}
		$str_buff .= '</div>';
		unset($ActiveProblems, $active_problems);

		/**
		 * Allergies
		 */
		$Allergies = new Allergies();
		$allergies = $Allergies->getPatientAllergiesByEid($eid);
		$str_buff .= '<div class="indent">';
		if(!empty($allergies)){
			$lis = '';
			foreach($allergies as $foo){
				$lis .= '<li>Allergy: ' . $foo['allergy'] . ' (' . $foo['allergy_type'] . ')<br>';
				$lis .= 'Reaction: ' . $foo['reaction'] . '<br>';
				$lis .= 'Severity: ' . $foo['severity'] . '<br>';
				$lis .= 'Location: ' . $foo['location'] . '<br>';
				$lis .= 'Active?: ' . ($foo['end_date'] != null ? 'Yes' : 'No') . '</li>';
			}
			$str_buff .= '<p><b>Allergies:</b></p>';
			$str_buff .= '<ul class="ProgressNote-ul">' . $lis . '</ul>';
		}else{
			if($encounter['review_allergies']){
				$str_buff .= '<p><b>Allergies:</b> No Known Allergies</p>';
			}
		}
		$str_buff .= '</div>';
		unset($Allergies, $allergies);

		/**
		 * Immunizations ????
		 */
		if($encounter['review_immunizations']){
			$Immunizations = new Immunizations();
			$immunizations = $Immunizations->getImmunizationsByEncounterID($eid);
			$str_buff .= '<div class="indent">';
			if(!empty($immunizations)){
				$lis = '';
				foreach($immunizations as $foo){
					$administered_by = Person::fullname($foo['administered_fname'], $foo['administered_mname'], $foo['administered_lname']);

					$lis .= '<li>Vaccine name: ' . $foo['vaccine_name'] . '<br>';
					$lis .= 'Vaccine ID: (' . $foo['code_type'] . ')' . $foo['code'] . '<br>';
					$lis .= 'Manufacturer: ' . $foo['manufacturer'] . '<br>';
					$lis .= 'Lot Number: ' . $foo['lot_number'] . '<br>';
					$lis .= 'Dose: ' . $foo['administer_amount'] . ' ' . $foo['administer_units'] . '<br>';
					$lis .= 'Administered By: ' . $administered_by . '</li>';
				}
				$str_buff .= '<p><b>Immunizations:</b></p>';
				$str_buff .= '<ul class="ProgressNote-ul">' . $lis . '</ul>';
			} else {
				$str_buff .= '<p><b>Immunizations:</b> No Immunizations</p>';
			}
			$str_buff .= '</div>';
			unset($Immunizations, $immunizations);
		}



		return $str_buff;
	}

	private function getPlanExtraDataByEid($eid){

//		$record = $this->getEncounter($eid, true, false);
//		$encounter = (array)$record['encounter'];

		$str_buff = '';

		/**
		 * Active Medications
		 */

			$Medications = new Medications();
			$medications = $Medications->getPatientMedicationsOrdersByEid($eid);

			if(!empty($medications)){
				$str_buff .= '<div class="indent">';
				$str_buff .= '<p><b>Medications Orders:</b></p>';

				foreach($medications as $foo){
					$str_buff .= '<p class="indent">';
					$str_buff .= '<u>Medication: </u>' . $foo['STR'] . '<br>';
					$str_buff .= '<u>Dispense: </u>' . $foo['dispense'] . '<br>';
					$str_buff .= '<u>Refill: </u>' . $foo['refill'] . '<br>';
					$str_buff .= '<u>Instruction: </u>' . $foo['directions'] . '<br>';
					$str_buff .= '<u>Notes To Pharmacist: </u>' . $foo['notes'] . '<br>';
					$str_buff .= '</p>';
				}
				$str_buff .= '</div>';
			}

			unset($ActiveMedications, $active_medications);



		return $str_buff;
	}

	public function checkoutAlerts(stdClass $params) {
		$alerts = [];
		$records = $this->e->load(['eid' => $params->eid], [
				'review_immunizations',
				'review_allergies',
				'review_active_problems',
				'review_medications',
				'review_alcohol',
				'review_smoke',
				'review_pregnant'
		])->one();
		foreach($records as $key => $rec){
			if($rec != 0 && $rec != null){
				unset($records[$key]);
			}
		}
		foreach($records as $key => $rec){
			$foo = [];
			$foo['alert'] = 'Need to ' . str_replace('_', ' ', $key) . ' area';
			$foo['alertType'] = 1;
			$alerts[] = $foo;
		}
		//TODO: vitals check
		return $alerts;
	}

	/**
	 * @param $date
	 * @return mixed
	 */
	public function parseDate($date) {
		return str_replace('T', ' ', $date);
	}

	public function checkForAnOpenedEncounterByPid(stdClass $params) {
		$date = strtotime('-1 day', strtotime($params->date));
		$date = date('Y-m-d H:i:s', $date);
		$sql = "SELECT * FROM `encounters` WHERE (pid= ? AND close_date IS NULL) AND service_date >= ?";
		$sth = $this->conn->prepare($sql);
		$sth->execute([
			$params->pid,
			$date
		]);
		$data = $sth->fetch(PDO::FETCH_ASSOC);
		if(isset($data['eid'])){
			return true;
		} else {
			return false;
		}

	}

	public function getEncounterFollowUpInfoByEid($eid) {
		$sql = "SELECT followup_time, followup_facility FROM encounters WHERE eid = ?";
		$sth = $this->conn->prepare($sql);
		$sth->execute([$eid]);
		$rec = $sth->fetch(PDO::FETCH_ASSOC);
		if($rec !== false){
			$rec['followup_facility'] = intval($rec['followup_facility']);
		}
		return $rec;
	}

	public function getEncounterMessageByEid($eid) {
		$sql = "SELECT message FROM encounters WHERE eid = ?";
		$sth = $this->conn->prepare($sql);
		$sth->execute([$eid]);
		return $sth->fetch(PDO::FETCH_ASSOC);
	}

	public function getEncounterByDateFromToAndPatient($from, $to, $pid = null) {
		$sql = " SELECT encounters.pid,
	                    encounters.eid,
	                    encounters.service_date,
	                    patient.*
	               FROM encounters
	          LEFT JOIN patient ON encounters.pid = patient.pid
	              WHERE encounters.service_date BETWEEN '$from 00:00:00' AND '$to 23:59:59'";
		if(isset($pid) && $pid != ''){
			$sql .= " AND encounters.pid = '$pid'";
		}
		$sth = $this->conn->prepare($sql);
		$sth->execute();
		return $sth->fetchAll(PDO::FETCH_ASSOC);

	}

	public function getEncountersByDate($date) {
		$filters = new stdClass();

		$filters->filter[0] = new stdClass();
		$filters->filter[0]->property = 'service_date';
		$filters->filter[0]->operator = '>=';
		$filters->filter[0]->value = $date . ' 00:00:00';

		$filters->filter[1] = new stdClass();
		$filters->filter[1]->property = 'service_date';
		$filters->filter[1]->operator = '<=';
		$filters->filter[1]->value = $date . ' 00:00:00';

		return $this->getEncounters($filters, false);
	}

	public function getTodayEncounters() {
		return $this->getEncountersByDate(date('Y-m-d'));
	}

	public function getReviewOfSystems($params) {
		return $this->ros->load($params)->one();
	}

	public function addReviewOfSystems($params) {
		return $this->ros->save($params);
	}

	public function updateReviewOfSystems($params) {
		return $this->ros->save($params);
	}

	public function getSoap($params) {
		return $this->soap->load($params)->one();
	}

	public function addSoap($params) {
		return $this->soap->save($params);
	}

	public function updateSoap($params) {
		return $this->soap->save($params);
	}

	public function getDictation($params) {
		return $this->d->load($params)->one();
	}

	public function addDictation($params) {
		return $this->d->save($params);
	}

	public function updateDictation($params) {
		return $this->d->save($params);
	}

	public function getHCFAs($params) {
		return $this->hcfa->load($params)->all();
	}

	public function getHCFA($params) {
		return $this->hcfa->load($params)->one();
	}

	public function addHCFA($params) {
		return $this->hcfa->save($params);
	}

	public function updateHCFA($params) {
		return $this->hcfa->save($params);
	}

	public function getEncounterDxs($params = null) {
		$records = $this->edx->load($params)->all();
		foreach($records as $i => $record){
			if($record !== false){
				$code = $this->diagnosis->getICDDataByCode($record['code'], $record['code_type']);
				if(is_array($code))
					$records[$i] = array_merge($records[$i], $code);
			}
		}
		return $records;
	}

	public function getEncounterDx($params) {
		$record = $this->edx->load($params)->one();
		if($record !== false){
			$code = $this->diagnosis->getICDDataByCode($record['code'], $record['code_type']);
			if(is_array($code))
				$record = array_merge($record, $code);
		}
		return $record;
	}

	public function createEncounterDx($params) {
		return $this->edx->save($params);
	}

	public function updateEncounterDx($params) {
		return $this->edx->save($params);
	}

	public function destroyEncounterDx($params) {
		return $this->edx->destroy($params);
	}

	/**
	 * @param $eid
	 * @return array
	 */
	public function getEncounterPrintDocumentsByEid($eid) {
		$sql = 'SELECT `id` AS record_id, \'rx\' AS document_type, `STR` AS description  FROM `patient_medications` WHERE `eid` = ?';
		$sql .= ' UNION ';
		$sql .= 'SELECT `id` AS record_id, `order_type` AS document_type, `description` FROM `patient_orders` WHERE `eid` = ?';
		$sql .= ' UNION ';
		$sql .= 'SELECT `id` AS record_id, \'note\' AS document_type, \'Doctors Note\' AS description FROM `patient_doctors_notes` WHERE `eid` = ?';
		$sql .= ' UNION ';
		$sql .= 'SELECT `id` AS record_id, \'referral\' AS document_type, \'Referral\' AS description FROM `patient_referrals` WHERE `eid` = ?';
		$sth = $this->conn->prepare($sql);
		$sth->execute([
			$eid,
			$eid,
			$eid,
			$eid,
		]);
		return $sth->fetchAll(PDO::FETCH_ASSOC);
	}
}
