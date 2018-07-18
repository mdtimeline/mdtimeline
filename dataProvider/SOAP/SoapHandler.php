<?php

class SoapHandler
{

	/**
	 * @var stdClass
	 */
	private $params;

	private $site;

	private $facility;

	private $provider;

	private $patient;

	private $vDate = '/\d{4}-\d{2}-\d{2}/';

	private $AuditLog;

	/**
	 * This is the Mapping variable
	 * @var array
	 */
	private $demographicsMap = [
		'Pid' => 'pid',
		'RecordNumber' => 'pubpid',
		'AccountNumber' => 'pubaccount',
		'Title' => 'title',
		'FirstName' => 'fname',
		'MiddleName' => 'mname',
		'LastName' => 'lname',
		'DateOfBirth' => 'DOB',
		'Sex' => 'sex',
		'MaritalStatus' => 'marital_status',
		'Race' => 'race',
		'Ethnicity' => 'ethnicity',
		//'Religion' => 'pid',
		'Language' => 'language',
		'DriverLicence' => 'drivers_license',
		'DriverLicenceState' => 'drivers_license_state',
		'DriverLicenceExpirationDate' => 'drivers_license_exp',
		'PhysicalAddressLineOne' => 'address',
		'PhysicalAddressLineTwo' => 'address_cont',
		'PhysicalCity' => 'city',
		'PhysicalState' => 'state',
		'PhysicalCountry' => 'country',
		'PhysicalZipCode' => 'zipcode',
		'HomePhoneNumber' => 'home_phone',
		'MobilePhoneNumber' => 'mobile_phone',
		'WorkPhoneNumber' => 'work_phone',
		'WorkPhoneExt' => 'work_phone_ext',
		'Email' => 'email',
		'ProfileImage' => 'image',
		'IsBirthMultiple' => 'birth_multiple',
		'BirthOrder' => 'birth_order',
		'Deceased' => 'deceased',
		'DeceaseDate' => 'death_date',
		'MothersFirstName' => 'mothers_name',
		'GuardiansFirstName' => 'guardians_fname',
		'GuardiansMiddleName' => 'guardians_mname',
		'GuardiansLastName' => 'guardians_lname',
		'EmergencyContactFirstName' => 'emergency_contact_fname',
		'EmergencyContactMiddleName' => 'emergency_contact_mname',
		'EmergencyContactLastName' => 'emergency_contact_lname',
		'EmergencyContactPhone' => 'emergency_contact_phone',
		'Occupation' => 'occupation',
		'Employer' => 'employer_name',
		'WebPortalUsername' => 'portal_username',
		'WebPortalPassword' => 'portal_password',
		'WebPortalAccess' => 'allow_patient_web_portal',
		'EmergencyPortalAllow' => 'allow_emergency_contact_web_portal',
		'EmergencyPortalAllowCda' => 'allow_emergency_contact_web_portal_cda',
		'EmergencyPortalUsername' => 'emergency_contact_portal_username',
		'EmergencyPortalPassword' => 'emergency_contact_portal_password',
		'GuardianPortalAllow' => 'allow_guardian_web_portal',
		'GuardianPortalAllowCda' => 'allow_guardian_web_portal_cda',
		'GuardianPortalUsername' => 'guardian_portal_username',
		'GuardianPortalPassword' => 'guardian_portal_password'
	];

	function constructor($params)
	{
		$this->params = $params;
		$this->site = isset($params->ServerSite) ? $params->ServerSite : 'default';
		$this->facility = isset($params->Facility) ? $params->Facility : '1';

		if (!defined('_GaiaEXEC')) define('_GaiaEXEC', 1);

		define('SITE', $params->ServerSite);

		include_once(str_replace('\\', '/', dirname(__FILE__)) . '/../../registry.php');
		include_once(ROOT . "/sites/{$this->site}/conf.php");
		include_once(ROOT . '/classes/MatchaHelper.php');
		include_once(ROOT . '/dataProvider/AuditLog.php');

		if (!isset($this->AuditLog)) $this->AuditLog = new AuditLog();
		if (isset($params->Provider)) $this->getProvider($params->Provider);
		if (isset($params->Patient)) $this->getPatient($params->Patient);
	}

