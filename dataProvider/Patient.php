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

include_once(ROOT . '/classes/Tokens.php');
include_once(ROOT . '/dataProvider/Person.php');
include_once(ROOT . '/dataProvider/User.php');
include_once(ROOT . '/dataProvider/ACL.php');
include_once(ROOT . '/dataProvider/PatientContacts.php');
include_once(ROOT . '/dataProvider/Immunizations.php');
include_once(ROOT . '/dataProvider/ActiveProblems.php');

class Patient
{

	/**
	 * @var User
	 */
	private $user;
	/**
	 * @var
	 */
	private $patient;

	/**
	 * @var MatchaCUP
	 */
	public $p;

    /**
     * @var ACL
     */
    public $acl;

	/**
	 * @var MatchaCUP
	 */
	private $e;
	/**
	 * @var MatchaCUP
	 */
	private $d;
	/**
	 * @var MatchaCUP
	 */
	private $c;
	/**
	 * @var MatchaCUP
	 */
	private $pa;

	/**
	 * @var MatchaCUP
	 */
	private $patientContacts;
	/**
	 * @var Immunizations
	 */
	private $Immunizations;
	/**
	 * @var ActiveProblems
	 */
	private $ActiveProblems;

	/**
	 * @var array
	 */
	private $errors = [];

	private $adult_age = 18;

	private $pregnancy_codes = <<<PREGNANCY_CODES
    A34,O00.0,O00.1,O00.2,O00.8,O00.9,O01.9,O02.0,O02.1,O03.0,O03.1,O03.2,O03.30,O03.31,O03.32,O03.33,O03.34,O03.37,
    O03.39,O03.4,O03.5,O03.6,O03.7,O03.80,O03.81,O03.82,O03.83,O03.84,O03.85,O03.86,O03.87,O03.88,O03.89,O03.9,O04.5,
    O04.6,O04.7,O04.80,O04.81,O04.82,O04.83,O04.84,O04.85,O04.86,O04.87,O04.88,O04.89,O07.0,O07.1,O07.2,O07.30,O07.31,
    O07.32,O07.33,O07.34,O07.35,O07.36,O07.37,O07.38,O07.39,O07.4,O08.0,O08.1,O08.2,O08.3,O08.4,O08.5,O08.6,O08.7,
    O08.81,O08.82,O08.83,O08.89,O08.9,O09.00,O09.10,O09.211,O09.291,O09.30,O09.40,O09.41,O09.42,O09.43,O09.511,O09.512,
    O09.513,O09.519,O09.521,O09.522,O09.523,O09.529,O09.611,O09.621,O09.819,O09.821,O09.822,O09.823,O09.829,O09.891,
    O09.892,O09.893,O09.899,O09.90,O09.91,O09.92,O09.93,O10.011,O10.012,O10.013,O10.019,O10.03,O10.111,O10.112,O10.113,
    O10.119,O10.12,O10.13,O10.211,O10.212,O10.213,O10.219,O10.22,O10.23,O10.311,O10.312,O10.313,O10.319,O10.32,O10.33,
    O10.411,O10.412,O10.413,O10.419,O10.42,O10.43,O10.911,O10.912,O10.913,O10.919,O10.92,O10.93,O11.1,O11.2,O11.3,O11.9,
    O12.00,O12.01,O12.02,O12.03,O12.20,O12.21,O12.22,O12.23,O13.1,O13.2,O13.3,O13.9,O14.00,O14.02,O14.03,O14.10,O14.12,
    O14.13,O14.20,O14.22,O14.23,O14.90,O14.92,O14.93,O15.02,O15.03,O15.1,O15.2,O16.1,O16.2,O16.3,O16.9,O20.0,O20.8,O20.9,
    O21.0,O21.2,O21.8,O21.9,O22.00,O22.01,O22.02,O22.03,O22.10,O22.11,O22.12,O22.13,O22.20,O22.21,O22.22,O22.23,O22.30,
    O22.31,O22.32,O22.33,O22.40,O22.41,O22.42,O22.43,O22.50,O22.51,O22.52,O22.53,O22.90,O22.91,O22.92,O22.93,O23.00,
    O23.10,O23.20,O23.30,O23.40,O23.41,O23.42,O23.43,O23.519,O23.529,O23.599,O23.90,O23.91,O23.92,O23.93,O24.319,O24.32,
    O24.419,O24.429,O24.439,O24.911,O24.912,O24.913,O24.92,O24.93,O25.10,O25.11,O25.12,O25.13,O25.2,O25.3,O26.00,O26.01,
    O26.02,O26.03,O26.11,O26.12,O26.13,O26.20,O26.21,O26.22,O26.23,O26.41,O26.42,O26.43,O26.50,O26.51,O26.52,O26.53,
    O26.611,O26.612,O26.613,O26.619,O26.62,O26.811,O26.812,O26.813,O26.819,O26.821,O26.822,O26.823,O26.829,O26.831,
    O26.832,O26.833,O26.839,O26.841,O26.842,O26.843,O26.849,O26.851,O26.852,O26.853,O26.859,O26.872,O26.873,O26.879,
    O26.891,O26.892,O26.893,O26.899,O26.90,O30.001,O30.002,O30.003,O30.009,O30.021,O30.022,O30.023,O30.029,O30.101,
    O30.102,O30.103,O30.109,O30.201,O30.202,O30.203,O30.209,O30.801,O30.802,O30.803,O30.809,O30.90,O30.91,O30.92,O30.93,
    O33.0,O33.1,O33.2,O33.7,O33.8,O33.9,O34.00,O34.01,O34.02,O34.03,O34.10,O34.11,O34.12,O34.13,O34.21,O34.29,O34.30,
    O34.31,O34.32,O34.33,O34.40,O34.41,O34.42,O34.43,O34.511,O34.512,O34.513,O34.519,O34.521,O34.522,O34.523,O34.529,
    O34.531,O34.532,O34.533,O34.539,O34.591,O34.592,O34.593,O34.599,O34.60,O34.61,O34.62,O34.63,O34.70,O34.71,O34.72,
    O34.73,O34.80,O34.81,O34.82,O34.83,O34.90,O34.91,O34.92,O34.93,O35.7XX0,O35.8XX0,O36.0110,O36.0120,O36.0130,
    O36.0190,O36.0910,O36.0920,O36.0930,O36.0990,O36.1110,O36.1120,O36.1130,O36.1190,O36.1910,O36.1920,O36.1930,
    O36.1990,O36.5110,O36.5120,O36.5130,O36.5190,O36.5910,O36.5920,O36.5930,O36.5990,O36.8120,O36.8130,O36.8190,
    O36.8210,O36.8220,O36.8230,O36.8290,O36.8910,O36.8920,O36.8930,O36.8990,O41.1010,O41.1020,O41.1030,O41.1090,
    O41.1210,O41.1220,O41.1230,O41.1290,O41.1410,O41.1420,O41.1430,O41.1490,O42.00,O42.011,O42.012,O42.013,O42.02,
    O42.10,O42.111,O42.112,O42.113,O42.12,O43.011,O43.019,O43.101,O43.102,O43.103,O43.199,O43.211,O43.212,O43.213,
    O43.221,O43.222,O43.223,O43.231,O43.232,O43.233,O43.239,O43.811,O43.812,O43.813,O43.819,O43.91,O43.92,O43.93,
    O44.00,O44.01,O44.02,O44.03,O44.10,O44.11,O44.12,O44.13,O45.001,O45.002,O45.003,O45.011,O45.012,O45.013,O45.021,
    O45.022,O45.023,O45.091,O45.092,O45.093,O45.91,O45.92,O45.93,O46.001,O46.002,O46.003,O46.009,O46.011,O46.012,
    O46.013,O46.019,O46.021,O46.022,O46.023,O46.029,O46.091,O46.092,O46.093,O46.099,O46.90,O46.91,O46.92,O46.93,O47.00,
    O47.02,O47.03,O47.1,O47.9,O48.0,O48.1,O60.00,O60.02,O60.03,O61.0,O61.1,O61.9,O62.0,O62.1,O62.2,O62.3,O62.4,O62.9,
    O63.0,O63.1,O63.2,O63.9,O65.4,O65.5,O65.9,O66.0,O66.1,O66.40,O66.5,O66.8,O66.9,O67.0,O67.8,O67.9,O68,O70.0,O70.1,
    O70.2,O70.3,O70.4,O70.9,O71.00,O71.02,O71.03,O71.1,O71.2,O71.3,O71.4,O71.5,O71.6,O71.7,O71.82,O71.89,O71.9,O72.0,
    O72.1,O72.2,O72.3,O73.0,O73.1,O74.1,O74.2,O74.3,O74.8,O74.9,O75.0,O75.1,O75.2,O75.3,O75.4,O75.5,O75.81,O75.89,O75.9,
    O76,O77.0,O80,O82,O85,O86.0,O86.11,O86.12,O86.13,O86.19,O86.20,O86.21,O86.22,O86.29,O86.4,O86.81,O86.89,O87.0,O87.1,
    O87.2,O87.3,O87.4,O87.8,O87.9,O88.011,O88.012,O88.013,O88.019,O88.02,O88.03,O88.111,O88.112,O88.113,O88.119,O88.12,
    O88.13,O88.211,O88.212,O88.213,O88.219,O88.22,O88.23,O88.311,O88.312,O88.313,O88.319,O88.32,O88.33,O88.811,O88.812,
    O88.813,O88.819,O88.82,O88.83,O89.09,O89.1,O89.2,O89.8,O89.9,O90.0,O90.1,O90.2,O90.3,O90.4,O90.5,O90.6,O90.81,
    O90.89,O90.9,O91.011,O91.012,O91.013,O91.019,O91.02,O91.111,O91.112,O91.113,O91.119,O91.12,O91.211,O91.212,O91.213,
    O91.219,O91.22,O91.23,O92.011,O92.012,O92.013,O92.019,O92.03,O92.111,O92.112,O92.113,O92.119,O92.13,O92.20,O92.29,
    O92.3,O92.5,O92.6,O92.70,O92.79,O94,O98.011,O98.012,O98.013,O98.019,O98.02,O98.03,O98.111,O98.112,O98.113,O98.119,
    O98.12,O98.13,O98.211,O98.212,O98.213,O98.219,O98.22,O98.23,O98.311,O98.312,O98.313,O98.319,O98.32,O98.33,O98.42,
    O98.43,O98.511,O98.512,O98.513,O98.519,O98.52,O98.53,O98.611,O98.612,O98.613,O98.619,O98.62,O98.63,O98.811,O98.812,
    O98.813,O98.819,O98.82,O98.83,O98.911,O98.912,O98.913,O98.919,O98.92,O98.93,O99.011,O99.012,O99.013,O99.019,O99.02,
    O99.03,O99.111,O99.112,O99.113,O99.119,O99.12,O99.13,O99.210,O99.211,O99.212,O99.213,O99.214,O99.215,O99.280,
    O99.281,O99.282,O99.283,O99.284,O99.285,O99.320,O99.321,O99.322,O99.323,O99.324,O99.325,O99.330,O99.331,O99.332,
    O99.333,O99.334,O99.335,O99.340,O99.341,O99.342,O99.343,O99.344,O99.345,O99.350,O99.351,O99.352,O99.353,O99.354,
    O99.355,O99.411,O99.412,O99.413,O99.419,O99.42,O99.43,O99.810,O99.814,O99.815,O99.834,O99.835,O99.840,O99.841,
    O99.842,O99.843,O99.844,O99.845,O99.89,Z33.1,Z33.2,Z34.00,Z34.80,Z34.90,Z36
PREGNANCY_CODES;

