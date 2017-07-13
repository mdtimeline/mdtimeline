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
include_once(ROOT . '/dataProvider/Person.php');
include_once(ROOT . '/dataProvider/PatientContacts.php');
include_once(ROOT . '/dataProvider/User.php');
include_once(ROOT . '/dataProvider/Encounter.php');
include_once(ROOT . '/dataProvider/Referrals.php');
include_once(ROOT . '/dataProvider/Facilities.php');
include_once(ROOT . '/dataProvider/DocumentFPDI.php');
include_once(ROOT . '/dataProvider/i18nRouter.php');

class Documents {
	/**
	 * @var MatchaHelper
	 */
	private $db;
	/**
	 * @var Patient
	 */
	private $Patient;

	/**
	 * @var Encounter
	 */
	private $Encounter;

	/**
	 * @var User
	 */
	private $User;

	/**
	 * @var DocumentFPDI
	 */
	public $pdf;

	/**
	 * @var \MatchaCUP
	 */
	private $t;

	function __construct() {
		$this->db = new MatchaHelper();
		$this->Patient = new Patient();
		$this->Encounter = new Encounter();
		$this->User = new User();

		$this->t = \MatchaModel::setSenchaModel('App.model.administration.DocumentsPdfTemplate');
		return;
	}

	public function getArrayWithTokensNeededByDocumentID($id) {
		$this->db->setSQL("SELECT title, body FROM documents_templates WHERE id = '$id' ");
		$record = $this->db->fetchRecord(PDO::FETCH_ASSOC);
		$regex = '(\[\w*?\])';
		$body = $record['body'];
		preg_match_all($regex, $body, $tokensfound);
		return $tokensfound[0];
	}

	public function getTemplateBodyById($id) {
		$this->db->setSQL("SELECT title, body FROM documents_templates WHERE id = '$id' ");
		return $this->db->fetchRecord(PDO::FETCH_ASSOC);
	}

	public function getAllPatientData($pid) {
		$this->db->setSQL("SELECT * FROM patient WHERE pid = '$pid'");
		return $this->db->fetchRecord(PDO::FETCH_ASSOC);
	}

	public function updateDocumentsTitle(stdClass $params) {
		$data = get_object_vars($params);
		$id = $data['id'];
		unset($data['id'], $data['date']);
		$this->db->setSQL($this->db->sqlBind($data, 'patient_documents', 'U', ['id' => $id]));
		$this->db->execLog();
		return $params;
	}

	public function setArraySizeOfTokenArray($tokens) {
		$givingValuesToTokens = [];
		foreach($tokens as $tok){
			array_push($givingValuesToTokens, '');
		}
		return $givingValuesToTokens;
	}