	/**
	 * @return bool
	 */
	protected function isAuth()
	{
		require_once(ROOT . '/dataProvider/Applications.php');
		$Applications = new Applications();
		$access = $Applications->hasAccess($this->params->SecureKey);
		unset($Applications);
		return $access;
	}

	/**
	 * @param $params
	 * @return array
	 */
	function GetDirectAddressRecipients($params)
	{

		$this->constructor($params);
		if (!$this->isAuth()) {
			return [
				'Success' => false,
				'Error' => 'Error: HTTP 403 Access Forbidden'
			];
		}

		$conn = Matcha::getConn();
		$sth = $conn->prepare("SELECT direct_address FROM users WHERE direct_address IS NOT NULL AND direct_address != ''");
		$sth->execute();
		$data = $sth->fetchAll(PDO::FETCH_ASSOC);

		return [
			'Success' => true,
			'Data' => json_encode($data)
		];
	}

	/**
	 * @param $params
	 * @return array
	 */
	function GetPatientPid($params)
	{

		$this->constructor($params);
		if (!$this->isAuth()) {
			return [
				'Success' => false,
				'Error' => 'Error: HTTP 403 Access Forbidden'
			];
		}

		require_once(ROOT . '/dataProvider/Patient.php');
		$Patient = new Patient();


		$patient = false;
		$errors = [];
		$dob = '0000-00-00';

		if (isset($params->PatientRecordNumber) && $params->PatientRecordNumber != '') {
			$params->PatientRecordNumber = filter_var($params->PatientRecordNumber, FILTER_SANITIZE_ENCODED);
			$patient = $Patient->getPatientByPublicId($params->PatientRecordNumber);
		}

		if ($patient === false) {

			if (!isset($params->PatientFirstName)) {
				$errors[] = 'PatientFirstName Missing';
			} elseif ($params->PatientFirstName == '') {
				$errors[] = 'PatientFirstName Empty';
			}

			if (!isset($params->PatientLastName)) {
				$errors[] = 'PatientLastName Missing';
			} elseif ($params->PatientLastName == '') {
				$errors[] = 'PatientLastName Empty';
			}

			if (!isset($params->PatientDateOfBirth)) {
				$errors[] = 'PatientDateOfBirth Missing';
			} elseif ($params->PatientDateOfBirth == '') {
				$errors[] = 'PatientDateOfBirth Empty';
			} else {
				$params->PatientDateOfBirth = filter_var($params->PatientDateOfBirth, FILTER_SANITIZE_ENCODED);
				$dob = date('Y-m-d', strtotime($params->PatientDateOfBirth));
				if ($dob === false) {
					$errors[] = 'PatientDateOfBirth Parse Error';
				}
			}
			if (!isset($params->PatientSex)) {
				$errors[] = 'PatientSex missing';
			} elseif ($params->PatientSex == '') {
				$errors[] = 'PatientSex Empty';
			} else {
				if (
					$params->PatientSex != 'F' &&
					$params->PatientSex != 'M' &&
					$params->PatientSex != 'U'
				) {
					$errors[] = 'PatientSex Invalid Value';
				}
			}

			if (!empty($errors)) {
				return [
					'Success' => false,
					'Error' => 'Error: ' . implode(' and ', $errors)
				];
			}

			$params->PatientFirstName = filter_var($params->PatientFirstName, FILTER_SANITIZE_ENCODED);
			$params->PatientMiddleName = filter_var($params->PatientMiddleName, FILTER_SANITIZE_ENCODED);
			$params->PatientLastName = filter_var($params->PatientLastName, FILTER_SANITIZE_ENCODED);
			$params->PatientSex = filter_var($params->PatientSex, FILTER_SANITIZE_ENCODED);

			$sql = 'SELECT * 
					  FROM patient
					 WHERE fname = :fname
					   AND mname = :mname
					   AND lname = :lname
					   AND sex = :sex
					   AND DOB >= :dob_start
					   AND DOB <= :dob_end';

			$dob_start = $dob . ' 00:00:00';
			$dob_end = $dob . ' 23:59:59';

			$patients = $Patient->p->sql($sql)->all([
				':fname' => $params->PatientFirstName,
				':mname' => $params->PatientMiddleName,
				':lname' => $params->PatientLastName,
				':sex' => $params->PatientSex,
				':dob_start' => $dob_start,
				':dob_end' => $dob_end,
			]);

			if(count($patients) > 1){
				return [
					'Success' => false,
					'Error' => 'Error: Multiple Patients Found'
				];
			}

			$patient = $patients[0];
		}

		if (!isset($patient) || $patient === false) {
			return [
				'Success' => false,
				'Error' => 'Error: Patient Not Found'
			];
		}

		return [
			'Success' => true,
			'PatientPid' => $patient['pid']
		];
	}