	/**
	 * @var PoolArea
	 */
	//private $poolArea;
	function __construct($pid = null)
	{
		$this->user = new User();
		$this->acl = new ACL();
		$this->setPatient($pid);
        $this->Immunizations = new Immunizations();
        $this->ActiveProblems = new ActiveProblems();
	}

	/**
	 * @return MatchaCUP
	 */
	public function setPatientModel()
	{
		if (!isset($this->p))
			return $this->p = MatchaModel::setSenchaModel('App.model.patient.Patient');
	}

	/**
	 * @return MatchaCUP
	 */
	public function setDocumentModel()
	{
		if ($this->d == null) {
			return $this->d = MatchaModel::setSenchaModel('App.model.patient.PatientDocuments');
		}
		return $this->d;
	}

	/**
	 * @return MatchaCUP
	 */
	public function setChartCheckoutModel()
	{
		if ($this->c == null) {
			return $this->c = MatchaModel::setSenchaModel('App.model.patient.PatientChartCheckOut');
		}
		return $this->c;
	}

	/**
	 * @return MatchaCUP
	 */
	public function setPatientEncounterModel()
	{
		if ($this->e == null) {
			$this->e = MatchaModel::setSenchaModel('App.model.patient.Encounter');
		}
		return $this->e;
	}

	/**
	 * @return MatchaCUP
	 */
	public function setPatientAccountModel()
	{
		if ($this->pa == null) {
			$this->pa = MatchaModel::setSenchaModel('App.model.patient.PatientAccount');
		}
		return $this->pa;
	}

	/**
	 * @param stdClass $params
	 * @param bool $fullname
	 *
	 * @return mixed
	 */
	public function getPatients($params, $fullname = true)
	{
		$this->setPatientModel();
		$Records = $this->p->load($params)->all();
		// Compile custom fields
		if ($fullname) {
			foreach ($Records as $Index => $Record) {
				$Records[$Index]['name'] = Person::fullname($Record['fname'], $Record['mname'], $Record['lname']);
			}
		}
		return $Records;
	}

	/**
	 * @param stdClass $params
	 *
	 * @return mixed
	 */
	public function savePatient($params)
	{
		$this->setPatientModel();
		$params->update_uid = isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : '0';
		$params->update_date = date('Y-m-d H:i:s');

		$new_patient = !isset($params->pid);

		$this->patient = (object)$this->p->save($params);
		$this->generateQrCodes($this->patient);
		if($new_patient){
			$this->generatePubpid($this->patient);
		}
		$this->createPatientDir($this->patient->pid);

		return $this->patient;
	}

	public function updatePatient($params)
	{
		return $this->p->save($params);
	}

	/**
	 * @param $pid
	 *
	 * @return mixed
	 */
	protected function setPatient($pid)
	{
		return $this->getPatientByPid($pid);
	}

	/**
	 * @param  $params
	 * @return array
	 */
	public function getPatient($params = null)
	{
		if(isset($params)){
			$this->patient = $this->p->load($params)->one();
		}
		return $this->patient;
	}

	/**
	 * @param $pid
	 * @param $include_accounts
	 * @param $account_facility_id
	 *
	 * @return mixed
	 */
	public function getPatientByPid($pid, $include_accounts = false, $account_facility_id = null)
	{
		$this->setPatientModel();
		$params = new stdClass();
		$params->filter[0] = new stdClass();
		$params->filter[0]->property = 'pid';
		$params->filter[0]->value = $pid;
		$this->patient = $this->p->load($params)->one();
		unset($params);

		if ($this->patient !== false) {
			$this->patient['pic'] = $this->patient['image'];
			$this->patient['age'] = $this->getPatientAge();
			$this->patient['name'] = $this->getPatientFullName();

			if($include_accounts === true && isset($account_facility_id)){
				$this->patient['account'] = (object) $this->getPatientAccountsByPidAndFacility($pid, $account_facility_id);
			}

			if (
				$this->patient['rating'] > 0 && isset($_SESSION['user']) && !ACL::hasPermission("allow_access_patient_rating_{$this->patient['rating']}")
			) {
				$this->errors[] = "No Authorized to Access Patient With Rating {$this->patient['rating']}";
				$this->patient = false;
			}

		}




		return $this->patient;
	}