	public function get_PatientTokensData($pid, $allNeededInfo, $tokens) {

        $patientData = $this->getAllPatientData($pid);
		$age = $this->Patient->getPatientAgeByDOB($patientData['DOB']);
		$patienInformation = [
			'[PATIENT_NAME]' => $patientData['fname'],
			'[PATIENT_ID]' => $patientData['pid'],
			'[PATIENT_RECORD_NUMBER]' => $patientData['pubpid'],
			'[PATIENT_FULL_NAME]' => $this->Patient->getPatientFullNameByPid($patientData['pid']),
			'[PATIENT_LAST_NAME]' => $patientData['lname'],
			'[PATIENT_SEX]' => $patientData['sex'],
			'[PATIENT_BIRTHDATE]' => $patientData['DOB'],
			'[PATIENT_MARITAL_STATUS]' => $patientData['marital_status'],
			'[PATIENT_SOCIAL_SECURITY]' => $patientData['SS'],
			'[PATIENT_EXTERNAL_ID]' => $patientData['pubpid'],
			'[PATIENT_DRIVERS_LICENSE]' => $patientData['drivers_license'],

			'[PATIENT_POSTAL_ADDRESS_LINE_ONE]' => isset($patientData['postal_address']) ? $patientData['postal_address'] : '',
			'[PATIENT_POSTAL_ADDRESS_LINE_TWO]' => isset($patientData['postal_address_cont']) ? $patientData['postal_address_cont'] : '',
			'[PATIENT_POSTAL_CITY]' => isset($patientData['postal_city']) ? $patientData['postal_city'] : '',
			'[PATIENT_POSTAL_STATE]' => isset($patientData['postal_state']) ? $patientData['postal_state'] : '',
			'[PATIENT_POSTAL_ZIP]' => isset($patientData['postal_zip']) ? $patientData['postal_zip'] : '',
			'[PATIENT_POSTAL_COUNTRY]' => isset($patientData['postal_country']) ? $patientData['postal_country'] : '',

			'[PATIENT_PHYSICAL_ADDRESS_LINE_ONE]' => isset($patientData['physical_address']) ? $patientData['physical_address'] : '',
			'[PATIENT_PHYSICAL_ADDRESS_LINE_TWO]' => isset($patientData['physical_address_cont']) ? $patientData['physical_address_cont'] : '',
			'[PATIENT_PHYSICAL_CITY]' => isset($patientData['physical_city']) ? $patientData['physical_city'] : '',
			'[PATIENT_PHYSICAL_STATE]' => isset($patientData['physical_state']) ? $patientData['physical_state'] : '',
			'[PATIENT_PHYSICAL_ZIP]' => isset($patientData['physical_zip']) ? $patientData['physical_zip'] : '',
			'[PATIENT_PHYSICAL_COUNTRY]' => isset($patientData['physical_country']) ? $patientData['physical_country'] : '',

			'[PATIENT_HOME_PHONE]' => isset($patientData['phone_home']) ? $patientData['phone_home'] : '',
			'[PATIENT_MOBILE_PHONE]' => isset($patientData['phone_mobile']) ? $patientData['phone_mobile'] : '',
			'[PATIENT_WORK_PHONE]' => isset($patientData['phone_work']) ? $patientData['phone_work'] : '',

			'[PATIENT_EMAIL]' => isset($patientData['email']) ? $patientData['email'] : '',

			'[PATIENT_MOTHERS_NAME]' => isset($patientData['mother_fname']) ?
                Person::fullname(
	                $patientData['mother_fname'],
	                $patientData['mother_mname'],
	                $patientData['mother_lname']
                ) : '',

			'[PATIENT_GUARDIANS_NAME]' => isset($patientData['guardians_fname']) ?
                Person::fullname(
	                $patientData['guardians_fname'],
	                $patientData['guardians_mname'],
	                $patientData['guardians_lname']
                ) : '',

			'[PATIENT_EMERGENCY_CONTACT]' => isset($patientData['emergency_contact_fname']) ?
                Person::fullname(
	                $patientData['emergency_contact_fname'],
	                $patientData['emergency_contact_mname'],
	                $patientData['emergency_contact_lname']
                ) : '',

            // TODO: Create a method to parse a phone number in the person dataProvider
			'[PATIENT_EMERGENCY_PHONE]' => isset($patientData['emergency_contact_phone']) ? $patientData['emergency_contact_phone'] : '',

			'[PATIENT_PROVIDER]' => is_numeric($patientData['provider']) ?
                $this->User->getUserFullNameById($patientData['provider']) : '',

			'[PATIENT_PHARMACY]' => $patientData['pharmacy'],
			'[PATIENT_AGE]' => $age['DMY']['years'],
			'[PATIENT_OCCUPATION]' => $patientData['occupation'],

			'[PATIENT_EMPLOYEER]' => isset($patientData['employer_name']) ? $patientData['employer_name'] : '',

			'[PATIENT_RACE]' => $patientData['race'],
			'[PATIENT_ETHNICITY]' => $patientData['ethnicity'],
			'[PATIENT_LENGUAGE]' => $patientData['language'],
			'[PATIENT_PICTURE]' => '<img src="' . $patientData['image'] . '" style="width:100px;height:100px">',
			'[PATIENT_QRCODE]' => '<img src="' . $patientData['qrcode'] . '" style="width:100px;height:100px">',

//			'[PATIENT_TABACCO]' => 'tabaco',
//			'[PATIENT_ALCOHOL]' => 'alcohol',
			//            '[PATIENT_BALANCE]' => '$' . $this->fees->getPatientBalanceByPid($pid),
			//            '[PATIENT_PRIMARY_PLAN]' => $patientData['primary_plan_name'],
			//            '[PATIENT_PRIMARY_EFFECTIVE_DATE]' => $patientData['primary_effective_date'],
			//            '[PATIENT_PRIMARY_SUBSCRIBER]' => $patientData['primary_subscriber_title'] . $patientData['primary_subscriber_fname'] . ' ' . $patientData['primary_subscriber_mname'] . ' ' . $patientData['primary_subscriber_lname'],
			//            '[PATIENT_PRIMARY_POLICY_NUMBER]' => $patientData['primary_policy_number'],
			//            '[PATIENT_PRIMARY_GROUP_NUMBER]' => $patientData['primary_group_number'],
			//            '[PATIENT_PRIMARY_SUBSCRIBER_STREET]' => $patientData['primary_subscriber_street'],
			//            '[PATIENT_PRIMARY_SUBSCRIBER_CITY]' => $patientData['primary_subscriber_city'],
			//            '[PATIENT_PRIMARY_SUBSCRIBER_STATE]' => $patientData['primary_subscriber_state'],
			//            '[PATIENT_PRIMARY_SUBSCRIBER_COUNTRY]' => $patientData['primary_subscriber_country'],
			//            '[PATIENT_PRIMARY_SUBSCRIBER_ZIPCODE]' => $patientData['primary_subscriber_zip_code'],
			//            '[PATIENT_PRIMARY_SUBSCRIBER_RELATIONSHIP]' => $patientData['primary_subscriber_relationship'],
			//            '[PATIENT_PRIMARY_SUBSCRIBER_PHONE]' => $patientData['primary_subscriber_phone'],
			//            '[PATIENT_PRIMARY_SUBSCRIBER_EMPLOYER]' => $patientData['primary_subscriber_employer'],
			//            '[PATIENT_PRIMARY_SUBSCRIBER_EMPLOYER_CITY]' => $patientData['primary_subscriber_employer_city'],
			//            '[PATIENT_PRIMARY_SUBSCRIBER_EMPLOYER_STATE]' => $patientData['primary_subscriber_employer_state'],
			//            '[PATIENT_PRIMARY_SUBSCRIBER_EMPLOYER_COUNTRY]' => $patientData['primary_subscriber_employer_country'],
			//            '[PATIENT_PRIMARY_SUBSCRIBER_EMPLOYER_ZIPCODE]' => $patientData['primary_subscriber_zip_code'],
			//            '[PATIENT_SECONDARY_PLAN]' => $patientData['secondary_plan_name'],
			//            '[PATIENT_SECONDARY_EFFECTIVE_DATE]' => $patientData['secondary_effective_date'],
			//            '[PATIENT_SECONDARY_SUBSCRIBER]' => $patientData['secondary_subscriber_title'] . $patientData['primary_subscriber_fname'] . ' ' . $patientData['primary_subscriber_mname'] . ' ' . $patientData['primary_subscriber_lname'],
			//            '[PATIENT_SECONDARY_POLICY_NUMBER]' => $patientData['secondary_policy_number'],
			//            '[PATIENT_SECONDARY_GROUP_NUMBER]' => $patientData['secondary_group_number'],
			//            '[PATIENT_SECONDARY_SUBSCRIBER_STREET]' => $patientData['secondary_subscriber_street'],
			//            '[PATIENT_SECONDARY_SUBSCRIBER_CITY]' => $patientData['secondary_subscriber_city'],
			//            '[PATIENT_SECONDARY_SUBSCRIBER_STATE]' => $patientData['secondary_subscriber_state'],
			//            '[PATIENT_SECONDARY_SUBSCRIBER_COUNTRY]' => $patientData['secondary_subscriber_country'],
			//            '[PATIENT_SECONDARY_SUBSCRIBER_ZIPCODE]' => $patientData['secondary_subscriber_zip_code'],
			//            '[PATIENT_SECONDARY_SUBSCRIBER_RELATIONSHIP]' => $patientData['secondary_subscriber_relationship'],
			//            '[PATIENT_SECONDARY_SUBSCRIBER_PHONE]' => $patientData['secondary_subscriber_phone'],
			//            '[PATIENT_SECONDARY_SUBSCRIBER_EMPLOYER]' => $patientData['secondary_subscriber_employer'],
			//            '[PATIENT_SECONDARY_SUBSCRIBER_EMPLOYER_CITY]' => $patientData['secondary_subscriber_employer_city'],
			//            '[PATIENT_SECONDARY_SUBSCRIBER_EMPLOYER_STATE]' => $patientData['secondary_subscriber_employer_state'],
			//            '[PATIENT_SECONDARY_SUBSCRIBER_EMPLOYER_COUNTRY]' => $patientData['secondary_subscriber_employer_country'],
			//            '[PATIENT_SECONDARY_SUBSCRIBER_EMPLOYER_ZIPCODE]' => $patientData['secondary_subscriber_zip_code'],
			//            '[PATIENT_TERTIARY_PLAN]' => $patientData['tertiary_plan_name'],
			//            '[PATIENT_TERTIARY_EFFECTIVE_DATE]' => $patientData['tertiary_effective_date'],
			//            '[PATIENT_TERTIARY_SUBSCRIBER]' => $patientData['tertiary_subscriber_title'] . $patientData['primary_subscriber_fname'] . ' ' . $patientData['primary_subscriber_mname'] . ' ' . $patientData['primary_subscriber_lname'],
			//            '[PATIENT_TERTIARY_POLICY_NUMBER]' => $patientData['tertiary_policy_number'],
			//            '[PATIENT_TERTIARY_GROUP_NUMBER]' => $patientData['tertiary_group_number'],
			//            '[PATIENT_TERTIARY_SUBSCRIBER_STREET]' => $patientData['tertiary_subscriber_street'],
			//            '[PATIENT_TERTIARY_SUBSCRIBER_CITY]' => $patientData['tertiary_subscriber_city'],
			//            '[PATIENT_TERTIARY_SUBSCRIBER_STATE]' => $patientData['tertiary_subscriber_state'],
			//            '[PATIENT_TERTIARY_SUBSCRIBER_COUNTRY]' => $patientData['tertiary_subscriber_country'],
			//            '[PATIENT_TERTIARY_SUBSCRIBER_ZIPCODE]' => $patientData['tertiary_subscriber_zip_code'],
			//            '[PATIENT_TERTIARY_SUBSCRIBER_RELATIONSHIP]' => $patientData['tertiary_subscriber_relationship'],
			//            '[PATIENT_TERTIARY_SUBSCRIBER_PHONE]' => $patientData['tertiary_subscriber_phone'],
			//            '[PATIENT_TERTIARY_SUBSCRIBER_EMPLOYER]' => $patientData['tertiary_subscriber_employer'],
			//            '[PATIENT_TERTIARY_SUBSCRIBER_EMPLOYER_CITY]' => $patientData['tertiary_subscriber_employer_city'],
			//            '[PATIENT_TERTIARY_SUBSCRIBER_EMPLOYER_STATE]' => $patientData['tertiary_subscriber_employer_state'],
			//            '[PATIENT_TERTIARY_SUBSCRIBER_EMPLOYER_COUNTRY]' => $patientData['tertiary_subscriber_employer_country'],
			//            '[PATIENT_TERTIARY_SUBSCRIBER_EMPLOYER_ZIPCODE]' => $patientData['tertiary_subscriber_zip_code']
		];

		foreach($tokens as $i => $tok){
			if(isset($patienInformation[$tok]) && ($allNeededInfo[$i] == '' || $allNeededInfo[$i] == null)){
				$allNeededInfo[$i] = $patienInformation[$tok];
			};
		}
		return $allNeededInfo;
	}