	/**
	 * @param $params
	 * @return array
	 */
	function GetMessageAddressRecipients($params)
	{

		$this->constructor($params);
		if (!$this->isAuth()) {
			return [
				'Success' => false,
				'Error' => 'Error: HTTP 403 Access Forbidden'
			];
		}

		include(ROOT . '/dataProvider/User.php');
		$User = new User();

		$users = $User->getUsersByAcl('receive_patient_messages');

		$addresses = [];

		foreach ($users['data'] as $user) {
			$addresses[] = [
				'uid' => $user['id'],
				'user_name' => $user['title'] . ' ' . $user['lname'] . ', ' . $user['fname']
			];
		}

		return [
			'Success' => true,
			'Data' => json_encode($addresses)
		];
	}

	/**
	 * @param $params
	 * @return array
	 */
	public function PatientPortalAuthorize($params)
	{
		$this->constructor($params);
		$logObject = new stdClass();

		if (!$this->isAuth()) {

			// Save AuditLog
			$logObject->event = 'PORTAL LOGIN (Error)';
			$logObject->foreign_table = 'patient';
			$logObject->event_description = 'Patient portal login attempt: Secure key';
			$this->AuditLog->addLog($logObject);
			unset($logObject);

			return [
				'Success' => false,
				'Error' => 'Error: HTTP 403 Access Forbidden'
			];
		}
		$patient = $this->getPatient($params);

		$response = [
			'Success' => false,
			'Error' => 'Not Authorized'
		];

		if (!isset($patient)) {
			// Save AuditLog
			$logObject->event = 'PORTAL LOGIN (Patient not provided)';
			$logObject->foreign_table = 'patient';
			$logObject->event_description = 'Patient portal login attempt: Patient parameter';
			$this->AuditLog->addLog($logObject);
			unset($logObject);
			return $response;
		}

		// Check the AUTH of a Patient Login
		// Check for the password / allowance / Date of Birth of the Patient
		if ($patient->WebPortalAccess) {
			if ($patient->WebPortalPassword == $params->Password &&
				$patient->WebPortalUsername == $params->PatientAccount &&
				substr($patient->DateOfBirth, 0, 10) == $params->DateOfBirth
			) {
				// Save AuditLog
				$logObject->event = 'PORTAL LOGIN (Success)';
				$logObject->pid = $patient->Pid;
				$logObject->foreign_table = 'patient';
				$logObject->event_description = 'Patient portal login attempt: Success patient';
				$this->AuditLog->addLog($logObject);
				unset($logObject);
				return [
					'Success' => true,
					'Patient' => $patient,
					'Who' => 'Patient',
					'WhoName' => $patient->FirstName . ' ' . $patient->MiddleName . ' ' . $patient->LastName,
					'Error' => ''
				];
			}
		}

		// Check the AUTH of a Guardian Login
		// Check for the password / allowance / Date of Birth of the Patient
		if ($patient->GuardianPortalAllow) {
			if ($patient->GuardianPortalPassword == $params->Password &&
				$patient->GuardianPortalUsername == $params->PatientAccount &&
				substr($patient->DateOfBirth, 0, 10) == $params->DateOfBirth
			) {
				// Save AuditLog
				$logObject->event = 'PORTAL LOGIN (Success)';
				$logObject->pid = $patient->Pid;
				$logObject->foreign_table = 'patient';
				$logObject->event_description = 'Patient portal login attempt: Success guardian';
				$this->AuditLog->addLog($logObject);
				return [
					'Success' => true,
					'Patient' => $patient,
					'Who' => 'Guardian',
					'WhoName' => $patient->GuardiansFirstName . ' ' . $patient->GuardiansMiddleName . ' ' . $patient->GuardiansLastName,
					'Error' => ''
				];
			}
		}

		// Check the AUTH of a Emergency Contact Login
		// Check for the password / allowance / Date of Birth of the Patient
		if ($patient->EmergencyPortalAllow) {
			if ($patient->EmergencyPortalPassword == $params->Password &&
				$patient->EmergencyPortalUsername == $params->PatientAccount &&
				substr($patient->DateOfBirth, 0, 10) == $params->DateOfBirth
			) {
				// Save AuditLog
				$logObject->event = 'PORTAL LOGIN (Success)';
				$logObject->pid = $patient->Pid;
				$logObject->foreign_table = 'patient';
				$logObject->event_description = 'Patient portal login attempt: Success emergency';
				$this->AuditLog->addLog($logObject);
				return [
					'Success' => true,
					'Patient' => $patient,
					'Who' => 'Emergency',
					'WhoName' => $patient->EmergencyContactFirstName . ' ' . $patient->EmergencyContactMiddleName . ' ' . $patient->EmergencyContactLastName,
					'Error' => ''
				];
			}
		}

		return $response;
	}