	/**
	 * @param $pubpid
	 * @param $pubpid_issuer
	 *
	 * @return mixed
	 */
	public function getPatientByPublicId($pubpid, $pubpid_issuer = null)
	{
		$this->setPatientModel();
		$params = new stdClass();
		$params->filter[0] = new stdClass();
		$params->filter[0]->property = 'pubpid';
		$params->filter[0]->value = $pubpid;

        if(isset($pubpid_issuer)){
            $params->filter[1] = new stdClass();
            $params->filter[1]->property = 'pubpid_issuer';
            $params->filter[1]->value = $pubpid_issuer;
        }else{
            $this->p->setOrFilterProperties(['pubpid_issuer']);
            $params->filter[1] = new stdClass();
            $params->filter[1]->property = 'pubpid_issuer';
            $params->filter[1]->value = null;
            $params->filter[2] = new stdClass();
            $params->filter[2]->property = 'pubpid_issuer';
            $params->filter[2]->value = '';
        }

		$this->patient = $this->p->load($params)->one();
		if ($this->patient !== false) {
			$this->patient['pic'] = $this->patient['image'];
			$this->patient['age'] = $this->getPatientAge();
			$this->patient['name'] = $this->getPatientFullName();
		}
		unset($params);
		return $this->patient;
	}

	/**
	 * @param $username
	 *
	 * @return mixed
	 */
	public function getPatientByUsername($username)
	{
		$this->setPatientModel();
		$params = new stdClass();
		$params->filter[0] = new stdClass();
		$params->filter[0]->property = 'portal_username';
		$params->filter[0]->value = $username;
		$this->patient = $this->p->load($params)->one();
		if ($this->patient !== false) {
			$this->patient['pic'] = $this->patient['image'];
			$this->patient['age'] = $this->getPatientAge();
			$this->patient['name'] = $this->getPatientFullName();
		}

		unset($params);
		return $this->patient;
	}

	/**
	 * Return a patient record depending of its Guardian username
	 * @param $guardian_portal_username
	 *
	 * @return Patient Record
	 */
	public function getPatientByGuardian($guardian_portal_username)
	{
		$this->setPatientModel();
		$params = new stdClass();
		$params->filter[0] = new stdClass();
		$params->filter[0]->property = 'guardian_portal_username';
		$params->filter[0]->value = $guardian_portal_username;
		$this->patient = $this->p->load($params)->one();
		if ($this->patient !== false) {
			$this->patient['pic'] = $this->patient['image'];
			$this->patient['age'] = $this->getPatientAge();
			$this->patient['name'] = $this->getPatientFullName();
		}

		unset($params);
		return $this->patient;
	}

	/**
	 * Return a patient record depending of its Emergency username
	 * @param $emergency_portal_username
	 *
	 * @return Patient Record
	 */
	public function getPatientByEmergencyContact($emergency_portal_username)
	{
		$this->setPatientModel();
		$params = new stdClass();
		$params->filter[0] = new stdClass();
		$params->filter[0]->property = 'emergency_contact_portal_username';
		$params->filter[0]->value = $emergency_portal_username;
		$this->patient = $this->p->load($params)->one();
		if ($this->patient !== false) {
			$this->patient['pic'] = $this->patient['image'];
			$this->patient['age'] = $this->getPatientAge();
			$this->patient['name'] = $this->getPatientFullName();
		}

		unset($params);
		return $this->patient;
	}

	/**
	 * @param $pid
	 *
	 * @return mixed
	 */
	public function unsetPatient($pid)
	{
		$_SESSION['patient']['pid'] = null;
		if ($pid != null) {
			$this->patientChartInByPid($pid);
		}
		return;
	}

	/**
	 * @param $pid
	 *
	 * @return array
	 */
	public function getPatientDemographicDataByPid($pid)
	{
		$this->setPatient($pid);
		return $this->patient;
	}

	/**
	 * @param stdClass $params
	 *
	 * @return array
	 */
	public function getPatientDemographicData(stdClass $params)
	{
		return $this->setPatient($params->pid);
	}

	public function generateQrCodes(&$patient){
		$buff = [];
		if (isset($patient->fullname)) {
			$buff['pid'] = $patient->pid;
			$patient->qrcode = $buff['qrcode'] = $this->createPatientQrCode($patient->pid, $patient->fullname);
		} else if (isset($params->fname) && isset($params->mname) && isset($params->lname)) {
			$buff['pid'] = $patient->pid;
			$patient->qrcode = $buff['qrcode'] = $this->createPatientQrCode($patient->pid,
				Person::fullname($patient->fname, $patient->mname, $patient->lname)
			);
		}
		if(!empty($buff)){
			$this->p->save((object)$buff);
		}
	}

	public function generatePubpid(&$patient){
		if(isset($patient->pubpid) && $patient->pubpid != '') return;

		$pubpid = Globals::getGlobal('record_number_token');

		if($pubpid === false) return;

		$additional_tokens = [
			'{PID}' => $patient->pid,
			'{PAD_6_PID}' => str_pad($patient->pid, 6, '0', STR_PAD_LEFT),
			'{PAD_8_PID}' => str_pad($patient->pid, 8, '0', STR_PAD_LEFT),
			'{PAD_10_PID}' => str_pad($patient->pid, 10, '0', STR_PAD_LEFT),
			'{PAD_15_PID}' => str_pad($patient->pid, 15, '0', STR_PAD_LEFT),
			'{SEX}' => strtoupper($patient->sex),
			'{LAST_NAME_START_LETTER}' => strtoupper($patient->lname[0]),
			'{FIRST_NAME_START_LETTER}' => strtoupper($patient->fname[0]),
		];

		$pubpid = Tokens::StringReplace($pubpid, $additional_tokens);

		if($pubpid !== ''){
            $p_object = (object)['pid' => $patient->pid, 'pubpid' => $pubpid];
            $patient->pubpid = $pubpid;
            if(!isset($patient->pubaccount) || $patient->pubaccount === ''){
                $p_object->pubaccount = $pubpid;
                $patient->pubaccount = $p_object->pubaccount;
            }
			$this->p->save($p_object);
		}

	}

	/**
	 * @param $name
	 *
	 * @return array
	 */
	public function createNewPatientOnlyName($name)
	{
		$params = new stdClass();
		$foo = explode(' ', $name);
		$params->fname = trim($foo[0]);
		$params->mname = '';
		$params->lname = '';
		$params->sex = 'U';
		if (count($foo) == 2) {
			$params->lname = trim($foo[1]);
		} elseif (count($foo) >= 3) {
			$params->mname = (isset($foo[1])) ? trim($foo[1]) : '';
			unset($foo[0], $foo[1]);
			$params->lname = '';
			foreach ($foo as $fo) {
				$params->lname .= $params->lname . ' ' . $fo . ' ';
			}
			$params->lname = trim($params->lname);
		}
		$params->create_uid = $_SESSION['user']['id'];
		$params->create_date = date('Y-m-d H:i:s');
		$params->update_date = date('Y-m-d H:i:s');
		$patient = $this->savePatient($params);
		return [
			'success' => true,
			'patient' => [
				'pid' => $patient->pid
			]
		];
	}

	/**
	 * @param stdClass $params
	 *
	 * @return mixed
	 */
	public function createNewPatient(stdClass $params)
	{
        $params->create_date = date('Y-m-d H:i:s');

		if (isset($params->insurance)) {
			$insurance = (object)$params->insurance;
		}

		$patient = $this->savePatient($params);

		if (isset($insurance)) {
			$insurance->pid = $patient->pid;
			include_once(ROOT . '/dataProvider/Insurance.php');
			$Insurance = new Insurance();
			$Insurance->addInsurance($insurance);
		}

		return $patient;
	}

