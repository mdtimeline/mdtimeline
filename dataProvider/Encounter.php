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
include_once(ROOT . '/dataProvider/Orders.php');
include_once(ROOT . '/dataProvider/Referrals.php');
include_once(ROOT . '/dataProvider/ActiveProblems.php');
include_once(ROOT . '/dataProvider/Immunizations.php');
include_once(ROOT . '/dataProvider/Procedures.php');
include_once(ROOT . '/dataProvider/Services.php');
include_once(ROOT . '/dataProvider/DiagnosisCodes.php');
include_once(ROOT . '/dataProvider/FamilyHistory.php');
include_once(ROOT . '/dataProvider/NursesNotes.php');
include_once(ROOT . '/dataProvider/EncounterAddenda.php');
include_once(ROOT . '/dataProvider/PhysicalExams.php');

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
	private $ross;
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

	/**
	 * @var bool|MatchaCUP
	 */
	private $phye;

	/**
	 * @var FamilyHistory
	 */
	private $FamilyHistory;

	/**
	 * @var EncounterAddenda
	 */
	private $EncounterAddenda;

	function __construct() {
		$this->conn = Matcha::getConn();
		$this->patient = new Patient();
		$this->poolArea = new PoolArea();
		$this->diagnosis = new DiagnosisCodes();
		$this->FamilyHistory = new FamilyHistory();
		$this->EncounterAddenda = new EncounterAddenda();

        if(!isset($this->e)) $this->e = MatchaModel::setSenchaModel('App.model.patient.Encounter');
        if(!isset($this->ros)) $this->ros = MatchaModel::setSenchaModel('App.model.patient.ReviewOfSystems');
        if(!isset($this->ross)) $this->ross = MatchaModel::setSenchaModel('App.model.administration.ReviewOfSystemSettings');
        if(!isset($this->soap)) $this->soap = MatchaModel::setSenchaModel('App.model.patient.SOAP');
        if(!isset($this->d)) $this->d = MatchaModel::setSenchaModel('App.model.patient.Dictation');
        if(!isset($this->hcfa)) $this->hcfa = MatchaModel::setSenchaModel('App.model.patient.HCFAOptions');
        if(!isset($this->edx)) $this->edx = MatchaModel::setSenchaModel('App.model.patient.EncounterDx');
        if(!isset($this->edx)) $this->edx = MatchaModel::setSenchaModel('App.model.patient.EncounterDx');
        if(!isset($this->phye)) $this->phye = MatchaModel::setSenchaModel('App.model.patient.PhysicalExam');
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
		$records = $this->e->load($params)
            ->leftJoin(
                    [
                    'title' => 'provider_title',
                    'fname' => 'provider_fname',
                    'mname' => 'provider_mname',
                    'lname' => 'provider_lname',
                    'npi' => 'provider_npi',
                    'signature' => 'provider_signature',
                ], 'users', 'provider_uid', 'id')
            ->leftJoin(
                [
                    'name' => 'facility_name'
                ], 'facility', 'facility', 'id')
            ->all();
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

		return $this->e->load()->all();
	}
	/**
	 * @param null $start
	 * @param null $end
	 * @param bool $group_by_pid
	 * @return mixed
	 */
	public function getEncountersByDates($start = null, $end = null, $group_by_pid = false) {

		if(isset($start)){
			$this->e->addFilter('service_date', $start, '>=');
		}
		if(isset($end)) {
			$this->e->addFilter('service_date', $end, '<=');
		}

		if($group_by_pid){
		    $group = new stdClass();
		    $group->property = 'pid';
		    $group->direction = '';
            $this->e->group((object)['group' => [$group]]);
        }

		return $this->e->load()->all();
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
	 *
	 * @return array|mixed
	 */
	public function getEncounter($params, $relations = true) {

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
		$encounter = $this->getEncounterRelations($encounter);

		unset($filters);
		return ['encounter' => $encounter];
	}

	private function getEncounterRelations($encounter) {
		$filters = new stdClass();
		$filters->filter[0] = new stdClass();
		$filters->filter[0]->property = 'eid';
		$filters->filter[0]->value = $encounter['eid'];

		$Vitals = new Vitals();
		if($_SESSION['globals']['enable_encounter_vitals']){
			$encounter['vitals'] = $Vitals->getVitalsByEid($encounter['eid']);
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

		if($_SESSION['globals']['enable_encounter_hcfa']){
			$encounter['physicalexams'][] = $this->getPhysicalExams($filters);
		}

		$encounter['services'] = $this->getEncounterServiceCodesByEid($encounter['eid']);

		unset($filters);
		return $encounter;
	}

	public function getEncounterSummary(stdClass $params) {
		$this->setEid($params->eid);
		$record = $this->getEncounter($params, false);
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
		$filters->filter[0]->value = isset($params->eid) ? $params->eid : '0' ;
		$filters->filter[1] = new stdClass();
		$filters->filter[1]->property = 'pid';
		$filters->filter[1]->operator = '=';
		$filters->filter[1]->value = isset($params->pid) ? $params->pid : '0';

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
				$icds .= '<li><span style="font-weight:bold; text-decoration:none">' .
                    $code['code'] . '</span> - ' .
                    (isset($code['long_desc']) ? $code['long_desc'] : $code['code_text']) .
                    '</li>';
			}
			$encounter['subjective'] = $soap['subjective'] . $this->getSubjectiveExtraDataByEid($encounter['eid']);
			$encounter['objective'] = $this->getObjectiveExtraDataByEid($encounter['eid']) . $soap['objective'];
			$encounter['assessment'] = $soap['assessment'] . '<ul  class="ProgressNote-ul">' . $icds . '</ul>';
			$encounter['plan'] = (isset($soap['plan']) ? $soap['plan'] : '') . $this->getPlanExtraDataByEid($encounter['eid'], $soap['instructions'], true);
			$encounter['addenda'] = $this->getEncounterAddendaByEid($encounter['eid']);
			$encounter['leaf'] = true;
			unset($soap);
		}
		unset($filters);

		return $encounters;

		$encounters_children = [];

		foreach ($encounters as $encounter) {
			$encounter['leaf'] = true;
			if(!isset($encounter['parent_eid']) || $encounter['parent_eid'] == 0){
				$encounters_children[] = $encounter;
			}else{
				$this->encounterGridHandler($encounters_children, $encounter);;
			}
		}

		return $encounters_children;
	}

	private function getEncounterAddendaByEid($eid){
	    $addenda = $this->EncounterAddenda->getEncounterAddendaByEid($eid);
        $buff = '';

	    if(count($addenda) === 0){
	        return 'NONE';
        }

        foreach($addenda as $addendum){

            $buff .= '<ul style="list-style-type:disc; margin-left: 20px">';
            $buff .= '<li><span style="font-weight:bold; text-decoration:none">Date: </span> ' . date('F j, Y, g:i a', strtotime($addendum['create_date'])) . '</li>';
            $buff .= '<li><span style="font-weight:bold; text-decoration:none">Source: </span> ' . ucfirst($addendum['source']) . '</li>';
            $buff .= '<li><span style="font-weight:bold; text-decoration:none">Notes: </span> ' . trim($addendum['notes']) . '</li>';
            $buff .= '<li><span style="font-weight:bold; text-decoration:none">By: </span> ' . sprintf('%s, %s %s', $addendum['created_by_lname'],$addendum['created_by_fname'],$addendum['created_by_mname']) . '</li>';
            $buff .= '</ul>';
        }

        return $buff;

    }

	private function encounterGridHandler(&$encounters_children, $encounter){

		foreach ($encounters_children as &$encounters_tree_child){

			if($encounters_tree_child['eid'] === $encounter['parent_eid']){
				$encounters_tree_child['leaf'] = false;
				$encounters_tree_child['expanded'] = true;
				$encounters_tree_child['children'][] = $encounter;
				break;
			}

			if(isset($encounters_tree_child['children'])){
				$this->encounterGridHandler($encounters_tree_child['children'], $encounter);
			}
		}

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
	 * @param $include_nurse_data
	 * @return array
	 *  Naming: "closePatientEncounter"
	 */
	public function getProgressNoteByEid($eid, $include_nurse_data = true) {

		$record = $this->getEncounter($eid, true);
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
				$dxOl = '<p>Diagnosis</p>';
				$dxGroups = [];
				foreach($dxCodes as $dxCode){
					$dxGroups[$dxCode['dx_group']][] = $dxCode;
				}

				foreach($dxGroups as $dxGroup){
					$dxOl .= '<ol  class="ProgressNote-ol">';
					foreach($dxGroup as $dxCode){
						$dxOl .= '<li><span style="font-weight:bold; text-decoration:none">' . $dxCode['code'] . '</span> - ' . trim($dxCode['long_desc']) . '</li>';
					}
					$dxOl .= '</ol>';
				}
			}

			$soap = $this->getSoapByEid($eid);
			$soap['subjective'] = (isset($soap['subjective']) ? (trim($soap['subjective']) . '<br>') : '') . $this->getSubjectiveExtraDataByEid($eid, $encounter);
			$soap['assessment'] = (isset($soap['assessment']) ? (trim($soap['assessment']) . '<br>') : '');
			$soap['objective'] = $this->getObjectiveExtraDataByEid($eid, $encounter) . (isset($soap['objective']) ? trim($soap['objective']) : '');
			$soap['assessment'] = $soap['assessment'] . (isset($dxOl) ? $dxOl : '<br>');
			$instructions = (isset($soap['instructions']) ? (trim($soap['instructions'])) : null);
			$soap['plan'] = (isset($soap['plan']) ? trim($soap['plan']) : '') . $this->getPlanExtraDataByEid($eid, $instructions, $include_nurse_data);
			$encounter['soap'] = $soap;
		}

        $encounter['addenda'] = $this->getEncounterAddendaByEid($eid);

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

	public function ProgressNoteString($encounter){

		$output = '';
		$br = '<br>';

		if(isset($encounter['brief_description']) && $encounter['brief_description'] != ''){
			$output .= '<p><b>CHIEF COMPLAINT:</b></p>' . nl2br(trim($encounter['brief_description'])) . $br. $br;
		}

		if(isset($encounter['soap'])){
			$soap = $encounter['soap'];

			if(isset($soap['subjective']) && $soap['subjective'] != ''){
				$output .= '<p><b>SUBJECTIVE:</b></p>' . nl2br(trim($soap['subjective'])) ;
			}
			if(isset($soap['objective']) && $soap['objective'] != ''){
				$output .= '<p><b>OBJECTIVE:</b></p>' . nl2br(trim($soap['objective'])) . $br. $br;
			}
			if(isset($soap['assessment']) && $soap['assessment'] != ''){
				$output .= '<p><b>ASSESSMENT:</b></p>' . nl2br(trim($soap['assessment']));
			}
//			if(isset($soap['plan']) && $soap['plan'] != ''){
//				$output .= 'PLAN: ' . $soap['plan'] . $br . $br;
//			}
			if(isset($soap['plan']) && $soap['plan'] != ''){
				$output .= '<p><b>PLAN:</b></p>'  . nl2br(trim($soap['plan']));
			}
			unset($soap);
		}

        if(isset($encounter['addenda']) && $encounter['addenda'] !== 'NONE'){
            $output .= '<p><b>ADDENDA:</b></p>'  . trim($encounter['addenda']);
        }

		//$output .= $br . '--- END OF REPORT ---';

		return $output;
	}

	private function getSubjectiveExtraDataByEid($eid, $encounter = null) {

		$str_buff = '';



		/**
		 * Review Of System
		 */
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

			$ros_reports_buff = [];
			$ros_denies_buff = [];
			$notes = false;

			foreach($encounter['reviewofsystems'][0] as $key => $value){
				if(!array_key_exists($key, $fields)) continue;
				if(!isset($value)) continue;

				if($key === 'notes'){
					$notes = $value;
				} elseif($value == 1){
					$ros_reports_buff[] = $fields[$key];
				}else{
					$ros_denies_buff[] = $fields[$key];
				}
			}

			if(!empty($ros_reports_buff) || !empty($ros_denies_buff)){
				$str_buff .= '<div class="indent">';
				$str_buff .= '<p><b>Review of Systems:</b></p>';
				$str_buff .= '<div class="indent">';
				if(!empty($ros_reports_buff)){
					$str_buff .= '<div><b>Patient Reports:</b> ' . implode(', ', $ros_reports_buff) . '</div>';
				}
				if(!empty($ros_denies_buff)){
					$str_buff .= '<div><b>Patient Denies:</b> ' . implode(', ', $ros_denies_buff) . '</div>';
				}
				if($notes !== false){
					$str_buff .= '<div><b>Notes:</b> ' . $notes . '</div>';
				}
				$str_buff .= '</div>';
				$str_buff .= '</div>';
			}
		}


		/**
		 * Active Medications
		 */