	/**
	 * Method to handle patient amendments from the Patient Portal
	 * @param $params
	 * @return array
	 */
	public function newPatientAmendment($params)
	{
		try {
			$this->constructor($params);
			if (!$this->isAuth()) {
				return [
					'Success' => false,
					'Error' => 'Error: HTTP 403 Access Forbidden'
				];
			}

			include_once(ROOT . '/dataProvider/Amendments.php');
			$Amendments = new Amendments();
			$data = json_decode($params->Data);

			if (isset($data->demographics) && count($data->demographics) > 0) {
				foreach ($data->demographics as &$demographic) {
					if (array_key_exists($demographic->field_name, $this->demographicsMap)) {
						$demographic->field_name = $this->demographicsMap[$demographic->field_name];
					}
				}
			}

			$record = new stdClass();

			$record->portal_id = $params->PortalId;
			$record->pid = $params->Pid;
			$record->amendment_type = $params->Type;
			$record->amendment_data = $data;
			$record->amendment_message = $params->Message;
			$record->amendment_status = 'W';
			$record->is_read = '0';
			$record->is_viewed = '0';
			$record->is_synced = '1';
			$record->assigned_to_uid = '0';

			$record->create_uid = '0';
			$record->create_date = date('Y-m-d H:i:s');

			$record = $Amendments->addAmendment($record);
			$record = (object)$record['data'];

			if (isset($record->id) && $record->id > 0) {
				return [
					'Success' => true,
					'AmendmentId' => $record->id,
					'Error' => ''
				];
			} else {
				return [
					'Success' => false,
					'Error' => 'Unable to complete request, Please contact provider.'
				];
			}
		} catch (Exception $e) {
			return [
				'Success' => false,
				'Error' => 'Unable to complete request, Please contact provider.'
			];
		}
	}

	/**
	 * @param $params
	 * @return array
	 */
	public function cancelPatientAmendment($params)
	{
		$this->constructor($params);
		if (!$this->isAuth()) {
			return [
				'Success' => false,
				'Error' => 'Error: HTTP 403 Access Forbidden'
			];
		}

		include_once(ROOT . '/dataProvider/Amendments.php');
		$Amendments = new Amendments();

		$record = $Amendments->getAmendment([
			'id' => $params->AmendmentId,
			'pid' => $params->Pid
		]);

		if ($record === false) {
			return [
				'Success' => false,
				'Error' => 'Unable to find Amendment request'
			];
		}

		$record = (object)$record;

		if ($record->amendment_status != 'W') {

			if ($record->amendment_status === 'A') {
				$msg = 'Unable to cancel, Amendment previously approved';
			} elseif ($record->amendment_status === 'D') {
				$msg = 'Unable to cancel, Amendment previously denied';
			} elseif ($record->amendment_status === 'C') {
				$msg = 'Unable to cancel, Amendment previously canceled';
			} else {
				$msg = 'Unable to cancel, Amendment due to status (' . $record->amendment_status . ')';
			}

			return [
				'Success' => false,
				'Error' => $msg
			];
		}

		$record->is_synced = '1';
		$record->update_uid = 0;
		$record->amendment_status = 'C';
		$record->cancel_date = date('Y-m-d H:i:s');
		$record->cancel_by = 'P' . $record->pid;
		$record->update_date = date('Y-m-d H:i:s');

		$Amendments->updateAmendment($record);

		return [
			'Success' => true,
			'Error' => ''
		];

	}