	/**
	 * @param $params
	 *
	 * @return array
	 */
	public function getPatientSetDataByPid($params)
	{

		include_once(ROOT . '/dataProvider/PoolArea.php');
		$patient = $this->setPatient($params->pid);

		if ($patient === false) {
			return [
				'success' => false,
				'error' => implode(' & ', $this->errors)
			];
		}


		$poolArea = new PoolArea();

		$area = $poolArea->getCurrentPatientPoolAreaByPid($this->patient['pid']);
		if($area !== false){
			$chart = $this->patientChartOutByPid($this->patient['pid'], $area['area_id']);
		}else{
			$chart = null;
		}

		$_SESSION['patient']['pid'] = $this->patient['pid'];

		return [
			'success' => true,
			'patient' => [
				'pid' => $this->patient['pid'],
				'pubpid' => $this->patient['pubpid'],
				'name' => $this->getPatientFullName(),
				'pic' => $this->patient['image'],
				'sex' => $this->getPatientSex(),
				'dob' => $this->getPatientDOB(),
				'age' => $this->getPatientAge(),
				'area' => $area !== false ? $area['poolArea'] : '',
				'priority' => (empty($area) ? null : $area['priority']),
				'rating' => (isset($this->patient['rating']) ? $this->patient['rating'] : 0),
				'covid_status' => $this->Immunizations->getCovidVaccineStatusByPid($this->patient['pid']),
				'is_pregnant' => $this->ActiveProblems->isActiveByPidAndCodes($this->patient['pid'], $this->pregnancy_codes),
				'record' => $this->patient
			],
			'chart' => [
				'readOnly' => (isset($chart) && $chart->read_only == '1') || '0',
				'overrideReadOnly' => $this->acl->hasPermission('override_readonly'),
				'outUser' => isset($chart->outChart->uid) ? $this->user->getUserFullNameById($chart->outChart->uid) : 0,
				'outArea' => isset($chart->outChart->pool_area_id) ? $poolArea->getAreaTitleById($chart->outChart->pool_area_id) : 0,
			]
		];
	}

	/**
	 * @return array
	 */
	public function getPatientAge()
	{
		return $this->getPatientAgeByDOB($this->patient['DOB']);
	}

	/**
	 * @param $dob
	 * @param string $from
	 *
	 * @return array
	 * @internal param $birthday
	 */
	public static function getPatientAgeByDOB($dob, $from = 'now')
	{
		if (isset($dob) && $dob != '0000-00-00 00:00:00') {
			$from = $from == 'now' ? new DateTime(date('Y-m-d')) : $from;
			$t = new DateTime($dob);
			$age['days'] = $t->diff($from)->d;
			$age['months'] = $t->diff($from)->m;
			$age['years'] = $t->diff($from)->y;
			if ($age['years'] >= 2) {
				$ageStr = $age['years'] . ' yrs';
			} else {
				if ($age['years'] >= 1) {
					$ageStr = 12 + $age['months'] . ' mos';
				} else {
					if ($age['months'] > 1) {
						$ageStr = $age['months'] . ' mos';
					} elseif ($age['months'] == 1) {
						$ageStr = $age['months'] . ' mo';
					} elseif ($age['days'] > 1) {
						$ageStr = $age['days'] . ' days';
					} else {
						$ageStr = $age['days'] . ' day';
					}
				}
			}
			return [
				'DMY' => $age,
				'str' => $ageStr
			];
		} else {
			return [
				'DMY' => [
					'years' => 0,
					'months' => 0,
					'days' => 0
				],
				'str' => '<span style="color:red">Age</span>'
			];
		}
	}

	/**
	 * @param stdClass $params
	 *
	 * @return object
	 */
	public function setPatientRating(stdClass $params)
	{
		$this->setPatientModel();
		return $this->p->save($params);
	}

	public function createPatientQrCode($pid, $fullname)
	{
		$data = '{"name":"' . $fullname . '","pid":' . $pid . ',"ehr": "mdtimeline"}';
		include_once (ROOT . '/lib/phpqrcode/qrlib.php');
		ob_start();
		QRCode::png($data, false, 'Q', 2, 2);
		$imageString = base64_encode(ob_get_contents());
		ob_end_clean();
		return 'data:image/jpeg;base64,' . $imageString;
	}

	/**
	 * @param bool|string $capitalize
	 *
	 * @return string
	 */
	public function getPatientFullName($capitalize = true)
	{
		return Person::fullname($this->patient['fname'], $this->patient['mname'], $this->patient['lname'], $capitalize);
	}

	/**
	 * @param $pid
	 *
	 * @return string
	 */
	public function getPatientFullNameByPid($pid)
	{
		$this->setPatientModel();
		$p = $this->p->sql("SELECT pid,fname,mname,lname FROM patient WHERE pid = '$pid'")->one();
		return Person::fullname($p['fname'], $p['mname'], $p['lname']);
	}

	/**
	 * @param $pid
	 *
	 * @return string
	 */
	public function getPatientFullAddressByPid($pid)
	{
		$patientContact = new PatientContacts();
		$record = $patientContact->getSelfContact($pid);
		if (isset($record)) {
			return Person::fulladdress(
				$record['street_mailing_address'],
				null,
				$record['city'],
				$record['state'],
				$record['zip']
			);
		}
	}