//		if($encounter['review_medications'] && isset($encounter)){
		if(isset($encounter)){
			$ActiveMedications = new Medications();
			$active_medications = $ActiveMedications->getPatientActiveMedicationsByPid($encounter['pid']);

			$str_buff .= '<div class="indent">';
			if(!empty($active_medications)){
				$lis = '';
				foreach($active_medications as $foo){
					$source = $foo['eid'] == $eid ? '*' : '';
					$lis .= "<li>{$source}" . $foo['STR'] . '</li>';
				}
				$str_buff .= '<p><b>Active Medications:</b>';

				if(!$encounter['review_medications']){
                    $str_buff .= ' (Not reviewed)';
                }

				$str_buff .= '</p>';


				$str_buff .= '<ul class="ProgressNote-ul">' . $lis . '</ul>';
			} else {
                if($encounter['review_medications']) {
                    $str_buff .= '<p><b>Active Medications:</b> Patient has no Active Medications</p>';
                }
                else
                {
                    $str_buff .= '<p><b>Active Medications:</b> (Not Reviewed)</p>';
                }
			}
			$str_buff .= '</div>';
			unset($ActiveMedications, $active_medications);
		}


		/**
		 * Advance Directives
		 */
        include_once (ROOT. '/dataProvider/AdvanceDirective.php');
		if(isset($encounter)){
			$AdvanceDirective = new AdvanceDirective();
			$advance_directives = $AdvanceDirective->getPatientAdvanceDirectivesByEid($eid);

			error_log(print_r($advance_directives,true));

			$str_buff .= '<div class="indent">';
			if(!empty($advance_directives)){
				$lis = '';
				foreach($advance_directives as $foo){
					$source = $foo['eid'] == $eid ? '*' : '';
					$lis .= "<li>{$source}" . $foo['code_text'] . ' - ' . $foo['status_code_text'] . '</li>';
				}
				$str_buff .= '<p><b>Advance Directive:</b>';

				if(!$encounter['review_advance_directives']){
                    $str_buff .= ' (Not reviewed)';
                }

				$str_buff .= '</p>';


				$str_buff .= '<ul class="ProgressNote-ul">' . $lis . '</ul>';
			} else {
                if($encounter['review_advance_directives']) {
                    $str_buff .= '<p><b>Advance Directive:</b> Patient has no Advance Directives</p>';
                }
                else{
                    $str_buff .= '<p><b>Advance Directive:</b> (Not Reviewed)</p>';
                }
			}
			$str_buff .= '</div>';
			unset($AdvanceDirective, $advance_directives);
		}

		/**
		 * Allergies
		 */
		$Allergies = new Allergies();

		if(isset($encounter)){
			$allergies = $Allergies->getPatientAllergiesByPid($encounter['pid']);
		}else{
			$allergies = $Allergies->getPatientAllergiesByEid($eid);
		}

		if(!empty($allergies)){

            $str_buff .= '<div class="indent">';
			$str_buff .= '<p><b>Allergies: </b>';

			if(isset($encounter) && !$encounter['review_allergies']){
                $str_buff .= ' (Not reviewed)';
            }

			$str_buff .= '</p>';
			$str_buff .= '<ul style="list-style-type:disc; margin-left: 20px">';
			foreach($allergies as $foo){
				$source = $foo['eid'] == $eid ? '*' : '';
				if($source !== '*' && $foo['status'] !== 'Active'){
					continue;
				}
				$str_buff .= '<li>';
				$str_buff .= "<u>{$source}" . $foo['allergy'] . ' (' . $foo['allergy_type'] . ')</u> - ';
				$str_buff .= '<u>Reaction:</u> ' . $foo['reaction'] . ' - ';
				$str_buff .= '<u>Severity:</u> ' . $foo['severity'] . ' - ';
				$str_buff .= '<u>Location:</u> ' . $foo['location'] . ' - ';
				$str_buff .= '<u>Status:</u> ' . (isset($foo['status']) ? $foo['status'] : 'Unknown');
				$str_buff .= '</li>';
			}
			$str_buff .= '</ul>';
            $str_buff .= '</div>';

		}else if(isset($encounter)){

			if($encounter['review_allergies']){
                $str_buff .= '<div class="indent">';
				$str_buff .= '<p><b>Allergies:</b> Patient has no Known Allergies</p>';
                $str_buff .= '</div>';
			}else
            {
                $str_buff .= '<div class="indent">';
                $str_buff .= '<p><b>Allergies:</b> (Not Reviewed)</p>';
                $str_buff .= '</div>';
            }
		}


		unset($Allergies, $allergies);


		/**
		 * Family History
		 */
		include_once (ROOT. '/dataProvider/FamilyHistory.php');
		$FamilyHistory = new FamilyHistory();

		if(isset($encounter)){
			$family_histories = $FamilyHistory->getFamilyHistoryByPid($encounter['pid']);
		}else{
			$family_histories = $FamilyHistory->getFamilyHistoryByEid($eid);
		}

		$str_buff .= '<div class="indent">';

		if(!empty($family_histories)){
			$str_buff .= '<p><b>Family History:</b></p>';
			$str_buff .= '<ul style="list-style-type:disc; margin-left: 20px">';
			$lu_buffs = [];

			foreach($family_histories as $family_history){

				$relation = (isset($family_history['relation']) ? $family_history['relation'] : '');
				$condition = (isset($family_history['condition']) ? $family_history['condition'] : '') ;
				$notes = (isset($family_history['notes']) && $family_history['notes'] != '' ? $family_history['notes'] : '') ;

				if(!isset($lu_buffs[$relation])){
					$lu_buffs[$relation] = [];
				}

				$source = $family_history['eid'] == $eid ? '*' : '';
				$_buff = '<li>';
				$_buff .= "<u>{$source}{$condition}</u>";

				if($notes !== ''){
					$_buff .= ' - Note: ' . $notes;
				}

				$_buff .= '</li>';

				$lu_buffs[$relation][] = $_buff;
			}

			foreach ($lu_buffs as $relation => $lu_buff){
				$str_buff .= '<li><u>' . $relation . ':</u></li>';
				$foo = implode('', $lu_buff);
				$str_buff .= '<ul style="list-style-type:disc; margin-left: 20px">' . $foo . '</ul>';
			}

			$str_buff .= '</ul>';

		}else{
			$str_buff .= '<p><b>Family History:</b> No Family History Recorded</p>';
		}

		$str_buff .= '</div>';
		unset($family_histories, $FamilyHistory);


		/**
		 * Active Problems found in this Encounter
		 */
		$ActiveProblems = new ActiveProblems();
		if(isset($encounter)){
			$active_problems = $ActiveProblems->getPatientActiveProblemByPid($encounter['pid']);
		}else{
			$active_problems = $ActiveProblems->getPatientActiveProblemByEid($eid);
		}

		if(!empty($active_problems)){
			$str_buff .= '<div class="indent">';
			$str_buff .= '<p><b>Active Problems:</b>';

			if(!$encounter['review_active_problems']){
                $str_buff .= ' (Not reviewed)';
            }

			$str_buff .= '</p>';
			$str_buff .= '<ul class="ProgressNote-ul">';
			foreach($active_problems as $foo){
				$source = $foo['eid'] == $eid ? '*' : '';
				$str_buff .= "<li>{$source}[" . $foo['code'] . '] - ' . $foo['code_text'] . '</li>';
			}
			$str_buff .= '</ul>';
			$str_buff .= '</div>';
		}else if(isset($encounter)){
		    if($encounter['review_active_problems']){
				$str_buff .= '<div class="indent">';
				$str_buff .= '<p><b>Active Problems:</b> Patient has no Active Problems</p>';
				$str_buff .= '</div>';
			}else{
                $str_buff .= '<div class="indent">';
                $str_buff .= '<p><b>Active Problems:</b> (Not Reviewed)</p>';
                $str_buff .= '</div>';
            }
		}

		unset($ActiveProblems, $active_problems);


		/**
		 * Social History
		 */
		include_once (ROOT. '/dataProvider/SocialHistory.php');
		$SocialHistory = new SocialHistory();
		$smoking_statuses = $SocialHistory->getSocialHistoryByEidAndCode($eid, 'smoking_status');
		$str_buff .= '<div class="indent">';

		if(!empty($smoking_statuses)){
			$str_buff .= '<p><b>Smoking Status:</b></p>';
			$str_buff .= '<ul style="list-style-type:disc; margin-left: 20px">';
			foreach($smoking_statuses as $smoking_status){

				if(isset($smoking_status['start_date'])){
					$from = date('F j, Y', strtotime($smoking_status['start_date']));
					if($from == false) $from = 'UNK';
				}else{
					$from ='UNK';
				}
				if(isset($smoking_status['end_date'])){
					$to = date('F j, Y', strtotime($smoking_status['end_date']));
					if($to == false) $to = 'UNK';
				}else{
					$to ='UNK';
				}

				$str_buff .= '<u>' . (isset($smoking_status['status']) ? $smoking_status['status'] : '') . '</u> - ';
				$str_buff .= '<u>Dates From/To:</u> ' . $from . ' / ' . $to . ' - ';
				$str_buff .= '<u>Counseling Given:</u> ' . (isset($smoking_status['counseling']) && $smoking_status['counseling'] ? 'Yes' : 'No') . ' - ';
				$str_buff .= '<u>Note:</u> ' . (isset($smoking_status['note']) ? $smoking_status['note'] : '');
			}
			$str_buff .= '</ul>';

		}else{
			$str_buff .= '<p><b>Smoking Status:</b> No Smoking Status Recorded</p>';
		}

		$str_buff .= '</div>';

		$social_history = $SocialHistory->getSocialHistoryByEidAndCode($eid, 'history');

		if(!empty($social_history)){
			$str_buff .= '<div class="indent">';
			$str_buff .= '<p><b>Social History:</b></p>';
			$str_buff .= '<ul style="list-style-type:disc; margin-left: 20px">';
			foreach ($social_history as $history){

				$str_buff .= '<li>';
				$str_buff .= '<u>Category:</u> ' . $history['category_code_text'] . ' - ';
				$str_buff .= '<u>Observation:</u> ' . $history['observation'] . ' - ';
				$str_buff .= '<u>Note:</u> ' . $history['note'];
				$str_buff .= '</li>';

			}
			$str_buff .= '</ul>';

			$str_buff .= '</div>';

		}

		return $str_buff;
	}

	private function getObjectiveExtraDataByEid($eid, $encounter = null) {

		if(!isset($encounter)){
			$record = $this->getEncounter($eid, true);
			$encounter = (array)$record['encounter'];
		}

		$str_buff = '';

		$Vitals = new Vitals();
		$vitals = $Vitals->getVitalsByEid($eid);

		$str_buff .= '<div class="indent">';

		/**
		 * Vitals
		 */
		if(!empty($vitals)){
			$str_buff .= '<p><b>Vitals:</b></p>';
			$vitals_buff = '';
			foreach($vitals as $foo){

				$vital_buff = [];
				$is_metric = $_SESSION['globals']['units_of_measurement'] == 'metric';


				if(isset($foo['date'])){
					$date = strtotime($foo['date']);
					$vital_buff[] = '<u>Date:</u> ' . date($_SESSION['globals']['date_time_display_format'], $date);
				}

				/**
				 * Weight
				 */
				if($is_metric){
					if(isset($foo['weight_kg']) && $foo['weight_kg'] != ''){
						$vital_buff[] = '<u>Weight:</u> ' . $foo['weight_lbs'] . 'Kg';
					}
				}else{
					if(isset($foo['weight_lbs']) && $foo['weight_lbs'] != ''){
						$vital_buff[] = '<u>Weight:</u> ' . $foo['weight_lbs'] . 'Lbs';
					}
				}

				/**
				 * Height
				 */
				if($is_metric){
					if(isset($foo['height_cm']) && $foo['height_cm'] != ''){
						$vital_buff[] = '<u>Height:</u> ' . $foo['height_cm'] . 'cm';
					}
				}else{
					if(isset($foo['height_in']) && $foo['height_in'] != ''){
						$vital_buff[] = '<u>Height:</u> ' . $foo['height_in'] . '"';
					}
				}

				/**
				 * Pulse
				 */
				if(isset($foo['pulse']) && $foo['pulse'] != ''){
					$vital_buff[] = '<u>Pulse:</u> ' . $foo['pulse'];
				}

				/**
				 * Blood Pressure
				 */
				if(isset($foo['bp_systolic']) || isset($foo['bp_diastolic'])){
					$buff = [];
					$buff[0] = isset($foo['bp_systolic']) ? $foo['bp_systolic'] : '';
					$buff[1] = isset($foo['bp_diastolic']) ? $foo['bp_diastolic'] : '';
					$vital_buff[] = '<u>BP:</u> ' . implode('/', $buff);
					unset($buff);
				}


				/**
				 * BMI
				 */
				if(isset($foo['bmi']) && $foo['bmi'] != ''){
					$vital_buff[] = '<u>BMI:</u> ' . $foo['bmi'];
				}
				if(isset($foo['bmi_status']) && $foo['bmi_status'] != ''){
					$vital_buff[] = '<u>BMI Status:</u> ' . $foo['bmi_status'];
				}

				/**
				 * Temperature
				 */
				if($is_metric){
					if(isset($foo['temp_c']) && $foo['temp_c'] != ''){
						$vital_buff[] = '<u>Temp:</u> ' . $foo['temp_c'] . ' &deg;C';
					}
				}else{
					if(isset($foo['temp_f']) && $foo['temp_f'] != ''){
						$vital_buff[] = '<u>Temp:</u> ' . $foo['temp_f'] . ' &deg;F';
					}
				}

				if(isset($foo['temp_location']) && $foo['temp_location'] != ''){
                    $vital_buff[] = '<u>Temp Loc:</u> ' . $foo['temp_location'];
				}


//				/**
//				 * Administer Name
//				 */
//				if(isset($foo['administer_by']) && $foo['administer_by'] != ''){
//					$vital_buff .= '<u>Administer By:</u> ' .$foo['administer_by'];
//				}

				if(!empty($vital_buff)){
					$vitals_buff .= '<div class="indent" style="margin-left: 20px;">'.implode('<br>', $vital_buff) . '</div>';
				}
			}

			$str_buff .= $vitals_buff;

		}else{
			$str_buff .= '<p><b>Vitals:</b> No Vitals Recorded</p>';
		}
		$str_buff .= '</div>';

        /**
         * Physical Exam
         */
        $PhysicalExams = new PhysicalExams();
        $physical_exams = $PhysicalExams->getPhysicalExamsByEid($eid);
        if(!empty($physical_exams)){

            $str_buff .= '<div class="indent">';
            $lis = '';
            foreach($physical_exams as $foo){

                $observations = (array)$foo['exam_data'];

                foreach ($observations as $observation){

                    $lis .= '<li>';
                    $lis .= '<u>' . (isset($observation->label) ? $observation->label : '')  . '</u> - ';
                    if(isset($observation->normal) && $observation->normal){
                        $lis .= 'Normal';
                    }else if(isset($observation->abnormal) && $observation->abnormal){
                        $lis .= 'Abnormal';
                    }else{
                        $lis .= 'Not Reported';
                    }

                    if(isset($observation->abnormal_note) && $observation->abnormal_note !== ''){
                        $lis .= ': ' . $observation->abnormal_note;
                    }

                    $lis .= '</li>';

                }

            }
            $str_buff .= '<p><b>Physical Exam:</b></p>';
            $str_buff .= '<ul style="list-style-type:disc; margin-left: 20px">' . $lis . '</ul>';
            $str_buff .= '</div>';

        }
        unset($ActiveMedications, $medications_ordered, $medications_administered);

		/**
		 * Immunizations
		 */
        $Immunizations = new Immunizations();
        $immunizations = $Immunizations->getImmunizationsByEncounterID($eid);

        if(!empty($immunizations)){
            $str_buff .= '<div class="indent">';
            $lis = '';
            foreach($immunizations as $foo){
                $administered_by = Person::fullname($foo['administered_fname'], $foo['administered_mname'], $foo['administered_lname']);

                $lis .= '<li><b>Vaccine name:</b> ' . $foo['vaccine_name'] . ' - ';
                $lis .= '<b>Vaccine ID:</b> (' . $foo['code_type'] . ')' . $foo['code'] . ' - ';
                $lis .= '<b>Manufacturer:</b> ' . $foo['manufacturer'] . ' - ';
                $lis .= '<b>Lot Number:</b> ' . $foo['lot_number'] . ' - ';
                $lis .= '<b>Dose:</b> ' . $foo['administer_amount'] . ' ' . $foo['administer_units'] . ' - ';
                $lis .= '<b>Administered By:</b> ' . $administered_by . '</li>';
            }
            $str_buff .= '<p><b>Immunizations:</b>';

            if(!$encounter['review_immunizations']){
                $str_buff .= ' (Not reviewed)';
            }

            $str_buff .= '</p>';

            $str_buff .= '<ul class="ProgressNote-ul">' . $lis . '</ul>';

            $str_buff .= '</div>';
        } else if($encounter['review_immunizations']) {
            $str_buff .= '<div class="indent">';
            $str_buff .= '<p><b>Immunizations:</b> Patient has no Immunizations.</p>';
            $str_buff .= '</div>';
        }else{
            $str_buff .= '<div class="indent">';
            $str_buff .= '<p><b>Immunizations:</b> (Not Reviewed)</p>';
            $str_buff .= '</div>';
        }

        unset($Immunizations, $immunizations);


		/**
		 * Medications Administered
		 */
		$Medications = new Medications();
		$medications_administered = $Medications->getPatientMedicationsAdministered((object)['eid' => $eid]);
		if(!empty($medications_administered)){

			$str_buff .= '<div class="indent">';
			$lis = '';
			foreach($medications_administered as $foo){

				$lis .= '<li>';
				$lis .= '<u>' . (isset($foo['description']) ? $foo['description'] : '')  . '</u> - ';

				if(isset($foo['not_performed_text']) && $foo['not_performed_text'] != ''){
					$lis .= '<b> Not Performed:</b> ' . $foo['not_performed_text'];
				}else{
					$lis .= '<b> Amount:</b> ' . (isset($foo['administered_amount']) ? $foo['administered_amount'] : '')  . ' - ';
					$lis .= '<b> Units:</b> ' . (isset($foo['administered_units']) ? $foo['administered_units'] : '')  . ' - ';
					$lis .= '<b> Site:</b> ' . (isset($foo['administered_site']) ? $foo['administered_site'] : '')  . ' - ';
					$lis .= '<b> Exp. Date:</b> ' . (isset($foo['exp_date']) && $foo['exp_date'] !== '' ? $foo['exp_date'] : ' - ');
					$lis .= '<b> Lot No.:</b> ' . (isset($foo['lot_number']) && $foo['lot_number'] !== '' ? $foo['lot_number'] : ' - ');
				}

				if(isset($foo['adverse_reaction_text']) && $foo['adverse_reaction_text'] != ' '){
					$lis .= '<b> Adverse Reaction:</b> ' . $foo['adverse_reaction_text'];
				}

				if(isset($foo['administered_lname']) && $foo['administered_lname'] != ' ') {
					$lis .= '<b> Administer By:</b> ' . sprintf('%s, %s', $foo['administered_lname'], $foo['administered_fname']);
				}

				$lis .= '</li>';
			}
			$str_buff .= '<p><b>Medications Administered:</b></p>';
			$str_buff .= '<ul style="list-style-type:disc; margin-left: 20px">' . $lis . '</ul>';
			$str_buff .= '</div>';

		}
		unset($ActiveMedications, $medications_ordered, $medications_administered);

		/**
		 * Procedures
		 */
		$Procedures = new Procedures();
		$procedures = $Procedures->getPatientProceduresByEid($eid);
		if(!empty($procedures)){
			$str_buff .= '<div class="indent">';
			$lis = '';
			foreach($procedures as $foo){
				$performed_by = Person::fullname($foo['performer_fname'], $foo['performer_mname'], $foo['performer_lname']);
				$lis .= sprintf('<li><b>Description: </b> %s [%s] - ', $foo['code_text'], $foo['code']);

				if(isset($foo['not_performed_code_text']) && $foo['not_performed_code_text'] != ''){
					$lis .= sprintf('<b>Not Performed:</b> %s - </li>', $foo['not_performed_code_text']);
				}else{
					$lis .= sprintf('<b>Status:</b> %s - ', $foo['status_code']);
					$lis .= sprintf('<b>Observation:</b> %s - ', $foo['observation']);
					$lis .= sprintf('<b>Target Site:</b> %s - ', $foo['target_site_code_text']);
					$lis .= sprintf('<b>Date:</b> %s', $foo['procedure_date']);
//					$lis .= sprintf('<b>Performed By:</b> %s </li>', $performed_by);
				}
			}
			$str_buff .= '<p><b>Procedures:</b></p>';
			$str_buff .= '<ul class="ProgressNote-ul">' . $lis . '</ul>';
			$str_buff .= '</div>';
		}
		unset($Procedures, $procedures);


		return $str_buff;
	}

	private function getPlanExtraDataByEid($eid, $instructions = null, $include_nurse_data = true){

//		$record = $this->getEncounter($eid, true, false);
//		$encounter = (array)$record['encounter'];

		$str_buff = '';

		if(isset($instructions) && $instructions != ''){
            $instructions = trim($instructions);
			$str_buff .= '<div class="indent">';
			$str_buff .= "<p><b>Instructions:</b> {$instructions}</p>";
			$str_buff .= '</div>';
		}

		/**
		 * Medications Orders
		 */
		$Medications = new Medications();
		$medications_ordered = $Medications->getPatientMedicationsOrdersByEid($eid);
		if(!empty($medications_ordered)){

			$str_buff .= '<div class="indent">';
			$lis = '';
			foreach($medications_ordered as $foo){
				$lis .= '<li>';
				$lis .= '<u>' . (isset($foo['STR']) ? $foo['STR'] : '')  . '</u> - ';
				$lis .= '<b> Dispense:</b> ' . (isset($foo['dispense']) ? $foo['dispense'] : '')  . ' - ';
				$lis .= '<b> Refill:</b> ' . (isset($foo['refill']) ? $foo['refill'] : '')  . ' - ';
				$lis .= '<b> Instruction:</b> ' . (isset($foo['directions']) ? $foo['directions'] : '')  . ' - ';
				$lis .= '<b> Notes To Pharmacist:</b> ' . (isset($foo['notes']) && $foo['notes'] !== '' ? $foo['notes'] : 'None');
				$lis .= '</li>';
			}
			$str_buff .= '<p><b>Medications Order(s):</b></p>';
			$str_buff .= '<ul style="list-style-type:disc; margin-left: 20px">' . $lis . '</ul>';
			$str_buff .= '</div>';

		}

		$Orders = new Orders();

		/**
		 *  Laboratories
		 */
		$orders = $Orders->getPatientLabOrdersByEid($eid, true);
		if(!empty($orders)){
			$str_buff .= '<div class="indent">';
			$str_buff .= '<p><b>Laboratory Order(s):</b></p>';

			foreach($orders as $foo){
				$str_buff .= '<p class="indent" style="margin-left: 20px;">';
				$str_buff .= '<u>Description:</u> ' . $foo['description'] . '<br>';
				$str_buff .= '<u>Notes:</u> ' . $foo['note'] . '<br>';
				$str_buff .= '</p>';
			}
			$str_buff .= '</div>';
		}

		unset($orders);

		/**
		 * Radiology Orders
		 */
		$orders = $Orders->getPatientRadOrdersByEid($eid);
		if(!empty($orders)){
			$str_buff .= '<div class="indent">';
			$str_buff .= '<p><b>Radiology Order(s):</b></p>';

			foreach($orders as $foo){
				$str_buff .= '<p class="indent" style="margin-left: 20px;">';
				$str_buff .= '<u>Description:</u> ' . $foo['description'] . '<br>';
				$str_buff .= '<u>Notes:</u> ' . $foo['note'] . '<br>';
				$str_buff .= '</p>';
			}
			$str_buff .= '</div>';
		}

		unset($Orders, $orders);

		/**
		 * Referrals
		 */
		$Referrals = new Referrals();
		$referrals = $Referrals->getPatientReferralsByEid($eid);
		if(!empty($referrals)){
			$str_buff .= '<div class="indent">';
			$str_buff .= '<p><b>Referral(s):</b></p>';

			foreach($referrals as $foo){
				$str_buff .= '<p class="indent">';
				$str_buff .= '<u>Service:</u> ' . $foo['service_text'] . '<br>';
				$str_buff .= '<u>Reason:</u> ' . $foo['referal_reason'] . '<br>';
				$str_buff .= '<u>Risk Lvl:</u> ' . $foo['risk_level'] . '<br>';
				$str_buff .= '<u>To:</u> ' . $foo['refer_to'] . '<br>';
				$str_buff .= '</p>';
			}
			$str_buff .= '</div>';
		}
		unset($Referrals, $referrals);


		// dont include nurse data if false
		if(!$include_nurse_data){
			return $str_buff;
		}

		/**
		 * Nurses Notes
		 */
		$NursesNotes = new NursesNotes();
		$notes = $NursesNotes->getNursesNotesByEid($eid);
		if(!empty($notes)){
			$str_buff .= '<div class="indent" style="color:orangered">';
			$str_buff .= '<p><b>Nurses Notes(s):</b></p>';

			foreach($notes as $note){
				$str_buff .= '<p class="indent">';
				$str_buff .= '<u>Note:</u> ' . $note['note'] . '<br>';
				$str_buff .= '<u>Nurse:</u> ' . sprintf(
					'%s, %s %s',
					$note['nurse_lname'],
					$note['nurse_fname'],
					$note['nurse_mname']) . '<br>';
				$str_buff .= '</p>';
			}
			$str_buff .= '</div>';
		}
		unset($NursesNotes, $notes);


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
		$filters->filter[1]->value = $date . ' 23:59:59';

		return $this->getEncounters($filters, false);
	}

	public function getDashboardTodayEncounters(){

		$sql = 'SELECT CONCAT(DATE(service_date), \' \' ,HOUR(service_date), \':00:00\' ) as service_hour, count(*) as total_by_hour  FROM (
					SELECT eid, service_date FROM encounters WHERE service_date >= CONCAT(curdate(), \' 00:00:00\') && service_date <= CONCAT(curdate(), \' 23:59:59\')
				) as enc GROUP BY service_hour;';

		$sth = $this->conn->prepare($sql);
		$sth->execute();
		return $sth->fetchAll(PDO::FETCH_ASSOC);
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
		return $this->soap->load($params)->leftJoin(
			['brief_description' => 'chief_complaint'], 'encounters', 'eid', 'eid'
		)->one();
	}

	public function getSoaps($params) {
		return $this->soap->load($params)->leftJoin(
			['brief_description' => 'chief_complaint'], 'encounters', 'eid', 'eid'
		)->all();
	}

	public function getReviewOfSystemSettingsByUserId($user_id){
		$this->ross->addFilter('user_id', $user_id);
		return $this->ross->load()->one();
	}

	public function saveReviewOfSystemSettings($params){
		return $this->ross->save($params);
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

	public function getPhysicalExams($params) {
		return $this->phye->load($params)->one();
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

	public function TransferEncounter($eid, $pid){

		$transfer_tables = [];
		$sth = $this->conn->prepare('SHOW TABLES');
		$sth->execute();
		$tables = $sth->fetchAll(PDO::FETCH_NUM);

		foreach($tables as $table){
			$table = $table[0];

			if($table == 'patient' || $table == 'patient_temp' || $table == 'patient_sync') continue;
			$sth = $this->conn->prepare("SHOW COLUMNS FROM {$table} WHERE Field = 'pid' OR Field = 'eid'");
			$sth->execute();
			$columns = $sth->fetchAll(PDO::FETCH_ASSOC);

			if(count($columns) != 2) continue;
			$transfer_tables[] = $table;

		}

		$transfer_table = '';

		try{

			$this->conn->beginTransaction();
			$this->conn->exec('SET FOREIGN_KEY_CHECKS = 0;');

			foreach($transfer_tables as $transfer_table){
				$sth = $this->conn->prepare("UPDATE {$transfer_table} SET `pid` = :pid WHERE `eid` = :eid");
				$sth->execute([ 'pid' => $pid, ':eid' => $eid ]);
			}

			$this->conn->exec('SET FOREIGN_KEY_CHECKS = 0;');
			$this->conn->commit();
			return true;
		}catch (Exception $e){
			error_log($e->getMessage());
			$this->conn->rollBack();
			return $e->getMessage() . 'Table: '. $transfer_table;
		}

	}

	public function getOpenEncounters($params){

		$where = '';
		$values = [];

		if(isset($params->provider_uid)){
			$where = 'AND e.provider_uid = :provider_uid';
			$values[':provider_uid'] = $params->provider_uid;
		}

		$sth = $this->conn->prepare("
			SELECT e.service_date,
			       e.provider_uid,
			       e.eid,
			       e.pid,
			       CONCAT(u.lname,', ', u.fname) as provider,
			       CONCAT(p.lname,', ', p.fname) as patient
			FROM encounters as e
			INNER JOIN users as u ON u.id = e.provider_uid
			INNER JOIN patient as p ON p.pid = e.pid
			WHERE e.close_date IS NULL {$where}
		");

		$sth->execute($values);
		$results = $sth->fetchAll(PDO::FETCH_ASSOC);

		return [
			'total' => count($results),
			'data' => array_slice($results, $params->start, $params->limit)
		];

	}

}