	/**
	 * @param $params
	 * @return array
	 */
	public function GetCCDDocument($params)
	{
		$this->constructor($params);

		if (!$this->isAuth()) {
			return [
				'Success' => false,
				'Error' => 'Error: HTTP 403 Access Forbidden'
			];
		}

		$_SESSION['user']['facility'] = $this->facility;
		include_once(ROOT . '/dataProvider/CCDDocument.php');
		$ccd = new CCDDocument();
		$ccd->setPid($params->Pid);
		$ccd->setEid('all_enc');
		$ccd->setTemplate('toc');
		$ccd->createCCD();

		return [
			'Success' => true,
			'Document' => $ccd->get()
		];
	}

	/**
	 * @param $params
	 * @return array
	 */
	public function GetCCDSDocument($params){

		$this->constructor($params);
		if (!$this->isAuth()) {
			return [
				'Success' => false,
				'Error' => 'Error: HTTP 403 Access Forbidden'
			];
		}

		$params->DateStart = isset($params->DateStart) ? $params->DateStart : null;
		$params->DateEnd = isset($params->DateEnd) ? $params->DateEnd : null;
		$params->Excludes = isset($params->Excludes) ? explode('&', $params->Excludes) : [];

		include_once(ROOT . '/dataProvider/PatientRecord.php');
		$PatientRecord = new PatientRecord('xml');

		$record = $PatientRecord->getRecord(
				$params->Pid,
				null,
				null,
				$params->DateStart,
				$params->DateEnd,
				$params->Excludes);

		return [
			'Success' => true,
			'Record' => $record,
			'Error' => ''
		];
	}

	public function MergePatient($params){

        $this->constructor($params);
        if (!$this->isAuth()) {
            return [
                'Success' => false,
                'Error' => 'Error: HTTP 403 Access Forbidden'
            ];
        }



        return [
            'Success' => true,
            'Pid' => 123,
            'RecordNumber' => 'RECORD-NUMBER',
            'Error' => 'Error Text is any'
        ];

    }
	public function TransferPatient($params){

        $this->constructor($params);
        if (!$this->isAuth()) {
            return [
                'Success' => false,
                'Error' => 'Error: HTTP 403 Access Forbidden'
            ];
        }



        return [
            'Success' => true,
            'Pid' => 123,
            'RecordNumber' => 'RECORD-NUMBER',
            'Error' => 'Error Text is any'
        ];

    }