	public function patientLiveSearch(stdClass $params)
	{
		$this->setPatientModel();
		$conn = Matcha::getConn();
		$whereValues = [];
		$where = [];
		$queries = explode(',', $params->query);

		$single_query = count($queries) === 1;

		foreach ($queries as $index => $query) {
			$query = trim($query);

			$whereValues[':fname' . $index] = $query . '%';
			$whereValues[':lname' . $index] = $query . '%';
			$whereValues[':mname' . $index] = $query . '%';

			if(preg_match('/\d{3}-\d{3}-\d{4}/', $query)){
				$phone_query = " OR phone_home = :phone_home_{$index} OR phone_mobile = :phone_mobile_{$index}";
				$whereValues[':phone_home_' . $index] = $query;
				$whereValues[':phone_mobile_' . $index] = $query;
			}else{
				$phone_query = '';
			}

            if(is_numeric($query)){
                $lic_query = " OR drivers_license = :drivers_license_{$index}";
                $whereValues[':drivers_license_' . $index] = $query;
            }else{
                $lic_query = '';
            }

			if ($single_query) {
                if (preg_match('/^(.)-(.*)-(.{2})$/', $query, $matches)) {
					$whereValues[':reg_pubpid' . $index] = '^' . $matches[1] . '-' . str_pad($matches[2], 15, '0', STR_PAD_LEFT) . '-' . $matches[3] . '$';
				} elseif (preg_match('/^(.)-(.*)$/', $query, $matches)) {
					$whereValues[':reg_pubpid' . $index] = '^' . $matches[1] . '-' . str_pad($matches[2], 15, '0', STR_PAD_LEFT);
				} elseif (preg_match('/(.*)-(.{2})$/', $query, $matches)) {
					$whereValues[':reg_pubpid' . $index] = str_pad($matches[1], 15, '0', STR_PAD_LEFT) . '-' . $matches[2];
				} else {
					$whereValues[':reg_pubpid' . $index] = trim($query, '-') . '-.{2}$';
				}
                $whereValues[':pubpid' . $index] = $query;

                $where[] = " (pubpid REGEXP :reg_pubpid{$index} OR pubpid = :pubpid{$index} OR fname LIKE :fname{$index} OR lname LIKE :lname{$index} OR mname LIKE :mname{$index} OR DOB LIKE :DOB{$index} OR pid LIKE :pid{$index} OR SS LIKE :ss{$index} {$phone_query} {$lic_query}) ";

            } else {
                $where[] = " (fname LIKE :fname{$index} OR lname LIKE :lname{$index} OR mname LIKE :mname{$index} OR DOB LIKE :DOB{$index} OR pid LIKE :pid{$index} OR SS LIKE :ss{$index} {$phone_query} {$lic_query}) ";
			}

			$whereValues[':DOB' . $index] = $query . '%';
			$whereValues[':pid' . $index] = $query . '%';
			$whereValues[':ss' . $index] = '%' . $query;
		}
		$sth = $conn->prepare('SELECT pid, pubpid, fname, lname, mname,
                                            DOB, SS, sex, email, phone_mobile, phone_home, phone_work
 								        FROM `patient` WHERE ' . implode(' AND ', $where) . ' LIMIT 300');
		$sth->execute($whereValues);
		$patients = $sth->fetchAll(PDO::FETCH_ASSOC);
		return [
			'totals' => count($patients),
			'rows' => array_slice($patients, $params->start, $params->limit)
		];
	}

	/**
	 * createPatientDir
	 * Creates the patient directory to store
	 * documents and media into the file system
	 *
	 * @param $pid
	 * @return bool
	 */
	private function createPatientDir($pid)
	{
		try {
			$path = site_path . '/patients/' . $pid;
			if (!file_exists($path)) {
				if (mkdir($path, 0775, true)) {
					chmod($path, 0775);
				} else {
					throw new Exception('Could not create the directory for the patient.');
				}
			} else {
				if (!is_writable($path)) chmod($path, 0775);
			}
			return true;
		} catch (Exception $Error) {
			error_log($Error->getMessage());
			return $Error->getMessage();
		}
	}

	public function getPatientAddressById($pid)
	{
		$patientContact = new PatientContacts();
		$record = $patientContact->getSelfContact($pid);
		$address = '';
		if (isset($record)) {
			$address = $record['address'] . ' <br>' . $record['city'] . ',  ' . $record['state'] . ' ' . $record['country'];
		}
		return $address;
	}

	public function getPatientArrivalLogWarningByPid($pid)
	{
		$this->setPatientModel();
		$alert = $this->p->sql("SELECT pid FROM patient WHERE pid = '$pid' AND (sex IS NULL OR DOB IS NULL)")->one();
		return $alert !== false;
	}

	/** Patient Charts
	 *
	 * @param $pid
	 * @param $pool_area_id
	 *
	 * @return object|stdClass
	 */
	public function patientChartOutByPid($pid, $pool_area_id)
	{
		$this->setChartCheckoutModel();
		$outChart = $this->isPatientChartOutByPid($pid);
		$params = new stdClass();
		$params->pid = $pid;
		$params->uid = $_SESSION['user']['id'];
		$params->chart_out_time = date('Y-m-d H:i:s');
		$params->pool_area_id = $pool_area_id;
		$params->read_only = $outChart === false ? '0' : '1';
		$params = (object)$this->c->save($params);
		$params->outChart = $outChart;
		return $params;
	}

	public function patientChartInByPid($pid)
	{
		$this->setChartCheckoutModel();
		$filters = new stdClass();
		$filters->filter[0] = new stdClass();
		$filters->filter[0]->property = 'uid';
		$filters->filter[0]->value = $_SESSION['user']['id'];
		$filters->filter[2] = new stdClass();
		$filters->filter[2]->property = 'pid';
		$filters->filter[2]->value = $pid;
		$filters->filter[3] = new stdClass();
		$filters->filter[3]->property = 'chart_in_time';
		$filters->filter[3]->value = null;
		$chart = $this->c->load($filters)->one();
		unset($filters);
		if ($chart !== false) {
			$chart = (object)$chart;
			$chart->chart_in_time = date('Y-m-d H:i:s');
			return $this->c->save($chart);
		}
		return false;
	}

	public function patientChartInByUserId($uid)
	{
		$this->setChartCheckoutModel();
		$filters = new stdClass();
		$filters->filter[0] = new stdClass();
		$filters->filter[0]->property = 'uid';
		$filters->filter[0]->value = $uid;
		$filters->filter[1] = new stdClass();
		$filters->filter[1]->property = 'chart_in_time';
		$filters->filter[1]->value = null;
		$chart = $this->c->load($filters)->one();
		unset($filters);
		if ($chart !== false) {
			$chart = (object)$chart;
			$chart->chart_in_time = date('Y-m-d H:i:s');
			return $this->c->save($chart);
		}
		return false;
	}

	public function isPatientChartOutByPid($pid)
	{
		$this->setChartCheckoutModel();
		$filters = new stdClass();
		$filters->filter[0] = new stdClass();
		$filters->filter[0]->property = 'pid';
		$filters->filter[0]->value = $pid;
		$filters->filter[1] = new stdClass();
		$filters->filter[1]->property = 'chart_in_time';
		$filters->filter[1]->value = null;
		$result = $this->c->load($filters)->one();
		unset($filters);
		return $result;
	}

	public function getDOBByPid($pid)
	{
		$this->setPatientModel();
		$p = $this->p->load(['pid' => $pid])->one();
		return $p['DOB'];
	}

	public function getPatientDOB()
	{
		return $this->patient['DOB'];
	}

	public function getPatientDOBByPid($pid)
	{
		return $this->getDOBByPid($pid);
	}

	public function getPatientAgeByPid($pid)
	{
		return $this->getPatientAgeByDOB($this->getDOBByPid($pid));
	}

	public function getPatientPid()
	{
		return $this->patient['pid'];
	}

	public function getPatientPubpid()
	{
		return $this->patient['pubpid'];
	}

	public function getPatientSex()
	{
		return $this->patient['sex'];
	}

	public function getPatientSS()
	{
		return $this->patient['SS'];
	}

	public function getPatientSexByPid($pid)
	{
		$this->setPatientModel();
		$p = $this->p->load(['pid' => $pid])->one();
		return $p['sex'];
	}

	public function getPatientSexIntByPid($pid)
	{
		return (strtolower($this->getPatientSexByPid($pid)) == 'female' ? 1 : 2);
	}

	public function getPatientPregnantStatusByPid($pid)
	{
		$this->setPatientEncounterModel();
		$p = $this->e->sql("SELECT e.* FROM encounters as e WHERE e.eid = (
							SELECT  MAX(ee.eid) as eid FROM encounters as ee WHERE ee.pid = '$pid')")->one();
		return $p['review_pregnant'];
	}

	public function getPatientActiveProblemsById($pid, $tablexx, $columnName)
	{
		$records = $this->p->sql("SELECT $columnName FROM $tablexx WHERE pid ='$pid'")->all();
		$rows = [];
		foreach ($records as $record) {
			if (!isset($record['end_date']) || $record['end_date'] != null || $record['end_date'] != '0000-00-00 00:00:00') {
				$rows[] = $record;
			}
		}
		return $records;
	}

	public function getPatientDocuments(stdClass $params)
	{
		$this->setDocumentModel();
		$docs = $this->d->load($params)->all();
		if (isset($docs['data'])) {
			foreach ($docs['data'] AS $index => $row) {
				$docs['data'][$index]['user_name'] = $this->user->getUserNameById($row['uid']);
			}
		}
		return $docs;
	}

	public function getMeaningfulUserAlertByPid(stdClass $params)
	{
		$record = [];

		$this->setPatientModel();
		$patient = $this->p->load($params->pid, ['pid', 'language', 'race', 'ethnicity', 'fname', 'lname', 'sex', 'DOB',])->one();
		foreach ($patient as $key => $val) {
			$val = ($val == null || $val == '') ? false : true;
			$record[] = [
				'name' => $key,
				'val' => $val
			];
		}
		return $record;
	}

	public function getPatientPhotoSrc()
	{
		return $this->patient['image'];
	}

	public function getPatientPhotoSrcIdByPid($pid)
	{
		$this->setPatientModel();
		$patient = $this->p->load($pid)->one();
		return $patient['image'];
	}

	/**
	 * @param $params
	 * @return mixed
	 */
	public function getPossibleDuplicatesByFilters($params)
	{
		$this->setPatientModel();

//		$sql = "SELECT *
//				  FROM `patient`
// 				 WHERE `fname` SOUNDS LIKE 'sudipto'"

		return [];
	}

	/**
	 * @param $params
	 * @param $includeDateOfBirth
	 *
	 * @return mixed
	 */
	public function getPossibleDuplicatesByDemographic($params, $includeDateOfBirth = false)
	{
		$this->setPatientModel();

		$sql_params = [
			':fname' => $params->fname,
			':lname' => $params->lname,
		];

		if (isset($params->policy_numer)) {
			$sql_params[':policy_numer'] = $params->policy_numer;
			$sql[] = 'SELECT * FROM `patient` WHERE `pid` IN (SELECT pid FROM `patient_insurances` WHERE policy_numner = :policy_numer)';
		}

		$sql[] = "SELECT * FROM `patient` WHERE `fname` SOUNDS LIKE :fname AND `lname` SOUNDS LIKE :lname";


//		if ($includeDateOfBirth && isset($params->DOB)) {
//			$sql_params[':DOB'] = $params->DOB;
//			$sql = " AND `DOB` = :DOB";
//		}
//
//		if (isset($params->pid) && $params->pid != 0) {
//			$sql_params[':pid'] = $params->pid;
//			$sql .= " AND `pid` != :pid";
//		}

		$sql = implode(' UNION ', $sql);

		$results = $this->p->sql($sql)->all($sql_params);
		foreach ($results as $index => $record) {

			$results[$index]['name'] = Person::fullname(
				$record['fname'],
				$record['mname'],
				$record['lname']
			);

			$results[$index]['fulladdress'] = Person::fulladdress(
				isset($record['postal_address']) ? $record['postal_address'] : '',
				isset($record['postal_address_cont']) ? $record['postal_address_cont'] : '',
				isset($record['postal_city']) ? $record['postal_city'] : '',
				isset($record['postal_state']) ? $record['postal_state'] : '',
				isset($record['postal_zip']) ? $record['postal_zip'] : ''
			);
			$results[$index]['phones'] = (isset($record['phone_home']) ? $record['phone_home'] : '000-000-0000') . ' (h) | ';
			$results[$index]['phones'] .= (isset($record['phone_mobile']) ? $record['phone_mobile'] : '000-000-0000') . ' (m)';

		}
		return [
			'total' => count($results),
			'data' => $results
		];
	}

	public function search($params)
	{

		$concepts = [];

		if (!isset($params->date_from) || !isset($params->date_to)) {
			return [];
		}

		$params->date_from = $params->date_from . ' 00:00:00';
		$params->date_to = $params->date_to . ' 23:59:59';
		$patient_in = '';
		$sorters = [];

		if (isset($params->sort) && is_array($params->sort)) {
			foreach ($params->sort as $sort) {
				$direction = isset($sort->direction) ? $sort->direction : 'ASC';
				$sorters[$sort->property] = $direction;
			}
		}

		if (
			isset($params->lname) ||
			isset($params->fname) ||
			isset($params->ageFrom) ||
			isset($params->ageTo) ||
			isset($params->sex) ||
			isset($params->race) ||
			isset($params->ethnicity) ||
			isset($params->marital_status) ||
			isset($params->language) ||
			isset($params->phone_publicity)

		) {
			$where = [];

			if (isset($params->ageTo)) {
				$params->ageTo++;
				$dob_start_time = new DateTime();
				$dob_start_time->sub(new DateInterval("P{$params->ageTo}Y"));
				$dob_start_time->add(new DateInterval("P1D"));
				$dob_start = $dob_start_time->format('Y-m-d');
				$where[] = "(p.DOB >= '$dob_start')";
			}
			if (isset($params->ageFrom)) {
				$dob_stop_time = new DateTime();
				$dob_stop_time->sub(new DateInterval("P{$params->ageFrom}Y"));
				$dob_stop = $dob_stop_time->format('Y-m-d');
				$where[] = "(p.DOB <= '$dob_stop')";
			}

			if (isset($params->fname)) {
				$where[] = "(p.fname LIKE '{$params->fname}%')";
			}
			if (isset($params->lname)) {
				$where[] = "(p.lname LIKE '{$params->lname}%')";
			}
			if (isset($params->sex)) {
				$or = [];
				foreach ($params->sex as $sex) {
					$or[] = "p.sex = '{$sex}'";
				}
				$where[] = '(' . implode(' OR ', $or) . ')';
			}
			if (isset($params->race)) {
				$or = [];
				foreach ($params->race as $race) {
					$or[] = "p.race = '{$race}'";
				}
				$where[] = '(' . implode(' OR ', $or) . ')';
			}
			if (isset($params->ethnicity)) {
				$or = [];
				foreach ($params->ethnicity as $ethnicity) {
					$or[] = "p.ethnicity = '{$ethnicity}'";
				}
				$where[] = '(' . implode(' OR ', $or) . ')';
			}
			if (isset($params->marital_status)) {
				$or = [];
				foreach ($params->marital_status as $marital_status) {
					$or[] = "p.marital_status = '{$marital_status}'";
				}
				$where[] = '(' . implode(' OR ', $or) . ')';
			}
			if (isset($params->language)) {
				$or = [];
				foreach ($params->language as $language) {
					$or[] = "p.language = '{$language}'";
				}
				$where[] = '(' . implode(' OR ', $or) . ')';
			}
			if (isset($params->phone_publicity)) {
				$or = [];
				foreach ($params->phone_publicity as $phone_publicity) {
					$or[] = "p.phone_publicity = '{$phone_publicity}'";
				}
				$where[] = '(' . implode(' OR ', $or) . ')';
			}

			if (!empty($where)) {
				$where = implode(' AND ', $where);
				$sql = "SELECT p.pid FROM patient as p WHERE {$where}";
				$patients = $this->p->sql($sql)->all();

				if (!empty($patients)) {
					$pid_list = [];
					foreach ($patients as $patient) {
						$pid_list[] = $patient['pid'];
					}
					$pid_list = implode(',', $pid_list);
					$patient_in = "AND patient.pid in ({$pid_list})";
				}
			}
		}

		if (isset($params->providers) && is_array($params->providers)) {

			$filter_providers = true;

			$date = "(enc.service_date >= '{$params->date_from}' AND enc.service_date <= '{$params->date_to}')";

			if (isset($pid_list)) {
				$patient_in = "AND enc.pid in ({$pid_list})";
			}

			$where = [];
			foreach ($params->providers as $provider) {
				$where[] = "(enc.provider_uid = '{$provider}')";
			}
			$where = implode(' OR ', $where);

			$sort = '';
			if (isset($sorters['providers'])) {
				$sort = 'ORDER BY u.lname';
			}

			$concepts[] = "SELECT enc.pid,
								  enc.service_date as service_date,
 								  GROUP_CONCAT(CONCAT(u.lname, ', ', u.fname, ' ', u.mname) SEPARATOR '<br>') AS providers,
                                  '' as allergies,
                                  '' as problems,
                                  '' as medications,
 								  '' as lab_results
							 FROM encounters AS enc
        					 JOIN users AS u ON enc.provider_uid = u.id AND u.id IS NOT NULL 
        					WHERE ({$date}) AND ({$where}) {$patient_in} GROUP BY enc.pid {$sort}";
		}

		if (isset($params->allergy_codes)) {

			$filter_allergies = true;

			$date = "(aller.create_date >= '{$params->date_from}' AND aller.create_date <= '{$params->date_to}')";

			if (isset($pid_list)) {
				$patient_in = "AND aller.pid in ({$pid_list})";
			}

			$where = [];
			foreach ($params->allergy_codes as $allergy_code) {
				$where[] = "(aller.allergy_code = '{$allergy_code}')";
			}
			$where = implode(' OR ', $where);

			$sort = '';
			if (isset($sorters['allergies'])) {
				$sort = 'ORDER BY aller.allergy';
			}

			$concepts[] = "SELECT aller.pid,
								  aller.create_date as service_date,
								  '' as providers,
 								  GROUP_CONCAT(CONCAT(aller.allergy, ' - ', aller.severity, ' - ', aller.status) SEPARATOR '<br>') AS allergies,
                                  '' as problems,
                                  '' as medications,
 								  '' as lab_results
							 FROM patient_allergies AS aller
							WHERE ({$date}) AND ({$where}) {$patient_in} GROUP BY aller.pid {$sort}";
		}

		if (isset($params->problem_codes)) {

			$filter_problems = true;

			$date = "(pro.create_date >= '{$params->date_from}' AND pro.create_date <= '{$params->date_to}')";

			if (isset($pid_list)) {
				$patient_in = "AND pro.pid in ({$pid_list})";
			}

			$where = [];
			foreach ($params->problem_codes as $problem_code) {
				$where[] = "(pro.code = '{$problem_code}')";
			}
			$where = implode(' OR ', $where);

			$sort = '';
			if (isset($sorters['problems'])) {
				$sort = 'ORDER BY pro.code_text';
			}

			$concepts[] = "SELECT pro.pid,
								  pro.create_date as service_date,
 								  '' as providers,
 								  '' as allergies,
 								  GROUP_CONCAT(CONCAT(pro.code_text , ' - ', pro.status) SEPARATOR '<br>') AS problems,
 								  '' as medications,
 								  '' as lab_results 								  
							 FROM patient_active_problems AS pro
							WHERE ({$date}) AND ({$where}) {$patient_in} GROUP BY pro.pid {$sort}";
		}

		if (isset($params->medication_codes)) {

			$filter_medications = true;

			$date = "(med.begin_date >= '{$params->date_from}' AND med.begin_date <= '{$params->date_to}')";

			if (isset($pid_list)) {
				$patient_in = "AND med.pid in ({$pid_list})";
			}

			$where = [];
			foreach ($params->medication_codes as $medication_code) {
				$where[] = "(med.RXCUI = '{$medication_code}')";
			}
			$where = implode(' OR ', $where);

			$sort = '';
			if (isset($sorters['medications'])) {
				$sort = 'ORDER BY med.STR';
			}

			$concepts[] = "SELECT med.pid,
								  med.begin_date as service_date,
								  '' as providers,
 								  '' as allergies,
 								  '' as problems,
 								  GROUP_CONCAT(CONCAT(med.STR , ' - ', med.directions) SEPARATOR '<br>') AS medications,
 								  '' as lab_results 									
							 FROM patient_medications AS med
					        WHERE ({$date}) AND ({$where}) {$patient_in} GROUP BY med.pid {$sort}";
		}

		if (isset($params->lab_results) && is_array($params->lab_results)) {

			$filter_lab_results = true;

			$date = "(por.create_date >= '{$params->date_from}' AND por.create_date <= '{$params->date_to}')";

			if (isset($pid_list)) {
				$patient_in = "AND por.pid in ({$pid_list})";
			}

			$where = [];
			foreach ($params->lab_results as $lab_result) {
				$where[] = "(poro.code = '{$lab_result->lab_code}' AND poro.value {$lab_result->operator} '{$lab_result->value}')";
			}
			$where = implode(' OR ', $where);

			$sort = '';
			if (isset($sorters['lab_results'])) {
				$sort = 'ORDER BY poro.code';
			}

			$concepts[] = "SELECT por.pid,
								  por.create_date as service_date,
 								  '' as providers,
 								  '' as allergies,
 								  '' as problems,
 								  '' as medications,
 								  GROUP_CONCAT(CONCAT(poro.code, ' - ', poro.code_text , ' = ', poro.value) SEPARATOR '<br>') AS lab_results
					         FROM patient_order_results_observations AS poro
					    	 JOIN patient_order_results AS por ON por.id = poro.result_id AND por.pid IS NOT NULL
					    WHERE ({$date}) AND ({$where}) {$patient_in} GROUP BY por.pid {$sort}";
		}

		if (empty($concepts) && isset($pid_list)) {

			$date = "(p.create_date >= '{$params->date_from}' AND p.create_date <= '{$params->date_to}')";
			$date = '';

			$concepts[] = "SELECT p.pid,
								  p.create_date as service_date,
 								  '' as providers,
 								  '' as allergies,
 								  '' as problems,
 								  '' as medications,
 								  '' as lab_results
					         FROM patient AS p  WHERE {$date} p.pid in ({$pid_list})";
		}

		if (empty($concepts)) {
			return [];
		}

		$concepts = implode(' UNION ', $concepts);

		$container = [];

		if (isset($filter_providers)) {
			$container[] = "container.providers !=''";
		}
		if (isset($filter_allergies)) {
			$container[] = "container.allergies !=''";
		}
		if (isset($filter_problems)) {
			$container[] = "container.problems !=''";
		}
		if (isset($filter_medications)) {
			$container[] = "container.medications !=''";
		}
		if (isset($filter_lab_results)) {
			$container[] = "container.lab_results !=''";
		}

		if (!empty($container)) {
			$container = implode(' AND ', $container);
		} else {
			$container = '';
		}

		$sql = "SELECT patient.pid,
 					   patient.pubpid,
 					   patient.fname,
 					   patient.mname,
 					   patient.lname,
 					   patient.sex,
 					   patient.race,
 					   patient.ethnicity,
 					   patient.DOB,
 					   patient.marital_status,
 					   patient.phone_publicity,
 					   patient.language,
 					   concepts.service_date,
 					   GROUP_CONCAT(concepts.providers SEPARATOR '') AS providers,
 					   GROUP_CONCAT(concepts.allergies SEPARATOR '') AS allergies,
 					   GROUP_CONCAT(concepts.problems SEPARATOR '') AS problems,
 					   GROUP_CONCAT(concepts.medications SEPARATOR '') AS medications,
 					   GROUP_CONCAT(concepts.lab_results SEPARATOR '') AS lab_results
				FROM ({$concepts}) as concepts 
				JOIN patient ON concepts.pid = patient.pid GROUP BY patient.pid";


		if ($container != '') {
			$sql = "SELECT * FROM ({$sql}) AS container WHERE {$container}";
		}

		$this->p->sql($sql);
		$total = $this->p->rowCount();

		$sorts = '';

		if (!empty($sorters)) {
			$buff = [];
			foreach ($sorters as $property => $direction) {
				switch ($property) {
					case 'pid':
					case 'pubpid':
					case 'fname':
					case 'lname':
					case 'sex':
					case 'race':
					case 'ethnicity':
					case 'DOB':
					case 'marital_status':
					case 'phone_publicity':
					case 'language':
						$buff[] = "patient.{$property} {$direction}";
						break;
					case 'service_date':
					case 'providers':
					case 'allergies':
					case 'problems':
					case 'medications':
					case 'lab_results':
						$buff[] = "concepts.{$property} {$direction}";
						break;
				}
			}
			if (!empty($buff)) {
				$sorts = 'ORDER BY ' . implode(', ', $buff);
			}
		}

		$sql = "SELECT patient.pid,
 					   patient.pubpid,
 					   patient.fname,
 					   patient.mname,
 					   patient.lname,
 					   patient.sex,
 					   patient.race,
 					   patient.ethnicity,
 					   patient.DOB,
 					   patient.marital_status,
 					   patient.phone_publicity,
 					   patient.language,
 					   concepts.service_date,
 					   GROUP_CONCAT(concepts.providers SEPARATOR '') AS providers,
 					   GROUP_CONCAT(concepts.allergies SEPARATOR '') AS allergies,
 					   GROUP_CONCAT(concepts.problems SEPARATOR '') AS problems,
 					   GROUP_CONCAT(concepts.medications SEPARATOR '') AS medications,
 					   GROUP_CONCAT(concepts.lab_results SEPARATOR '') AS lab_results
				FROM ({$concepts}) as concepts
				JOIN patient ON concepts.pid = patient.pid GROUP BY patient.pid {$sorts} LIMIT {$params->start}, {$params->limit}";

		if ($container != '') {
			$sql = "SELECT * FROM ({$sql}) AS container WHERE {$container}";
		}

		return [
			'total' => $total,
			'data' => $this->p->sql($sql)->all()
		];
	}

	public function getAuthorizedPersonsByPid($pid)
	{

		$results = [];

		$patient = $this->getPatientByPid($pid);
		$age_object = $this->getPatientAge();
		$is_adult = $age_object['DMY']['years'] >= 18;


		if($is_adult){
			$results[] = [
				'text' => $this->getPatientFullName() . ' (Patient)'
			];
		}else{
			if(isset($patient['mother_lname'])) {
				$results[] = [
					'text' => Person::fullname(
						$patient['father_fname'],
						$patient['father_mname'],
						$patient['father_lname']
					) . ' (Father)'
				];
			}
			if(isset($patient['mother_lname'])){
				$results[] = [
					'text' => Person::fullname(
						$patient['mother_fname'],
						$patient['mother_mname'],
						$patient['mother_lname']
					) . ' (Mother)'
				];
			}
		}

		if (isset($patient['authorized_01_lname'])){
			$results[] = [
				'text' => Person::fullname(
					$patient['authorized_01_fname'],
					$patient['authorized_01_mname'],
					$patient['authorized_01_lname']
				) . ' (Authorized Person 1)'
			];
		}

		if (isset($patient['authorized_02_lname'])){
			$results[] = [
				'text' => Person::fullname(
					$patient['authorized_02_fname'],
					$patient['authorized_02_mname'],
					$patient['authorized_02_lname']
				) . ' (Authorized Person 2)'
			];
		}

		if (isset($patient['authorized_03_lname'])){
			$results[] = [
				'text' => Person::fullname(
					$patient['authorized_03_fname'],
					$patient['authorized_03_mname'],
					$patient['authorized_03_lname']
				) . ' (Authorized Person 3)'
			];
		}

		return $results;
	}


	public function getRelatedRecordsByPid($pid){

		$sql = "
			SELECT * FROM (
				(
					SELECT pid, pubpid as recordNumber, email, sex, DOB as dob, fname, lname, 'FATHER' as relation FROM patient WHERE 
					(father_pid = :pid_1 AND DATE(DOB) > DATE_SUB(DATE(NOW()), INTERVAL 18 YEAR))
				) UNION (
					SELECT pid, pubpid as recordNumber, email, sex, DOB as dob, fname, lname, 'MOTHER' as relation FROM patient WHERE 
					(mother_pid = :pid_2 AND DATE(DOB) > DATE_SUB(DATE(NOW()), INTERVAL 18 YEAR))
				) UNION (
					SELECT pid, pubpid as recordNumber, email, sex, DOB as dob, fname, lname, 'GUARDIAN' as relation FROM patient WHERE 
					guardians_pid = :pid_3
				)
			) as results  group by pid
		";

		return $this->p->sql($sql)->all(['pid_1' => $pid, 'pid_2' => $pid, 'pid_3' => $pid]);
	}

	public function hashPid($pid){
		return MatchaUtils::encrypt($pid);
	}
	public function unHashPid($pid){
		return MatchaUtils::decrypt($pid);
	}

	// patient account stuff
	public function getPatientAccounts($params){
		$this->setPatientAccountModel();
		return $this->pa->load($params)->all();
	}
	public function addPatientAccount($params){
		$this->setPatientAccountModel();
		return $this->pa->save($params);
	}
	public function updatePatientAccount($params){
		$this->setPatientAccountModel();
		return $this->pa->save($params);
	}
	public function destroyPatientAccount($params){
		$this->setPatientAccountModel();
		return $this->pa->destroy($params);
	}
	public function getPatientAccountsByPidAndFacility($pid, $facility_id){
		$this->setPatientAccountModel();
		$this->pa->addFilter('pid', $pid);
		$this->pa->addFilter('facility_id', $facility_id);
		return $this->pa->load()->one();
	}

	public function getPatientTokenByPid($pid){

	    $patient = $this->getPatientByPid($pid);
        $tokens = [];

        if($patient === false){
            return $tokens;
        }

        $tokens['[PATIENT_NAME]'] = trim(sprintf('%s, %s %s', $patient['lname'],$patient['fname'],$patient['mname']));
        $tokens['[PATIENT_ID]'] = $patient['pid'];
        $tokens['[PATIENT_RECORD_NUMBER]'] = $patient['pubpid'];
        $tokens['[PATIENT_FIRST_NAME]'] = $patient['fname'];
        $tokens['[PATIENT_LAST_NAME]'] = $patient['lname'];
        $tokens['[PATIENT_MIDDLE_NAME]'] = $patient['mname'];
        $tokens['[PATIENT_SEX]'] = $patient['sex'];
        $tokens['[PATIENT_BIRTHDATE]'] = date('m/d/Y', strtotime($patient['DOB']));
        $tokens['[PATIENT_MARITAL_STATUS]'] = $patient['marital_status'];
        $tokens['[PATIENT_SOCIAL_SECURITY]'] = $patient['SS'];
        $tokens['[PATIENT_INTERFACE_NO]'] = $patient['interface_number'];
        $tokens['[PATIENT_DRIVERS_LICENSE]'] = $patient['drivers_license'];
        $tokens['[PATIENT_POSTAL_ADDRESS]'] = trim(sprintf('%s %s, %s, %s %s', $patient['postal_address'],$patient['postal_address_cont'],$patient['postal_city'], $patient['postal_state'], $patient['postal_zip']));
        $tokens['[PATIENT_POSTAL_ADDRESS_LINE_ONE]'] = $patient['postal_address'];
        $tokens['[PATIENT_POSTAL_ADDRESS_LINE_TWO]'] = $patient['postal_address_cont'];
        $tokens['[PATIENT_POSTAL_CITY]'] = $patient['postal_city'];
        $tokens['[PATIENT_POSTAL_STATE]'] = $patient['postal_state'];
        $tokens['[PATIENT_POSTAL_ZIP]'] = $patient['postal_zip'];
        $tokens['[PATIENT_POSTAL_COUNTRY]'] = $patient['postal_country'];
        $tokens['[PATIENT_PHYSICAL_ADDRESS]'] = trim(sprintf('%s %s, %s, %s %s', $patient['physical_address'],$patient['physical_address_cont'],$patient['physical_city'], $patient['physical_state'], $patient['physical_zip']));
        $tokens['[PATIENT_PHYSICAL_ADDRESS_LINE_ONE]'] = $patient['physical_address'];
        $tokens['[PATIENT_PHYSICAL_ADDRESS_LINE_TWO]'] = $patient['physical_address_cont'];
        $tokens['[PATIENT_PHYSICAL_CITY]'] = $patient['physical_city'];
        $tokens['[PATIENT_PHYSICAL_STATE]'] = $patient['physical_state'];
        $tokens['[PATIENT_PHYSICAL_ZIP]'] = $patient['physical_zip'];
        $tokens['[PATIENT_PHYSICAL_COUNTRY]'] = $patient['physical_country'];
        $tokens['[PATIENT_HOME_PHONE]'] = $patient['phone_home'];
        $tokens['[PATIENT_MOBILE_PHONE]'] = $patient['phone_mobile'];
        $tokens['[PATIENT_WORK_PHONE]'] = $patient['phone_work'];
        $tokens['[PATIENT_WORK_PHONE_EXT]'] = $patient['phone_work_ext'];
        $tokens['[PATIENT_EMAIL]'] = $patient['email'];
        $tokens['[PATIENT_MOTHERS_NAME]'] = trim(sprintf('%s, %s %s', $patient['mother_lname'],$patient['mother_fname'],$patient['mother_mname']));
        $tokens['[PATIENT_FATHERS_NAME]'] = trim(sprintf('%s, %s %s', $patient['father_lname'],$patient['father_fname'],$patient['father_mname']));
        $tokens['[PATIENT_GUARDIANS_NAME]'] = trim(sprintf('%s, %s %s', $patient['guardians_lname'],$patient['guardians_fname'],$patient['guardians_mname']));
        $tokens['[PATIENT_EMERGENCY_CONTACT]'] = trim(sprintf('%s, %s %s', $patient['emergency_contact_lname'],$patient['emergency_contact_fname'],$patient['emergency_contact_mname']));
        $tokens['[PATIENT_EMERGENCY_PHONE]'] = $patient['emergency_contact_phone'];
        $tokens['[PATIENT_PROVIDER]'] = '';
        $tokens['[PATIENT_PHARMACY]'] = '';
        $age = $this->getPatientAge();
        $tokens['[PATIENT_AGE]'] = $age['str'];
        $tokens['[PATIENT_OCCUPATION]'] = $patient['occupation'];
        $tokens['[PATIENT_EMPLOYER]'] = $patient['employer_name'];
        $tokens['[PATIENT_RACE]'] = $patient['race'];
        $tokens['[PATIENT_ETHNICITY]'] = $patient['ethnicity'];
        $tokens['[PATIENT_LANGUAGE]'] = $patient['language'];
        $tokens['[PATIENT_PICTURE]'] = $patient['image'];
        $tokens['[PATIENT_QRCODE]'] = $patient['qrcode'];



	    return $tokens;
    }
}