	public function get_EncounterTokensData($eid, $allNeededInfo, $tokens) {

		$params = new stdClass();
		$params->eid = $eid;
		$encounter = $this->Encounter->getEncounter($params);

		if(!isset($encounter['encounter'])){
			return $allNeededInfo;
		}

		$encounterCodes = $this->Encounter->getEncounterCodes($params);

		$vitals = end($encounter['encounter']['vitals']);

		$soap = $encounter['encounter']['soap'];

		if(isset($encounter['encounter']['reviewofsystemschecks'])){
			$rosCks = $encounter['encounter']['reviewofsystemschecks'];

			unset($rosCks['id'], $rosCks['pid'], $rosCks['eid'], $rosCks['uid'], $rosCks['date']);

			foreach($rosCks as $rosc => $num){
				if($num == '' || $num == null || $num == 0){
					unset($rosCks[$rosc]);
				}
			}
		}

		if(isset($encounter['encounter']['reviewofsystems'])){
			$reviewofsystems = $encounter['encounter']['reviewofsystems'];

			unset($reviewofsystems['pid'], $reviewofsystems['eid'], $reviewofsystems['uid'], $reviewofsystems['id'], $reviewofsystems['date']);

			foreach($reviewofsystems as $ros => $num){
				if($num == '' || $num == null || $num == 'null'){
					unset($reviewofsystems[$ros]);
				}
			}
		}

		$cpt = [];
		$dx = [];
		$hcpc = [];
		$cvx = [];

		if(isset($encounterCodes['rows'])){
			foreach($encounterCodes['rows'] as $code){
				if($code['code_type'] == 'CPT'){
					$cpt[] = $code;
				} elseif($code['code_type'] == 'ICD' || $code['code_type'] == 'ICD9' || $code['code_type'] == 'ICD10') {
					$dx[] = $code;
				} elseif($code['code_type'] == 'HCPC') {
					$hcpc[] = $code;
				} elseif($code['code_type'] == 'CVX') {
					$cvx[] = $code;
				}
			}
		}

		$Medications = new Medications();
		$medications = $Medications->getPatientMedicationsByEid($eid);
		unset($Medications);

		$Immunizations = new Immunizations();
		$immunizations = $Immunizations->getImmunizationsByEid($eid);
		unset($Immunizations);

		$Allergies = new Allergies();
		$allergies = $Allergies->getPatientAllergiesByEid($eid);
		unset($Allergies);

		$ActiveProblems = new ActiveProblems();
		$activeProblems = $ActiveProblems->getPatientActiveProblemByEid($eid);
		unset($ActiveProblems);

		$encounter = $encounter['encounter'];

		$encounterInformation = [
			'[ENCOUNTER_DATE]' => $this->dateToString($encounter['service_date']),
			'[ENCOUNTER_START_DATE]' => $this->dateToString($encounter['service_date']),
			'[ENCOUNTER_END_DATE]' => $this->dateToString($encounter['close_date']),
			'[ENCOUNTER_BRIEF_DESCRIPTION]' => $encounter['brief_description'],
			'[ENCOUNTER_SENSITIVITY]' => $encounter['priority'],
			'[ENCOUNTER_WEIGHT_LBS]' => $vitals['weight_lbs'],
			'[ENCOUNTER_WEIGHT_KG]' => $vitals['weight_kg'],
			'[ENCOUNTER_HEIGHT_IN]' => $vitals['height_in'],
			'[ENCOUNTER_HEIGHT_CM]' => $vitals['height_cm'],
			'[ENCOUNTER_BP_SYSTOLIC]' => $vitals['bp_systolic'],
			'[ENCOUNTER_BP_DIASTOLIC]' => $vitals['bp_diastolic'],
			'[ENCOUNTER_PULSE]' => $vitals['pulse'],
			'[ENCOUNTER_RESPIRATION]' => $vitals['respiration'],
			'[ENCOUNTER_TEMP_FAHRENHEIT]' => $vitals['temp_f'],
			'[ENCOUNTER_TEMP_CELSIUS]' => $vitals['temp_c'],
			'[ENCOUNTER_TEMP_LOCATION]' => $vitals['temp_location'],
			'[ENCOUNTER_OXYGEN_SATURATION]' => $vitals['oxygen_saturation'],
			'[ENCOUNTER_HEAD_CIRCUMFERENCE_IN]' => $vitals['head_circumference_in'],
			'[ENCOUNTER_HEAD_CIRCUMFERENCE_CM]' => $vitals['head_circumference_cm'],
			'[ENCOUNTER_WAIST_CIRCUMFERENCE_IN]' => $vitals['waist_circumference_in'],
			'[ENCOUNTER_WAIST_CIRCUMFERENCE_CM]' => $vitals['waist_circumference_cm'],
			'[ENCOUNTER_BMI]' => $vitals['bmi'],
			'[ENCOUNTER_BMI_STATUS]' => $vitals['bmi_status'],
			'[ENCOUNTER_SUBJECTIVE]' => (isset($soap['subjective']) ? $soap['subjective'] : ''),
			'[ENCOUNTER_OBJECTIVE]' => (isset($soap['objective']) ? $soap['objective'] : ''),
			'[ENCOUNTER_ASSESSMENT]' => (isset($soap['assessment']) ? $soap['assessment'] : ''),
			'[ENCOUNTER_PLAN]' => (isset($soap['plan']) ? $soap['plan'] : ''),
			'[ENCOUNTER_CPT_CODES]' => $this->tokensForEncountersList($cpt, 1),
			'[ENCOUNTER_ICD_CODES]' => $this->tokensForEncountersList($dx, 2),
			'[ENCOUNTER_HCPC_CODES]' => $this->tokensForEncountersList($hcpc, 3),
			'[ENCOUNTER_ALLERGIES_LIST]' => $this->tokensForEncountersList($allergies, 4),
			'[ENCOUNTER_MEDICATIONS_LIST]' => $this->tokensForEncountersList($medications, 5),
			'[ENCOUNTER_ACTIVE_PROBLEMS_LIST]' => $this->tokensForEncountersList($activeProblems, 6),
			'[ENCOUNTER_IMMUNIZATIONS_LIST]' => $this->tokensForEncountersList($immunizations, 7),
			//'[ENCOUNTER_PREVENTIVECARE_DISMISS]' => $this->tokensForEncountersList($preventivecaredismiss, 10),
			'[ENCOUNTER_REVIEWOFSYSTEMSCHECKS]' => isset($rosCks) ? $this->tokensForEncountersList($rosCks, 11) : '',
			'[ENCOUNTER_REVIEWOFSYSTEMS]' => isset($reviewofsystems) ? $this->tokensForEncountersList($reviewofsystems, 12) : '',
			//            '[]'     =>$this->tokensForEncountersList($hcpc,13),
			//            '[]'     =>$this->tokensForEncountersList($hcpc,14),
			//            '[]'     =>$this->tokensForEncountersList($hcpc,15),
			//            '[]'
			// =>$this->tokensForEncountersList($preventivecaredismiss,16),
			//            '[]'
			// =>$this->tokensForEncountersList($reviewofsystemschecks,17),
			//            '[]'
			// =>$this->tokensForEncountersList($preventivecaredismiss,16),
			//            '[]'
			// =>$this->tokensForEncountersList($preventivecaredismiss,16)
		];

		foreach($tokens as $i => $tok){
			if(isset($encounterInformation[$tok]) && ($allNeededInfo[$i] == '' || $allNeededInfo[$i] == null)){
				$allNeededInfo[$i] = $encounterInformation[$tok];
			}
		}


		//
		if($key = array_search('[ENCOUNTER_PROGRESS_NOTE]', $tokens)){
			$allNeededInfo[$key] = $this->Encounter->ProgressNoteString(
				$this->Encounter->getProgressNoteByEid($params->eid)
			);
		}

		return $allNeededInfo;
	}