	/**
	 * @param $params
	 * @return array
	 */
	public function AddPatient($params)
	{
		try {

			$this->constructor($params);

			if (!$this->isAuth()) throw new \Exception('Error: HTTP 403 Access Forbidden');

			/**
			 * Patient class
			 */
			require_once(ROOT . '/dataProvider/Patient.php');
			$Patient = new Patient();

			/**
			 * validations
			 */
			$validations = [];
			if (!isset($params->Patient->FirstName)) {
				$validations[] = 'First Name Missing';
			}
			if (!isset($params->Patient->LastName)) {
				$validations[] = 'Last Name Missing';
			}
			if (!isset($params->Patient->DOB)) {
				$validations[] = 'DOB Missing';
			}
			if (preg_match($this->vDate, $params->Patient->DOB) == 0) {
				$validations[] = 'DOB format YYYY-MM-DD not valid';
			}
			if (!isset($params->Patient->Sex)) {
				$validations[] = 'Sex Missing';
			}
			if (isset($params->Patient->DriveLicenceExpirationDate) && preg_match($this->vDate, $params->Patient->DriveLicenceExpirationDate) == 0) {
				$validations[] = 'DriveLicenceExpirationDate format YYYY-MM-DD not valid';
			}
			if (isset($params->Patient->DeceaseDate) && preg_match($this->vDate, $params->Patient->DeceaseDate) == 0) {
				$validations[] = 'DeceaseDate format YYYY-MM-DD not valid';
			}
			if (isset($params->Patient->DeathDate) && preg_match($this->vDate, $params->Patient->DeathDate) == 0) {
				$validations[] = 'DeathDate format YYYY-MM-DD not valid';
			}
			if (isset($params->Patient->DeathDate) && preg_match($this->vDate, $params->Patient->DeathDate) == 0) {
				$validations[] = 'DeathDate format YYYY-MM-DD not valid';
			}
			if (isset($params->Patient->Email) && !filter_var($params->Patient->Email, FILTER_VALIDATE_EMAIL)) {
				$validations[] = 'Invalid Email format';
			}

			// TODO validate Sex, MaritalStatus, Race, Ethnicity, Religion, and Language  HL7 values

			if (isset($params->Patient->Pid) && $Patient->getPatientByPid($params->Patient->Pid) !== false) {
				$validations[] = 'Duplicated Pid found in database';
			}
			if (isset($params->Patient->RecordNumber) && $Patient->getPatientByPublicId($params->Patient->RecordNumber) !== false) {
				$validations[] = 'Duplicated RecordNumber found in database';
			}
			if (!empty($validations)) {
				throw new \Exception('Validation Error: ' . implode(', ', $validations));
			}

			/**
			 * Lets continue
			 */

			$patient = new stdClass();
			// basic info
			$patient->pubpid = isset($params->Patient->RecordNumber) ? $params->Patient->RecordNumber : '';
			$patient->title = isset($params->Patient->Title) ? $params->Patient->Title : '';
			$patient->fname = $params->Patient->FirstName;
			$patient->mname = isset($params->Patient->MiddleName) ? $params->Patient->MiddleName : '';
			$patient->lname = $params->Patient->LastName;
			$patient->DOB = $params->Patient->DOB;
			$patient->sex = $params->Patient->Sex;
			// extra info
			$patient->SS = isset($params->Patient->SSN) ? $params->Patient->SSN : '';
			$patient->marital_status = isset($params->Patient->MaritalStatus) ? $params->Patient->MaritalStatus : '';
			$patient->race = isset($params->Patient->Race) ? $params->Patient->Race : '';
			$patient->ethnicity = isset($params->Patient->Ethnicity) ? $params->Patient->Ethnicity : '';
			$patient->religion = isset($params->Patient->Religion) ? $params->Patient->Religion : '';
			$patient->language = isset($params->Patient->Language) ? $params->Patient->Language : '';
			// driver lic
			$patient->drivers_license = isset($params->Patient->DriverLicence) ? $params->Patient->DriverLicence : '';
			$patient->drivers_license_state = isset($params->Patient->DriverLicenceState) ? $params->Patient->DriverLicenceState : '';
			$patient->drivers_license_exp = isset($params->Patient->DriverLicenceExpirationDate) ? $params->Patient->DriverLicenceExpirationDate : '0000-00-00';
			// physical address
			$patient->address = isset($params->Patient->PhysicalAddressLineOne) ? $params->Patient->PhysicalAddressLineOne : '';
			$patient->address_cont = isset($params->Patient->PhysicalAddressLineTwo) ? $params->Patient->PhysicalAddressLineTwo : '';
			$patient->city = isset($params->Patient->PhysicalCity) ? $params->Patient->PhysicalCity : '';
			$patient->state = isset($params->Patient->PhysicalState) ? $params->Patient->PhysicalState : '';
			$patient->country = isset($params->Patient->PhysicalCountry) ? $params->Patient->PhysicalCountry : '';
			$patient->zipcode = isset($params->Patient->PhysicalZipCode) ? $params->Patient->PhysicalZipCode : '';
			// postal address
//			$patient->mname = isset($params->Patient->PostalAddressLineOne) ? $params->Patient->PostalAddressLineOne : '';
//			$patient->mname = isset($params->Patient->PostalAddressLineTwo) ? $params->Patient->PostalAddressLineTwo : '';
//			$patient->mname = isset($params->Patient->PostalCity) ? $params->Patient->PostalCity : '';
//			$patient->mname = isset($params->Patient->PostalState) ? $params->Patient->PostalState : '';
//			$patient->mname = isset($params->Patient->PostalCountry) ? $params->Patient->PostalCountry : '';
//			$patient->mname = isset($params->Patient->PostalZipCode) ? $params->Patient->PostalZipCode : '';
			// phones and email info
			$patient->home_phone = isset($params->Patient->HomePhoneNumber) ? $params->Patient->HomePhoneNumber : '';
			$patient->mobile_phone = isset($params->Patient->MobilePhoneNumber) ? $params->Patient->MobilePhoneNumber : '';
			$patient->work_phone = isset($params->Patient->WorkPhoneNumber) ? $params->Patient->WorkPhoneNumber : '';
			$patient->work_phone_ext = isset($params->Patient->WorkPhoneExt) ? $params->Patient->WorkPhoneExt : '';
			$patient->email = isset($params->Patient->Email) ? $params->Patient->Email : '';
			// image
			$patient->image = isset($params->Patient->Image) ? $params->Patient->Image : '';
			// ....
			$patient->birth_place = isset($params->Patient->BirthPlace) ? $params->Patient->BirthPlace : '';
			$patient->birth_multiple = isset($params->Patient->IsBirthMultiple) ? $params->Patient->IsBirthMultiple : '0';
			$patient->birth_order = isset($params->Patient->BirthOrder) ? $params->Patient->BirthOrder : null;
			$patient->deceased = isset($params->Patient->Deceased) ? $params->Patient->Deceased : '0';
			$patient->death_date = isset($params->Patient->DeceaseDate) ? $params->Patient->DeceaseDate : '0000-00-00';
			$patient->mothers_name = isset($params->Patient->MothersName) ? $params->Patient->MothersName : '';
			$patient->guardians_name = isset($params->Patient->GuardiansName) ? $params->Patient->GuardiansName : '';
			$patient->emer_contact = isset($params->Patient->EmergencyContact) ? $params->Patient->EmergencyContact : '';
			$patient->emer_phone = isset($params->Patient->EmergencyPhone) ? $params->Patient->EmergencyPhone : '';
			$patient->occupation = isset($params->Patient->Occupation) ? $params->Patient->Occupation : '';

			$patient = (object)$Patient->createNewPatient($patient);

			return [
				'Success' => true,
				'Pid' => $patient->pid,
				'RecordNumber' => $patient->pubpid
			];

		} catch (\Exception $e) {
			return [
				'Success' => false,
				'Error' => $e->getMessage()
			];
		}
	}

	/**
	 * @param $params
	 * @return array
	 */
	public function UploadPatientDocument($params)
	{
		$this->constructor($params);

		if (!$this->isAuth()) {
			return [
				'Success' => false,
				'Error' => 'Error: HTTP 403 Access Forbidden'
			];
		}

		if (!$this->isPatientValid()) {
			return [
				'Success' => false,
				'Error' => 'Error: No Valid Patient Found'
			];
		}

		if (!$this->isProviderValid()) {
			return [
				'Success' => false,
				'Error' => 'Error: No Valid Provider Found'
			];
		}

		$document = new stdClass();
		$document->eid = 0;
		$document->pid = $this->patient->pid;
		$document->uid = $this->provider->id;
		$document->name = 'SoapUpload.pdf';

		$document->date = $params->Document->Date;
		$document->title = $params->Document->Title;
		$document->document = $params->Document->Base64Document;

		$document->docType = isset($params->Document->Category) ? $params->Document->Category : 'General';
		$document->note = isset($params->Document->Notes) ? $params->Document->Notes : '';
		$document->encrypted = isset($params->Document->Encrypted) ? $params->Document->Encrypted : false;;

		require_once(ROOT . '/dataProvider/DocumentHandler.php');
		$DocumentHandler = new DocumentHandler();
		$result = $DocumentHandler->addPatientDocument($document);
		unset($DocumentHandler);

		return [
			'Success' => isset($result['data']->id)
		];
	}
	/**
	 * @param $params
	 * @return array
	 */
	public function GetDocuments($params)
	{
		$this->constructor($params);

		if (!$this->isAuth()) {
			return [
				'Success' => false,
				'Error' => 'Error: HTTP 403 Access Forbidden'
			];
		}

		if($params->DocumentIds === ''){
			return [
				'Success' => false,
				'Error' => 'Error: DocumentIds required'
			];
		}

		$ids = explode(',', $params->DocumentIds);

		$filters = new stdClass();
		$filters->filter = [];

		foreach($ids as $id){
			$foo = new stdClass();
			$foo->property = 'id';
			$foo->value = $id;
			$filters->filter[] = $foo;
		}

		require_once(ROOT . '/dataProvider/DocumentHandler.php');
		$DocumentHandler = new DocumentHandler();
		$documents = $DocumentHandler->getPatientDocuments($filters, true, false);


		$Documents = [];

		foreach($documents['data'] as $document){
			if(!isset($document['document']) || empty($document['document'])) continue;

			$Documents[] = [
				'Id' => $document['id'],
				'Code' => $document['code'],
				'Base64Data' => $document['document'],
				'Date' => $document['date'],
				'Title' => $document['title'],
				'Category' => $document['docType'],
				'Notes' => $document['note'],
				'Encrypted' => false,
			];

		}

		return [
			'Success' => true,
			'Document' => array_slice($Documents, 0, 99)
		];
	}