	private function tokensForEncountersList($Array, $typeoflist) {
		$html = '';
		if($typeoflist == 1){
			$html .= '<table>';
			$html .= "<tr><th>" . "CPT" . "</th><th>" . "Code text" . "</th></tr>";
			foreach($Array as $row){
				$html .= "<tr><td>" . $row['code'] . "</td><td>" . $row['code_text_short'] . "</td></tr>";
			}
			$html .= '</table>';
		} elseif($typeoflist == 2) {
			$html .= '<table>';
			$html .= "<tr><th>" . "ICD" . "</th><th>" . "Code text" . "</th></tr>";
			foreach($Array as $row){
				$html .= "<tr><td>" . $row['code'] . "</td><td>" . $row['code_text'] . "</td></tr>";
			}
			$html .= '</table>';
		} elseif($typeoflist == 3) {
			$html .= '<table>';
			$html .= "<tr><th>" . "HCPC" . "</th><th>" . "Code text" . "</th></tr>";
			foreach($Array as $row){
				$html .= "<tr><td>" . $row['code'] . "</td><td>" . $row['code_text'] . "</td></tr>";
			}
			$html .= '</table>';
		} elseif($typeoflist == 4) {
			$html .= '<table>';
			$html .= "<tr><th>" . "Allergies" . "</th><th>" . "Type" . "</th><th>" . "Severity" . "</th></tr>";
			foreach($Array as $row){
				$html .= "<tr><td>" . $row['allergy'] . "</td><td>" . $row['allergy_type'] . "</td><td>" . $row['severity'] . "</td></tr>";
			}
			$html .= '</table>';
		} elseif($typeoflist == 5) {
			$html .= '<table>';
			$html .= "<tr><th>" . "Medications" . "</th></tr>";
			foreach($Array as $row){
				$html .= "<tr><td>" . $row['STR'] . ' ' . $row['dose'] . ' ' . $row['route'] . ' ' . $row['form'] . ' ' . $row['directions'] . "</td></tr>";
			}
			$html .= '</table>';
		} elseif($typeoflist == 6) {
			$html .= '<table>';
			$html .= "<tr><th>" . "Active Problems" . "</th></tr>";
			foreach($Array as $row){
				$html .= "<tr><td>" . $row['code_text'] . "</td></tr>";
			}
			$html .= '</table>';
		} elseif($typeoflist == 7) {
			$html .= '<table>';
			$html .= "<tr><th>" . "Immunizations" . "</th></tr>";
			foreach($Array as $row){
				$html .= "<tr><td>" . $row['vaccine_name'] . "</td></tr>";
			}
			$html .= '</table>';
		} elseif($typeoflist == 8) {
			// Dental
		} elseif($typeoflist == 9) {
			// Surgeries
		} elseif($typeoflist == 10) {
			$html .= '<table>';
			$html .= "<tr><th>" . "Preventive Care" . "</th><th>" . "Reason" . "</th></tr>";
			foreach($Array as $row){
				$html .= "<tr><td>" . $row['description'] . "</td><td>" . $row['reason'] . "</td></tr>";
			}
			$html .= '</table>';
		} elseif($typeoflist == 11) {
			$html .= '<table width="300">';
			$html .= "<tr><th>" . "Review of systems checks" . "</th><th>" . "Active?" . "</th></tr>";
			foreach($Array as $key => $val){
				$html .= "<tr><td>" . str_replace('_', ' ', $key) . "</td><td>" . (($val === 1 || $val === '1') ? 'Yes' : 'No') . "</td></tr>";
			}
			$html .= '</table>';

		} elseif($typeoflist == 12) {
			$html .= '<table width="300">';
			$html .= "<tr><th>" . "Review of systems" . "</th><th>" . "Active?" . "</th></tr>";
			foreach($Array as $key => $val){
				$html .= "<tr><td>" . str_replace('_', ' ', $key) . "</td><td>" . (($val == 1 || $val == '1') ? 'Yes' : 'No') . "</td></tr>";
			}
			$html .= '</table>';

		}

		return ($Array == null || $Array == '') ? '' : $html;
	}

	private function getCurrentTokensData($allNeededInfo, $tokens) {

		$currentInformation = [
			'[CURRENT_DATE]' => date('d-m-Y'),
			'[CURRENT_USER_NAME]' => $_SESSION['user']['name'],
			'[CURRENT_USER_FULL_NAME]' => $_SESSION['user']['name'],
			'[CURRENT_USER_LICENSE_NUMBER]',
			'[CURRENT_USER_DEA_LICENSE_NUMBER]',
			'[CURRENT_USER_DM_LICENSE_NUMBER]',
			'[CURRENT_USER_NPI_LICENSE_NUMBER]',
			'[LINE]' => '<hr>'
		];
		foreach($tokens as $i => $tok){
			if(isset($currentInformation[$tok]) && ($allNeededInfo[$i] == '' || $allNeededInfo[$i] == null)){
				$allNeededInfo[$i] = $currentInformation[$tok];
			}
		}
		return $allNeededInfo;
	}

	private function getClinicTokensData($allNeededInfo, $tokens) {
		$facility = new Facilities();
		$facilityInfo = $facility->getActiveFacilitiesById($_SESSION['user']['facility']);
		$clinicInformation = [
			'[FACILITY_NAME]' => $facilityInfo['name'],
			'[FACILITY_PHONE]' => $facilityInfo['phone'],
			'[FACILITY_FAX]' => $facilityInfo['fax'],
			'[FACILITY_STREET]' => $facilityInfo['address'],
			'[FACILITY_STREET_CONT]' => $facilityInfo['address_cont'],
			'[FACILITY_CITY]' => $facilityInfo['city'],
			'[FACILITY_STATE]' => $facilityInfo['state'],
			'[FACILITY_POSTALCODE]' => $facilityInfo['postal_code'],
			'[FACILITY_COUNTRYCODE]' => $facilityInfo['country_code'],
			'[FACILITY_FEDERAL_EIN]' => $facilityInfo['ein'],
//			'[FACILITY_SERVICE_LOCATION]' => $facilityInfo['service_location'],
			'[FACILITY_BILLING_LOCATION]' => $facilityInfo['billing_location'],
			'[FACILITY_FACILITY_NPI]' => $facilityInfo['npi']
		];
		unset($facility);
		foreach($tokens as $i => $tok){
			if(isset($clinicInformation[$tok]) && ($allNeededInfo[$i] == '' || $allNeededInfo[$i] == null)){
				$allNeededInfo[$i] = $clinicInformation[$tok];
			}
		}
		return $allNeededInfo;
	}