	/**
	 * @param $provider
	 *
	 * @return mixed|object
	 */
	private function getProvider($provider)
	{
		require_once(ROOT . '/dataProvider/User.php');
		$User = new User();
		$provider = $User->getUserByNPI($provider->NPI);
		unset($User);
		return $this->provider = $provider !== false ? (object)$provider : $provider;
	}

	/**
	 * Method to get the patient record, by using the Patient ID, or the
	 * WebPortal, or Guardians Account this method is also used by mdTimeLine PHR Portal
	 * @param $patient Object
	 *
	 * @return mixed|object
	 */
	private function getPatient($patient)
	{
		require_once(ROOT . '/dataProvider/Patient.php');
		$Patient = new Patient();

		// If the Patient Account is set, this means that the method is called from the
		// Patient WebPortal.
		if (isset($patient->PatientAccount)) {
			$patientValidate = $Patient->getPatientByUsername($patient->PatientAccount);
			if (empty($patientValidate)) $patientValidate = $Patient->getPatientByGuardian($patient->PatientAccount);
			if (empty($patientValidate)) $patientValidate = $Patient->getPatientByEmergencyContact($patient->PatientAccount);
		} else {
			$patientValidate = $Patient->getPatientByPid($patient->Pid);
		}

		unset($Patient);
		return $this->patient !== false ? $this->convertPatient($patientValidate, false) : $patientValidate;
	}

	/**
	 * @return bool
	 */
	private function isPatientValid()
	{
		return $this->patient !== false;
	}

	/**
	 * @return bool
	 */
	private function isProviderValid()
	{
		return $this->provider !== false;
	}

	private function convertPatient($data, $inbound)
	{
		$mapped = new \stdClass();
		if (is_array($data)) $data = (object)$data;
		if ($inbound) {
			foreach ($this->demographicsMap as $service => $gaia) {
				if (isset($data->{$service})) {
					$mapped->{$gaia} = $data->{$service};
					if ($gaia == 'DOB' || $gaia == 'drivers_license_exp' || $gaia == 'death_date') {
						$mapped->{$gaia} = str_replace(' ', 'T', $mapped->{$gaia});
					}
				}

			}
		} else {
			foreach ($this->demographicsMap as $service => $gaia) {
				if (isset($data->{$gaia})) {
					$mapped->{$service} = $data->{$gaia};

					if ($service == 'DateOfBirth' || $service == 'DriverLicenceExpirationDate' || $service == 'DeceaseDate') {
						$mapped->{$service} = str_replace(' ', 'T', $mapped->{$service});

					} elseif ($service == 'Language' && $mapped->{$service} == '') {
						unset($mapped->{$service});
					}
				}
			}
		}
		return $mapped;
	}
}