	/**
	 * @param $params
	 * @param string $path
	 * @param null|array $custom_header_data
	 * @param array $key_images
	 * @param string $water_mark
	 * @return bool
	 */
	public function PDFDocumentBuilder($params, $path = '', $custom_header_data = null, $water_mark = '', $key_images = [], $singleColumn = false) {
		$pid = $params->pid;
		$regex = '(\[\w*?\])';

		$pdf = new DocumentFPDI();

		$pdf->water_mark = $water_mark;

		if(isset($custom_header_data)){
			$pdf->addCustomHeaderData($custom_header_data);
		}

		if(isset($params->facility_id)){
			$template = $this->getPdfTemplateByFacilityId($params->facility_id);
		}elseif(isset($_SESSION['user']) && isset($_SESSION['user']['facility'])){
			$template = $this->getPdfTemplateByFacilityId($_SESSION['user']['facility']);
		}else{
			$template = $this->getPdfTemplateByFacilityId();
		}

		// template
		$pdf->setSourceFile($template['template']);

		$margins = [
			'left' => isset($template['body_margin_left']) ? intval($template['body_margin_left']) : 25,
			'top' => isset($template['body_margin_top']) ? intval($template['body_margin_top']) : 25,
			'right' => isset($template['body_margin_right']) ? intval($template['body_margin_right']) : 25,
			'bottom' => isset($template['body_margin_bottom']) ? intval($template['body_margin_bottom']) : 25,
			'footer_margin' => isset($template['footer_margin']) ? intval($template['footer_margin']) : 0
		];

		$font = [
			'family' => isset($template['body_font_family']) ? $template['body_font_family'] : 'times',
			'size' => isset($template['body_font_size']) ? intval($template['body_font_size']) : 12,
		    'style' => isset($template['body_font_style']) ? $template['body_font_style'] : '',
		];

		$pdf->SetDefaultMonospacedFont('courier');
		$pdf->setHeaderMargin(0);
		$pdf->setFooterMargin($margins['footer_margin']);
		$pdf->SetAutoPageBreak(true, 20);

		/**
		 * courier : Courier
		 * courierB : Courier Bold
		 * courierBI : Courier Bold Italic
		 * courierI : Courier Italic
		 * helvetica : Helvetica
		 * helveticaB : Helvetica Bold
		 * helveticaBI : Helvetica Bold Italic
		 * helveticaI : Helvetica Italic
		 * symbol : Symbol
		 * times : Times New Roman
		 * timesB : Times New Roman Bold
		 * timesBI : Times New Roman Bold Italic
		 * timesI : Times New Roman Italic
		 * zapfdingbats : Zapf Dingbats
		 */
		$pdf->SetFont($font['family'],$font['style'],$font['size'], true);

		$pdf->SetAutoPageBreak(true, $margins['bottom']);
		$pdf->SetMargins($margins['left'], $margins['top'], $margins['right'], true);

		$pdf->SetCreator('MDTIMELINE');
		$pdf->SetAuthor(isset($_SESSION['user']['name']) ? $_SESSION['user']['name'] : 'mdTimeline Automated Generator');

		if(isset($params->DoctorsNote)){
			$body = $params->DoctorsNote;
			preg_match_all($regex, $body, $tokensfound);
			$tokens = $tokensfound;
		} elseif(isset($params->templateId)) {
			$tokens = $this->getArrayWithTokensNeededByDocumentID($params->templateId);
			//getting the template
			$body = $this->getTemplateBodyById($params->templateId);
		}else{
			$body['body'] = isset($params->body) ? $params->body : '';
		}

		if(isset($tokens)){

			$allNeededInfo = $this->setArraySizeOfTokenArray($tokens);
			$allNeededInfo = $this->get_PatientTokensData($pid, $allNeededInfo, $tokens);
			if(isset($params->eid) && $params->eid != 0 && $params->eid != ''){
				$allNeededInfo = $this->get_EncounterTokensData($params->eid, $allNeededInfo, $tokens);
			}
			$allNeededInfo = $this->getCurrentTokensData($allNeededInfo, $tokens);
			$allNeededInfo = $this->getClinicTokensData($allNeededInfo, $tokens);
			if(isset($params->orderItems) || isset($params->date_ordered)){
				$allNeededInfo = $this->parseTokensForOrders($params, $tokens, $allNeededInfo);
			}
			if(isset($params->referralId)){
				$allNeededInfo = $this->addReferralData($params, $tokens, $allNeededInfo);
			}
			if(isset($params->docNoteid)){
				$allNeededInfo = $this->addDoctorsNoteData($params, $tokens, $allNeededInfo);
			}
			if(isset($params->provider_uid)){
				$allNeededInfo = $this->addProviderData($params, $tokens, $allNeededInfo);
			}
		}

		// add line token
		$tokens[] = '{line}';
		$allNeededInfo[] = '<hr style="margin: 10px;">';
		$html = str_replace($tokens, $allNeededInfo, (isset($params->DoctorsNote)) ? $body : $body['body']);
		$pages = explode('{newpage}', $html);

		foreach($pages AS $page){
			$pdf->AddPage('','',true);

			if($this->isHtml($page)){
				$pdf->writeHTML($page);
			}else{
				$pdf->writeHTML(nl2br($page));
			}
		}

		$y = $pdf->GetY();

		$rowHeight = 0;

		//image //title
		foreach ($key_images as $index => $key_image){

			if(!isset($key_image['image']) || !isset($key_image['title'])){
				continue;
			}

			if($index === 0){
				$y = $y + 10;
			}

			$newRow = $singleColumn || !($index % 2);

			if($newRow){
				$x = $margins['right'];
				if($rowHeight > 0){
					$y = $y + $rowHeight + 10;
					$rowHeight = 0;
				}
			}else{
				$x = $pdf->getCenter();
				$y -= 5;
			}

			$img = 'data://text/plain;base64,' . $key_image['image'];
			$y += 5;
			$pdf->Image($img, $x, $y, 0, 0, 'jpg', '', 'N');

			$imageBuffer = $pdf->getImageBuffer($img);

			if($rowHeight < $imageBuffer['h']){
				$rowHeight = $imageBuffer['h'] / 2;
			}
		}

		if($path == ''){
			return $pdf->Output('temp.pdf', 'S');
		} else {
			$pdf->Output($path, 'F');
			$pdf->Close();
			return true;
		}
	}

	private function addProviderData($params, $tokens, $allNeededInfo) {

		$provider = $this->User->getUserByUid($params->provider_uid);

		if($provider === false){
			return $allNeededInfo;
		}

		$info = [
			'[PROVIDER_ID]' => $provider['id'],
			'[PROVIDER_TITLE]' => isset($provider['title']) ? $provider['title'] : '',
			'[PROVIDER_FULL_NAME]' => Person::fullname($provider['fname'], $provider['mname'], $provider['lname']),
			'[PROVIDER_FIRST_NAME]' => isset($provider['fname']) ? $provider['fname'] : '',
			'[PROVIDER_MIDDLE_NAME]' => isset($provider['mname']) ? $provider['mname'] : '',
			'[PROVIDER_LAST_NAME]' => isset($provider['lname']) ? $provider['lname'] : '',
			'[PROVIDER_SIGNATURE]' => isset($provider['signature']) ? $provider['signature'] : '',
			'[PROVIDER_NPI]' => isset($provider['npi']) ? $provider['npi'] : '',
			'[PROVIDER_LIC]' => isset($provider['lic']) ? $provider['lic'] : '',
			'[PROVIDER_DEA]' => isset($provider['feddrugid']) ? $provider['feddrugid'] : '',
			'[PROVIDER_FED_TAX]' => isset($provider['fedtaxid']) ? $provider['fedtaxid'] : '',
			'[PROVIDER_ESS]' => isset($provider['ess']) ? $provider['ess'] : '',
			'[PROVIDER_TAXONOMY]' => isset($provider['taxonomy']) ? $provider['taxonomy'] : '',
			'[PROVIDER_EMAIL]' => isset($provider['email']) ? $provider['email'] : '',
			'[PROVIDER_DIRECT_ADDRESS]' => isset($provider['direct_address']) ? $provider['direct_address'] : '',
			'[PROVIDER_ADDRESS_LINE_ONE]' => isset($provider['street']) ? $provider['street'] : '',
			'[PROVIDER_ADDRESS_LINE_TWO]' => isset($provider['street_cont']) ? $provider['street_cont'] : '',
			'[PROVIDER_ADDRESS_CITY]' => isset($provider['city']) ? $provider['city'] : '',
			'[PROVIDER_ADDRESS_STATE]' => isset($provider['state']) ? $provider['state'] : '',
			'[PROVIDER_ADDRESS_ZIP]' => isset($provider['postal_code']) ? $provider['postal_code'] : '',
			'[PROVIDER_ADDRESS_COUNTRY]' => isset($provider['country_code']) ? $provider['country_code'] : '',
			'[PROVIDER_PHONE]' => isset($provider['phone']) ? $provider['phone'] : '',
			'[PROVIDER_MOBILE]' => isset($provider['mobile']) ? $provider['mobile'] : '',
		];

		unset($provider);
		foreach($tokens as $i => $tok){
			if(isset($info[$tok]) && ($allNeededInfo[$i] == '' || $allNeededInfo[$i] == null)){
				$allNeededInfo[$i] = $info[$tok];
			}
		}

		return $allNeededInfo;
	}

	private function addReferralData($params, $tokens, $allNeededInfo) {

		$referral = new Referrals();
		$data = $referral->getPatientReferral($params->referralId);
		if($data === false)
			return $allNeededInfo;
		$info = [
			'[REFERRAL_ID]' => $data['id'],
			'[REFERRAL_DATE]' => $data['referral_date'],
			'[REFERRAL_REASON]' => $data['referal_reason'],
			'[REFERRAL_DIAGNOSIS]' => $data['diagnosis_code'] . ' (' . $data['diagnosis_code_type'] . ')',
			'[REFERRAL_SERVICE]' => $data['service_code'] . ' (' . $data['service_code_type'] . ')',
			'[REFERRAL_RISK_LEVEL]' => $data['risk_level'],
			'[REFERRAL_BY_TEXT]' => $data['refer_by_text'],
			'[REFERRAL_TO_TEXT]' => $data['refer_to_text']
		];
		unset($referral);
		foreach($tokens as $i => $tok){
			if(isset($info[$tok]) && ($allNeededInfo[$i] == '' || $allNeededInfo[$i] == null)){
				$allNeededInfo[$i] = $info[$tok];
			}
		}
		return $allNeededInfo;
	}

	private function addDoctorsNoteData($params, $tokens, $allNeededInfo) {

		$DoctorsNotes = new DoctorsNotes();
		$data = $DoctorsNotes->getDoctorsNote($params->docNoteid);
		if($data === false)
			return $allNeededInfo;
		$info = [
			'[DOC_NOTE_ID]' => $data['id'],
			'[DOC_NOTE_CREATE_DATE]' => $data['create_date'],
			'[DOC_NOTE_ORDER_DATE]' => $data['order_date'],
			'[DOC_NOTE_FROM_DATE]' => $data['from_date'],
			'[DOC_NOTE_TO_DATE]' => $data['to_date'],
//			'[DOC_NOTE_RETURN_DATE]' => $data['return_date'],
			'[DOC_NOTE_RESTRICTIONS]' => $this->arrayToOrderedList($data['restrictions']),
			'[DOC_NOTE_COMMENTS]' => $data['comments']
		];
		unset($referral);
		foreach($tokens as $i => $tok){
			if(isset($info[$tok]) && ($allNeededInfo[$i] == '' || $allNeededInfo[$i] == null)){
				$allNeededInfo[$i] = $info[$tok];
			}
		}
		return $allNeededInfo;
	}

	private function parseTokensForOrders($params, $tokens, $allNeededInfo) {

		if(isset($params->date_ordered)){
			$index = array_search('[ORDER_DATE]', $tokens);
			if($index !== false){
				$allNeededInfo[$index] = $params->date_ordered;
			}
		}

		if(isset($params->orderItems)){
			$index = array_search('[ORDER_ITEMS]', $tokens);
			if($index !== false){
				$html = $this->arrayToTable($params->orderItems);
				$allNeededInfo[$index] = $html;
			}
		}

		return $allNeededInfo;
	}

	public function arrayToOrderedList($array) {
		if(!is_array($array) || count($array) == 0)
			return 'N/A';
		$ol = '<ol style="margin: 0">';
		foreach($array as $list){
			$ol .= '<li>' . $list . '</li>';
		}
		$ol .= '</ol>';
		return $ol;
	}

	public function arrayToUnorderedList($array) {
		if(!is_array($array) || count($array) == 0)
			return 'N/A';
		$ol = '<ul>';
		foreach($array as $list){
			$ol .= '<li>' . $list . '</li>';
		}
		$ol .= '</ul>';
		return $ol;
	}

	public function arrayToTable($array) {
		if(!is_array($array) || count($array) == 0)
			return 'N/A';
		// open table tag
		$table = '<table width="100%" border="0" cellspacing="0" cellpadding="5">';

		// get header row
		$th = array_shift($array);

		// table header
		$table .= '<tr>';
		foreach($th AS $cell){
			$table .= '<th style="background-color:#5CB8E6;border-bottom:1px solid #000000;">' . $cell . '</th>';
		}
		$table .= '</tr>';

		// table rows
		foreach($array AS $index => $row){
			$table .= '<tr>';
			foreach($row AS $cell){
				$color = ($index % 2 == 0 ? '#ffffff' : '#f6f6f6');
				$table .= '<td style="background-color:' . $color . ';border-bottom:1px solid #999999;">' . $cell . '</td>';
			}
			$table .= '</tr>';
		}

		// close table tag
		$table .= '</table>';
		return $table;
	}

	private function isHtml($string)
	{
		return preg_match("/<[^<]+>/",$string,$m) != 0;
	}

	private function dateToString($date){
		if(!isset($date)) return 'UNK';
		return date('F j, Y', strtotime($date));
	}

	private function getPdfTemplateByFacilityId($facility_id = null){
		if(isset($facility_id)){
			$record = $this->t->load(['facility_id' =>$facility_id, 'active' => 1])->one();
			if($record !== false){
				$record['template'] = ROOT . $record['template'];
				return $record;
			}
		}
		return [
			'template' => ROOT . '/resources/templates/default.pdf',
			'body_margin_left' => 25,
			'body_margin_top' => 25,
			'body_margin_right' => 25,
			'body_font_family' => 'times',
			'body_font_style' => '',
			'body_font_size' => 12,
			'header_data' => null
        ];
	}
}