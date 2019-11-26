<?php

class MeasureCalculation {

	private $conn;
	private $office_visit_codes;

	function __construct()
	{
		$this->conn = Matcha::getConn();
		$this->office_visit_codes = [
			'99201',
			'99202',
			'99203',
			'99205',
			'99205',
			'99211',
			'99212',
			'99213',
			'99214',
			'99215'
		];
	}

	public function getReportMeasureByDates($measure, $provider_id, $start_date, $end_date){
		try{
			if($measure == 'ePrescribing'){
				return $this->getPrescribingReportByDates($provider_id, $start_date, $end_date);
			}
			if($measure == 'ProvidePatientsElectronicAccess'){
				return $this->getProvidePatientsElectronicAccessReportByDates($provider_id, $start_date, $end_date);
			}
			if($measure == 'PatientEducation'){
				return $this->getPatientEducationReportByDates($provider_id, $start_date, $end_date);
			}
			if($measure == 'ViewDownloadTransmit'){
				return $this->getViewDownloadTransmitReportByDates($provider_id, $start_date, $end_date);
			}
			if($measure == 'Secure Messaging'){
				return $this->getSecureMessagingReportByDates($provider_id, $start_date, $end_date);
			}
			if($measure == 'CPOEMedications'){
				return $this->getCPOEMedications($provider_id, $start_date, $end_date);
			}
			if($measure == 'CPOELaboratory'){
				return $this->getCPOELaboratory($provider_id, $start_date, $end_date);
			}
			if($measure == 'CPOERadiology'){
				return $this->getCPOERadiology($provider_id, $start_date, $end_date);
			}
			return [];
		}catch (Exception $e){
			return [];
		}
	}

	// Required Test 1 - ePrescribing
	public function getPrescribingReportByDates($provider_id, $start_date, $end_date){

		$records = [];

		/**
		 * Required Test 1 - ePrescribing
		 * Modified Stage 2 Objective 4 and Stage 3 Objective 2
		 * Promoting Interoperability Transition Objective 2 Measure 1 and Promoting Interoperability Objective 2 Measure 1
		 */


		/**
		 * Modified Stage 2 Measure:
		 * Eligible Professional (EP): More than 50 percent of all permissible prescriptions written by the EP are
		 * queried for a drug formulary and transmitted electronically using Certified Health IT.
		 *
		 * Modified Stage 2 Measure English Statements:
		 * Numerator: The number of prescriptions in the denominator generated, queried for a drug formulary, and transmitted electronically.
		 * Denominator: Number of permissible prescriptions written during the EHR reporting period for drugs requiring a prescription in order to be dispensed.
		 *
		 * Modified Stage 2 Measure Elements:
		 * Numerator: Prescription generated, queried for a formulary, and transmitted electronically.
		 * Denominator: Prescriptions generated.
		 */
		$sth = $this->conn->prepare("SELECT id FROM patient_medications WHERE uid = '{$provider_id}' AND date_ordered BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE) AND date_ordered IS NOT NULL");
		$sth->execute();
		$ordered_prescriptions =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$ordered_prescriptions_ids = [];
		foreach($ordered_prescriptions as $ordered_prescription) {
			$ordered_prescriptions_ids[] = $ordered_prescription['id'];
		}
		$denominator = count($ordered_prescriptions_ids);
		$ordered_prescriptions_ids = join("','", $ordered_prescriptions_ids);

		$sth = $this->conn->prepare("SELECT count(*) as `count` FROM erx_prescriptions WHERE orderId IN ('{$ordered_prescriptions_ids}') GROUP BY orderId");
		$sth->execute();
		$numerator =  $sth->fetch(PDO::FETCH_ASSOC);
		$numerator = $numerator['count'];

		$records[] = [
			'group' => 'ePrescribing',
			'title' => 'Modified Stage 2 Measure',
			'description' => 'Eligible Professional (EP): More than 50 percent of all permissible prescriptions written by the EP are queried for a drug formulary and transmitted electronically using Certified Health IT.',
			'denominator' => $denominator,
			'numerator' => $numerator,
			'goal' => '50%'
		];


		/**
		 * Stage 3 Measure:
		 * Eligible Professional (EP): More than 60 percent of all permissible prescriptions written by the EP are
		 * queried for a drug formulary and transmitted electronically using CEHRT.
		 *
		 * Stage 3 Measure English Statements:
		 * Numerator: The number of prescriptions in the denominator generated, queried for a drug formulary, and transmitted electronically using CEHRT.
		 * Denominator: The number of prescriptions written for drugs requiring a prescription in order to be dispensed other
		 * than controlled substances during the EHR reporting period; or number of prescriptions written for drugs requiring
		 * a prescription in order to be dispensed during the EHR reporting period.
		 *
		 * Stage 3 Measure Elements:
		 * Modified Stage 2 Measure Elements:
		 * Numerator: Prescription generated, queried for a formulary, and transmitted electronically.
		 * Denominator: Prescriptions generated.
		 *
		 */
		$sth = $this->conn->prepare("SELECT id FROM patient_medications WHERE uid = '{$provider_id}' AND date_ordered BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE) AND date_ordered IS NOT NULL");
		$sth->execute();
		$ordered_prescriptions =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$ordered_prescriptions_ids = [];
		foreach($ordered_prescriptions as $ordered_prescription) {
			$ordered_prescriptions_ids[] = $ordered_prescription['id'];
		}
		$denominator = count($ordered_prescriptions_ids);
		$ordered_prescriptions_ids = join("','", $ordered_prescriptions_ids);

		$sth = $this->conn->prepare("SELECT count(*) as `count` FROM erx_prescriptions WHERE orderId IN ('{$ordered_prescriptions_ids}') GROUP BY orderId");
		$sth->execute();
		$numerator =  $sth->fetch(PDO::FETCH_ASSOC);
		$numerator = $numerator['count'];

		$records[] = [
			'group' => 'ePrescribing',
			'title' => 'Stage 3 Measure',
			'description' => 'Eligible Professional (EP): More than 60 percent of all permissible prescriptions written by the EP are queried for a drug formulary and transmitted electronically using CEHRT.',
			'denominator' => $denominator,
			'numerator' => $numerator,
			'goal' => '60%'
		];


		/**
		 * Promoting Interoperability Transition Measure:
		 * Eligible Clinician (EC): At least one permissible prescription written by the MIPS EC is queried for a drug
		 * formulary and transmitted electronically using certified EHR technology.
		 *
		 * Promoting Interoperability Transition English Statements:
		 * Numerator: The number of prescriptions in the denominator generated, queried for a drug formulary, and transmitted electronically using certified EHR technology.
		 * Denominator: Number of prescriptions written for drugs requiring a prescription to be dispensed other than controlled substances during the performance period; or number of prescriptions written for drugs requiring a prescription in order to be dispensed during the performance period.
		 *
		 * Promoting Interoperability Transition Measure Elements:
		 * Numerator: Prescription generated, queried for a formulary, and transmitted electronically.
		 * Denominator: Prescriptions other than controlled substances generated; or prescriptions generated.
		 */
		$sth = $this->conn->prepare("SELECT id FROM patient_medications WHERE uid = '{$provider_id}' AND date_ordered BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE) AND date_ordered IS NOT NULL");
		$sth->execute();
		$ordered_prescriptions =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$ordered_prescriptions_ids = [];
		foreach($ordered_prescriptions as $ordered_prescription) {
			$ordered_prescriptions_ids[] = $ordered_prescription['id'];
		}
		$denominator = count($ordered_prescriptions_ids);
		$ordered_prescriptions_ids = join("','", $ordered_prescriptions_ids);

		$sth = $this->conn->prepare("SELECT count(*) as `count` FROM erx_prescriptions WHERE orderId IN ('{$ordered_prescriptions_ids}') AND ndc IS NOT NULL GROUP BY orderId");
		$sth->execute();
		$numerator =  $sth->fetch(PDO::FETCH_ASSOC);
		$numerator = $numerator['count'];

		$records[] = [
			'group' => 'ePrescribing',
			'title' => 'Promoting Interoperability Transition Measure',
			'description' => 'Eligible Clinician (EC): At least one permissible prescription written by the MIPS EC is queried for a drug formulary and transmitted electronically using certified EHR technology.',
			'denominator' => $denominator,
			'numerator' => $numerator,
			'goal' => '1'
		];

		/**
		 * Promoting Interoperability Measure:
		 * EC: At least one permissible prescription written by the MIPS EC is queried for a drug formulary and transmitted electronically using certified EHR technology.
		 *
		 * Promoting Interoperability Transition English Statements:
		 * Numerator: The number of prescriptions in the denominator generated, queried for a drug formulary, and transmitted electronically using certified EHR technology.
		 * Denominator: Number of prescriptions written for drugs requiring a prescription in order to be dispensed other than controlled substances during the performance period; or number of prescriptions written for drugs requiring a prescription in order to be dispensed during the performance period.
		 *
		 * Promoting Interoperability Transition Measure Elements:
		 * Numerator: Prescription generated, queried for a formulary, and transmitted electronically.
		 * Denominator: Prescriptions other than controlled substances generated; or prescriptions generated.
		 */
		$sth = $this->conn->prepare("SELECT id FROM patient_medications WHERE uid = '{$provider_id}' AND date_ordered BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE) AND date_ordered IS NOT NULL");
		$sth->execute();
		$ordered_prescriptions =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$ordered_prescriptions_ids = [];
		foreach($ordered_prescriptions as $ordered_prescription) {
			$ordered_prescriptions_ids[] = $ordered_prescription['id'];
		}
		$denominator = count($ordered_prescriptions_ids);
		$ordered_prescriptions_ids = join("','", $ordered_prescriptions_ids);

		$sth = $this->conn->prepare("SELECT count(*) as `count` FROM erx_prescriptions WHERE orderId IN ('{$ordered_prescriptions_ids}') AND ndc IS NOT NULL GROUP BY orderId");
		$sth->execute();
		$numerator =  $sth->fetch(PDO::FETCH_ASSOC);
		$numerator = $numerator['count'];

		$records[] = [
			'group' => 'ePrescribing',
			'title' => 'Promoting Interoperability Measure',
			'description' => 'EC: At least one permissible prescription written by the MIPS EC is queried for a drug formulary and transmitted electronically using certified EHR technology.',
			'denominator' => $denominator,
			'numerator' => $numerator,
			'goal' => '1'
		];

		return $records;

	}

	// Required Test 2a, b, or c – Provide Patients Electronic Access to Their Health Information (formerly Patient Electronic Access)
	// TODO
	public function getProvidePatientsElectronicAccessReportByDates($provider_id, $start_date, $end_date){

		$records = [];

		/**
		 * Required Test 2a, b, or c – Provide Patients Electronic Access to Their Health Information (formerly Patient Electronic Access)
		 * Modified Stage 2 Objective 8 Measure 1 and Stage 3 Objective 5 Measure 1
		 * Promoting Interoperability Transition Objective 3 Measure 1 and Promoting Interoperability Objective 3 Measure 1
		 */

		$office_visit_codes = implode("','", $this->office_visit_codes);
		$sth = $this->conn->prepare("SELECT p.pid, p.pubpid, e.service_date FROM encounters as e
										  INNER JOIN patient as p on e.pid = p.pid
											   WHERE e.provider_uid = '{$provider_id}' AND e.service_date BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE) AND visit_category_code IN ('{$office_visit_codes}')
										    GROUP BY pid");
		$sth->execute();
		$patients =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$patients_pids = [];
		$patients_record_numbers = [];
		foreach($patients as $patient) {
			$patients_pids[] = $patient['pid'];
			$patients_record_numbers[] = $patient['pubpid'];
		}
		$denominator = count($patients_pids);
		$patients_pids = join("','", $patients_pids);

		$sth = $this->conn->prepare("SELECT count(*) as count FROM patient_education_resources WHERE pid IN ('{$patients_pids}') AND provided_date BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE) GROUP BY pid");
		$sth->execute();
		$numerator =  $sth->fetch(PDO::FETCH_ASSOC);
		$numerator = $numerator['count'];


		/**
		 * Modified Stage 2 Measure:
		 * Eligible Professional (EP): More than 50 percent of all unique patients seen by the EP during the EHR reporting
		 * period are provided timely access to view online, download, and transmit to a third party their health information
		 * subject to the EP's discretion to withhold certain information.
		 *
		 * Modified Stage 2 Measure English Statements:
		 * Numerator: The number of patients in the denominator who have access to view online, download, and transmit
		 * their health information within 4 business days after the information is available to the EP.
		 * Denominator: Number of unique patients seen by the EP during the EHR reporting period.
		 *
		 * Modified Stage 2 Measure Elements:
		 * Numerator:
		 *  Date and time information available to the EP;
		 *  Date and time information made available online to patient.
		 * Denominator: Number of patients seen by EP.
		 */


		/**
		 * Stage 3 Measure:
		 * Eligible Professional (EP): For more than 80 percent of all unique patients seen by the EP: (1) The patient
		 * (or the patient-authorized representative) is provided timely access to view online, download, and transmit
		 * his or her health information; and (2) The provider ensures the patient’s health information is available
		 * for the patient (or patient-authorized representative) to access using any application of their choice that
		 * is configured to meet the technical specifications of the API in the provider’s CEHRT.
		 *
		 * Modified Stage 2 Measure English Statements:
		 * Numerator: The number of patients in the denominator (or patient-authorized representative) who are provided
		 * timely access to health information to view online, download, and transmit to a third party and to access
		 * using an application of their choice that is configured meet the technical specifications of the API in
		 * the provider's CEHRT.
		 * Denominator: The number of unique patients seen by the EP during the EHR reporting period.
		 *
		 * Stage 3 Measure Elements:
		 * Numerator:
		 *  Date and time information available to the EP;
		 *  Date and time information made available online to patient;
		 *  Date and time information made available to an API.
		 * Denominator: Number of patients seen by the EP.
		 */


		/**
		 * Promoting Interoperability Transition Measure:
		 * At least one patient seen by the MIPS EC during the performance period is provided timely access to view
		 * online, download, and transmit to a third party their health information subject to the MIPS EC's discretion
		 * to withhold certain information.
		 *
		 * Promoting Interoperability Transition English Statements:
		 * Numerator: The number of patients in the denominator (or patient authorized representative) who are provided
		 * timely access to health information to view online, download, and transmit to a third party.
		 * Denominator: The number of unique patients seen by the MIPS EC during the performance period.
		 *
		 * Promoting Interoperability Transition Measure Elements:
		 * Numerator:
		 *  Date and time information available to the EC;
		 *  Date and time information made available online to patient.
		 * Denominator: Number of patients seen by the EC.
		 */


		/**
		 * Promoting Interoperability Measure:
		 * EC: For at least one unique patient seen by the MIPS EC (1) the patient (or the patient authorized representative)
		 * is provided timely access to view online, download, and transmit his or her health information; and (2) the
		 * MIPS EC ensures the patient's health information is available for the patient (or patient-authorized
		 * representative) to access using any application of their choice that is configured to meet the technical
		 * specifications of the API in the MIPS EC's CEHRT.
		 *
		 * Promoting Interoperability English Statements:
		 * Numerator: The number of patients in the denominator (or patient authorized representatives) who are provided
		 * timely access to health information to view online, download, and transmit to a third party and to access
		 * using an application of their choice that is configured to meet the technical specifications of the API in
		 * the MIPS EC’s certified EHR technology.
		 * Denominator: The number of unique patients seen by the MIPS EC during the performance period.
		 *
		 * Promoting Interoperability Measure Elements:
		 * Numerator:
		 *  Date and time information available to the EC;
		 *  Date and time information made available online to patient;
		 *  Date and time information made available to an API.
		 * Denominator: Number of patients seen by the EC.
		 */

		return $records;
	}

	// Required Test 3 – Patient Education
	public function getPatientEducationReportByDates($provider_id, $start_date, $end_date){

		$records = [];

		/**
		 * Required Test 3 – Patient Education
		 * Modified Stage 2 Objective 6 and Stage 3 Objective 5 Measure 2
		 * Promoting Interoperability Transition Objective 4 Measure 1 and Promoting Interoperability Objective 3 Measure 2
		 */


		/**
		 * Modified Stage 2 Measure:
		 * Eligible Professional (EP): Patient-specific education resources identified by Certified Health IT Module (CEHRT)
		 * are provided to patients for more than 10 percent of all unique patients with office visits seen by the EP
		 * during the reporting period.
		 *
		 * MModified Stage 2 Measure English Statements:
		 * Numerator: The number of patients in the denominator who were provided patient-specific education resources identified by the EHR technology.
		 * Denominator: Number of unique patients with office visits seen by the EP during the EHR reporting period.
		 *
		 * Modified Stage 2 Measure Elements:
		 * Numerator: Provision of patient specific education resource(s) identified by the CEHRT.
		 * Denominator: Number of patients with visits to the EP.
		 */

		$office_visit_codes = implode("','", $this->office_visit_codes);
		$sth = $this->conn->prepare("SELECT pid FROM encounters WHERE provider_uid = '{$provider_id}' AND service_date BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE) AND visit_category_code IN ('{$office_visit_codes}') GROUP BY pid");
		$sth->execute();
		$patients =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$patients_pids = [];
		foreach($patients as $patient) {
			$patients_pids[] = $patient['pid'];
		}
		$denominator = count($patients_pids);
		$patients_pids = join("','", $patients_pids);

		$sth = $this->conn->prepare("SELECT count(*) as count FROM patient_education_resources WHERE pid IN ('{$patients_pids}') AND provided_date BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE) GROUP BY pid");
		$sth->execute();
		$numerator =  $sth->fetch(PDO::FETCH_ASSOC);
		$numerator = $numerator['count'];

		$records[] = [
			'group' => 'Patient Education',
			'title' => 'Modified Stage 2 Measure',
			'description' => 'Eligible Professional (EP): Patient-specific education resources identified by Certified Health IT Module (CEHRT) are provided to patients for more than 10 percent of all unique patients with office visits seen by the EP during the reporting period.',
			'denominator' => $denominator,
			'numerator' => $numerator,
			'goal' => '10%'
		];


		/**
		 * Stage 3 Measure:
		 * Eligible Professional: The EP must use clinically relevant information from CEHRT to identify patient-specific
		 * educational resources and provide electronic access to those materials to more than 35 percent of unique
		 * patients seen by the EP during the EHR reporting period.
		 *
		 * Stage 3 Measure English Statements:
		 * Numerator: The number of patients in the denominator who were provided electronic access to patient-specific
		 * educational resources using clinically relevant information identified from CEHRT during the EHR reporting period.
		 * Denominator: The number of unique patients seen by the EP or the number of unique patients discharged from
		 * an eligible hospital or CAH inpatient or emergency department (POS 21 or 23) during the EHR reporting period
		 *
		 *
		 * Stage 3 Measure Elements:
		 * Numerator: Provision of electronic access to patient specific education resource(s) identified by the CEHRT.
		 * Denominator:
		 *  Number of patients seen by the EP.
		 *  Number of patients discharged from the EH or CAH.
		 */
		$office_visit_codes = implode("','", $this->office_visit_codes);
		$sth = $this->conn->prepare("SELECT pid FROM encounters WHERE provider_uid = '{$provider_id}' AND service_date BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE) AND visit_category_code IN ('{$office_visit_codes}') GROUP BY pid");
		$sth->execute();
		$patients =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$patients_pids = [];
		foreach($patients as $patient) {
			$patients_pids[] = $patient['pid'];
		}
		$denominator = count($patients_pids);
		$patients_pids = join("','", $patients_pids);

		$sth = $this->conn->prepare("SELECT count(*) as count FROM patient_education_resources WHERE pid IN ('{$patients_pids}') AND provided_date BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE) GROUP BY pid");
		$sth->execute();
		$numerator =  $sth->fetch(PDO::FETCH_ASSOC);
		$numerator = $numerator['count'];

		$records[] = [
			'group' => 'Patient Education',
			'title' => 'Stage 3 Measure',
			'description' => 'Eligible Professional: The EP must use clinically relevant information from CEHRT to identify patient-specific educational resources and provide electronic access to those materials to more than 35 percent of unique patients seen by the EP during the EHR reporting period.',
			'denominator' => $denominator,
			'numerator' => $numerator,
			'goal' => '35%'
		];


		/**
		 * Promoting Interoperability Transition Measure:
		 * The MIPS EC must use clinically relevant information from certified EHR technology to identify patient-specific
		 * educational resources and provide access to those materials to at least one unique patient seen by the MIPS EC.
		 *
		 * Promoting Interoperability Transition English Statements:
		 * Numerator: The number of patients in the denominator who were provided access to patient-specific educational
		 * resources using clinically relevant information identified from certified EHR technology during the performance period.
		 * Denominator: The number of unique patients seen by the MIPS EC during the performance period.
		 *
		 * Promoting Interoperability Transition Measure Elements:
		 * Numerator: Provision of patient specific education resource(s) identified by the CEHRT.
		 * Denominator: Number of patients seen by the EC.
		 */
		$office_visit_codes = implode("','", $this->office_visit_codes);
		$sth = $this->conn->prepare("SELECT pid FROM encounters WHERE provider_uid = '{$provider_id}' AND service_date BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE) AND visit_category_code IN ('{$office_visit_codes}') GROUP BY pid");
		$sth->execute();
		$patients =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$patients_pids = [];
		foreach($patients as $patient) {
			$patients_pids[] = $patient['pid'];
		}
		$denominator = count($patients_pids);
		$patients_pids = join("','", $patients_pids);

		$sth = $this->conn->prepare("SELECT count(*) as count FROM patient_education_resources WHERE pid IN ('{$patients_pids}') AND provided_date BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE) GROUP BY pid");
		$sth->execute();
		$numerator =  $sth->fetch(PDO::FETCH_ASSOC);
		$numerator = $numerator['count'];

		$records[] = [
			'group' => 'Patient Education',
			'title' => 'Promoting Interoperability Transition Measure',
			'description' => 'The MIPS EC must use clinically relevant information from certified EHR technology to identify patient-specific educational resources and provide access to those materials to at least one unique patient seen by the MIPS EC.',
			'denominator' => $denominator,
			'numerator' => $numerator,
			'goal' => '1'
		];

		/**
		 * Promoting Interoperability Measure (2018 only):
		 * The MIPS EC must use clinically relevant information from certified EHR technology to identify patient-specific
		 * educational resources and provide electronic access to those materials to at least one unique patient seen by the MIPS eligible clinician.
		 *
		 * Promoting Interoperability English Statements (2018 only):
		 * umerator: The number of patients in the denominator who were provided electronic access to patient-specific
		 * educational resources using clinically relevant information identified from certified EHR technology during the performance period.
		 * Denominator: The number of unique patients seen by the MIPS EC during the performance period.
		 *
		 * Promoting Interoperability Transition Measure Elements:
		 * Numerator: Provision of electronic access to patient-specific education resource(s) identified by the CEHRT.
		 * Denominator: Number of patients seen by the EC.
		 */
		$office_visit_codes = implode("','", $this->office_visit_codes);
		$sth = $this->conn->prepare("SELECT pid FROM encounters WHERE provider_uid = '{$provider_id}' AND service_date BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE) AND visit_category_code IN ('{$office_visit_codes}') GROUP BY pid");
		$sth->execute();
		$patients =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$patients_pids = [];
		foreach($patients as $patient) {
			$patients_pids[] = $patient['pid'];
		}
		$denominator = count($patients_pids);
		$patients_pids = join("','", $patients_pids);

		$sth = $this->conn->prepare("SELECT count(*) as count FROM patient_education_resources WHERE pid IN ('{$patients_pids}') AND provided_date BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE) GROUP BY pid");
		$sth->execute();
		$numerator =  $sth->fetch(PDO::FETCH_ASSOC);
		$numerator = $numerator['count'];

		$records[] = [
			'group' => 'Patient Education',
			'title' => 'Promoting Interoperability Measure',
			'description' => 'The MIPS EC must use clinically relevant information from certified EHR technology to identify patient-specific educational resources and provide electronic access to those materials to at least one unique patient seen by the MIPS eligible clinician.',
			'denominator' => $denominator,
			'numerator' => $numerator,
			'goal' => '1'
		];

		return $records;

	}

	// Required Test 4a, b, or c – View, Download, Transmit
	// TODO
	public function getViewDownloadTransmitReportByDates($provider_id, $start_date, $end_date){

		$records = [];

		/**
		 * Required Test 3 – Patient Education
		 * Modified Stage 2 Objective 6 and Stage 3 Objective 5 Measure 2
		 * Promoting Interoperability Transition Objective 4 Measure 1 and Promoting Interoperability Objective 3 Measure 2
		 */


		/**
		 * Modified Stage 2 Measure:
		 * Eligible Professional (EP): Patient-specific education resources identified by Certified Health IT Module (CEHRT)
		 * are provided to patients for more than 10 percent of all unique patients with office visits seen by the EP
		 * during the reporting period.
		 *
		 * MModified Stage 2 Measure English Statements:
		 * Numerator: The number of patients in the denominator who were provided patient-specific education resources identified by the EHR technology.
		 * Denominator: Number of unique patients with office visits seen by the EP during the EHR reporting period.
		 *
		 * Modified Stage 2 Measure Elements:
		 * Numerator: Provision of patient specific education resource(s) identified by the CEHRT.
		 * Denominator: Number of patients with visits to the EP.
		 */

		$office_visit_codes = implode("','", $this->office_visit_codes);
		$sth = $this->conn->prepare("SELECT pid FROM encounters WHERE provider_uid = '{$provider_id}' AND service_date BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE) AND visit_category_code IN ('{$office_visit_codes}') GROUP BY pid");
		$sth->execute();
		$patients =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$patients_pids = [];
		foreach($patients as $patient) {
			$patients_pids[] = $patient['pid'];
		}
		$denominator = count($patients_pids);
		$patients_pids = join("','", $patients_pids);

		$sth = $this->conn->prepare("SELECT count(*) as count FROM patient_education_resources WHERE pid IN ('{$patients_pids}') AND provided_date BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE) GROUP BY pid");
		$sth->execute();
		$numerator =  $sth->fetch(PDO::FETCH_ASSOC);
		$numerator = $numerator['count'];

		$records[] = [
			'group' => 'Patient Education',
			'title' => 'Modified Stage 2 Measure',
			'description' => 'Eligible Professional (EP): Patient-specific education resources identified by Certified Health IT Module (CEHRT) are provided to patients for more than 10 percent of all unique patients with office visits seen by the EP during the reporting period.',
			'denominator' => $denominator,
			'numerator' => $numerator,
			'goal' => '10%'
		];


		/**
		 * Stage 3 Measure:
		 * Eligible Professional: The EP must use clinically relevant information from CEHRT to identify patient-specific
		 * educational resources and provide electronic access to those materials to more than 35 percent of unique
		 * patients seen by the EP during the EHR reporting period.
		 *
		 * Stage 3 Measure English Statements:
		 * Numerator: The number of patients in the denominator who were provided electronic access to patient-specific
		 * educational resources using clinically relevant information identified from CEHRT during the EHR reporting period.
		 * Denominator: The number of unique patients seen by the EP or the number of unique patients discharged from
		 * an eligible hospital or CAH inpatient or emergency department (POS 21 or 23) during the EHR reporting period
		 *
		 *
		 * Stage 3 Measure Elements:
		 * Numerator: Provision of electronic access to patient specific education resource(s) identified by the CEHRT.
		 * Denominator:
		 *  Number of patients seen by the EP.
		 *  Number of patients discharged from the EH or CAH.
		 */
		$office_visit_codes = implode("','", $this->office_visit_codes);
		$sth = $this->conn->prepare("SELECT pid FROM encounters WHERE provider_uid = '{$provider_id}' AND service_date BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE) AND visit_category_code IN ('{$office_visit_codes}') GROUP BY pid");
		$sth->execute();
		$patients =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$patients_pids = [];
		foreach($patients as $patient) {
			$patients_pids[] = $patient['pid'];
		}
		$denominator = count($patients_pids);
		$patients_pids = join("','", $patients_pids);

		$sth = $this->conn->prepare("SELECT count(*) as count FROM patient_education_resources WHERE pid IN ('{$patients_pids}') AND provided_date BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE) GROUP BY pid");
		$sth->execute();
		$numerator =  $sth->fetch(PDO::FETCH_ASSOC);
		$numerator = $numerator['count'];

		$records[] = [
			'group' => 'Patient Education',
			'title' => 'Stage 3 Measure',
			'description' => 'Eligible Professional: The EP must use clinically relevant information from CEHRT to identify patient-specific educational resources and provide electronic access to those materials to more than 35 percent of unique patients seen by the EP during the EHR reporting period.',
			'denominator' => $denominator,
			'numerator' => $numerator,
			'goal' => '35%'
		];


		/**
		 * Promoting Interoperability Transition Measure:
		 * The MIPS EC must use clinically relevant information from certified EHR technology to identify patient-specific
		 * educational resources and provide access to those materials to at least one unique patient seen by the MIPS EC.
		 *
		 * Promoting Interoperability Transition English Statements:
		 * Numerator: The number of patients in the denominator who were provided access to patient-specific educational
		 * resources using clinically relevant information identified from certified EHR technology during the performance period.
		 * Denominator: The number of unique patients seen by the MIPS EC during the performance period.
		 *
		 * Promoting Interoperability Transition Measure Elements:
		 * Numerator: Provision of patient specific education resource(s) identified by the CEHRT.
		 * Denominator: Number of patients seen by the EC.
		 */
		$office_visit_codes = implode("','", $this->office_visit_codes);
		$sth = $this->conn->prepare("SELECT pid FROM encounters WHERE provider_uid = '{$provider_id}' AND service_date BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE) AND visit_category_code IN ('{$office_visit_codes}') GROUP BY pid");
		$sth->execute();
		$patients =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$patients_pids = [];
		foreach($patients as $patient) {
			$patients_pids[] = $patient['pid'];
		}
		$denominator = count($patients_pids);
		$patients_pids = join("','", $patients_pids);

		$sth = $this->conn->prepare("SELECT count(*) as count FROM patient_education_resources WHERE pid IN ('{$patients_pids}') AND provided_date BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE) GROUP BY pid");
		$sth->execute();
		$numerator =  $sth->fetch(PDO::FETCH_ASSOC);
		$numerator = $numerator['count'];

		$records[] = [
			'group' => 'Patient Education',
			'title' => 'Promoting Interoperability Transition Measure',
			'description' => 'The MIPS EC must use clinically relevant information from certified EHR technology to identify patient-specific educational resources and provide access to those materials to at least one unique patient seen by the MIPS EC.',
			'denominator' => $denominator,
			'numerator' => $numerator,
			'goal' => '1'
		];

		/**
		 * Promoting Interoperability Measure (2018 only):
		 * The MIPS EC must use clinically relevant information from certified EHR technology to identify patient-specific
		 * educational resources and provide electronic access to those materials to at least one unique patient seen by the MIPS eligible clinician.
		 *
		 * Promoting Interoperability English Statements (2018 only):
		 * umerator: The number of patients in the denominator who were provided electronic access to patient-specific
		 * educational resources using clinically relevant information identified from certified EHR technology during the performance period.
		 * Denominator: The number of unique patients seen by the MIPS EC during the performance period.
		 *
		 * Promoting Interoperability Transition Measure Elements:
		 * Numerator: Provision of electronic access to patient-specific education resource(s) identified by the CEHRT.
		 * Denominator: Number of patients seen by the EC.
		 */
		$office_visit_codes = implode("','", $this->office_visit_codes);
		$sth = $this->conn->prepare("SELECT pid FROM encounters WHERE provider_uid = '{$provider_id}' AND service_date BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE) AND visit_category_code IN ('{$office_visit_codes}') GROUP BY pid");
		$sth->execute();
		$patients =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$patients_pids = [];
		foreach($patients as $patient) {
			$patients_pids[] = $patient['pid'];
		}
		$denominator = count($patients_pids);
		$patients_pids = join("','", $patients_pids);

		$sth = $this->conn->prepare("SELECT count(*) as count FROM patient_education_resources WHERE pid IN ('{$patients_pids}') AND provided_date BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE) GROUP BY pid");
		$sth->execute();
		$numerator =  $sth->fetch(PDO::FETCH_ASSOC);
		$numerator = $numerator['count'];

		$records[] = [
			'group' => 'Patient Education',
			'title' => 'Promoting Interoperability Measure',
			'description' => 'The MIPS EC must use clinically relevant information from certified EHR technology to identify patient-specific educational resources and provide electronic access to those materials to at least one unique patient seen by the MIPS eligible clinician.',
			'denominator' => $denominator,
			'numerator' => $numerator,
			'goal' => '1'
		];

		return $records;

	}

	// Required Test 5 – Secure Messaging
	// TODO
	public function getSecureMessagingReportByDates($provider_id, $start_date, $end_date){

		$records = [];

		/**
		 * Required Test 3 – Patient Education
		 * Modified Stage 2 Objective 6 and Stage 3 Objective 5 Measure 2
		 * Promoting Interoperability Transition Objective 4 Measure 1 and Promoting Interoperability Objective 3 Measure 2
		 */


		/**
		 * Modified Stage 2 Measure:
		 * Eligible Professional (EP): Patient-specific education resources identified by Certified Health IT Module (CEHRT)
		 * are provided to patients for more than 10 percent of all unique patients with office visits seen by the EP
		 * during the reporting period.
		 *
		 * MModified Stage 2 Measure English Statements:
		 * Numerator: The number of patients in the denominator who were provided patient-specific education resources identified by the EHR technology.
		 * Denominator: Number of unique patients with office visits seen by the EP during the EHR reporting period.
		 *
		 * Modified Stage 2 Measure Elements:
		 * Numerator: Provision of patient specific education resource(s) identified by the CEHRT.
		 * Denominator: Number of patients with visits to the EP.
		 */

		$office_visit_codes = implode("','", $this->office_visit_codes);
		$sth = $this->conn->prepare("SELECT pid FROM encounters WHERE provider_uid = '{$provider_id}' AND service_date BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE) AND visit_category_code IN ('{$office_visit_codes}') GROUP BY pid");
		$sth->execute();
		$patients =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$patients_pids = [];
		foreach($patients as $patient) {
			$patients_pids[] = $patient['pid'];
		}
		$denominator = count($patients_pids);
		$patients_pids = join("','", $patients_pids);

		$sth = $this->conn->prepare("SELECT count(*) as count FROM patient_education_resources WHERE pid IN ('{$patients_pids}') AND provided_date BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE) GROUP BY pid");
		$sth->execute();
		$numerator =  $sth->fetch(PDO::FETCH_ASSOC);
		$numerator = $numerator['count'];

		$records[] = [
			'group' => 'Patient Education',
			'title' => 'Modified Stage 2 Measure',
			'description' => 'Eligible Professional (EP): Patient-specific education resources identified by Certified Health IT Module (CEHRT) are provided to patients for more than 10 percent of all unique patients with office visits seen by the EP during the reporting period.',
			'denominator' => $denominator,
			'numerator' => $numerator,
			'goal' => '10%'
		];


		/**
		 * Stage 3 Measure:
		 * Eligible Professional: The EP must use clinically relevant information from CEHRT to identify patient-specific
		 * educational resources and provide electronic access to those materials to more than 35 percent of unique
		 * patients seen by the EP during the EHR reporting period.
		 *
		 * Stage 3 Measure English Statements:
		 * Numerator: The number of patients in the denominator who were provided electronic access to patient-specific
		 * educational resources using clinically relevant information identified from CEHRT during the EHR reporting period.
		 * Denominator: The number of unique patients seen by the EP or the number of unique patients discharged from
		 * an eligible hospital or CAH inpatient or emergency department (POS 21 or 23) during the EHR reporting period
		 *
		 *
		 * Stage 3 Measure Elements:
		 * Numerator: Provision of electronic access to patient specific education resource(s) identified by the CEHRT.
		 * Denominator:
		 *  Number of patients seen by the EP.
		 *  Number of patients discharged from the EH or CAH.
		 */
		$office_visit_codes = implode("','", $this->office_visit_codes);
		$sth = $this->conn->prepare("SELECT pid FROM encounters WHERE provider_uid = '{$provider_id}' AND service_date BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE) AND visit_category_code IN ('{$office_visit_codes}') GROUP BY pid");
		$sth->execute();
		$patients =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$patients_pids = [];
		foreach($patients as $patient) {
			$patients_pids[] = $patient['pid'];
		}
		$denominator = count($patients_pids);
		$patients_pids = join("','", $patients_pids);

		$sth = $this->conn->prepare("SELECT count(*) as count FROM patient_education_resources WHERE pid IN ('{$patients_pids}') AND provided_date BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE) GROUP BY pid");
		$sth->execute();
		$numerator =  $sth->fetch(PDO::FETCH_ASSOC);
		$numerator = $numerator['count'];

		$records[] = [
			'group' => 'Patient Education',
			'title' => 'Stage 3 Measure',
			'description' => 'Eligible Professional: The EP must use clinically relevant information from CEHRT to identify patient-specific educational resources and provide electronic access to those materials to more than 35 percent of unique patients seen by the EP during the EHR reporting period.',
			'denominator' => $denominator,
			'numerator' => $numerator,
			'goal' => '35%'
		];


		/**
		 * Promoting Interoperability Transition Measure:
		 * The MIPS EC must use clinically relevant information from certified EHR technology to identify patient-specific
		 * educational resources and provide access to those materials to at least one unique patient seen by the MIPS EC.
		 *
		 * Promoting Interoperability Transition English Statements:
		 * Numerator: The number of patients in the denominator who were provided access to patient-specific educational
		 * resources using clinically relevant information identified from certified EHR technology during the performance period.
		 * Denominator: The number of unique patients seen by the MIPS EC during the performance period.
		 *
		 * Promoting Interoperability Transition Measure Elements:
		 * Numerator: Provision of patient specific education resource(s) identified by the CEHRT.
		 * Denominator: Number of patients seen by the EC.
		 */
		$office_visit_codes = implode("','", $this->office_visit_codes);
		$sth = $this->conn->prepare("SELECT pid FROM encounters WHERE provider_uid = '{$provider_id}' AND service_date BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE) AND visit_category_code IN ('{$office_visit_codes}') GROUP BY pid");
		$sth->execute();
		$patients =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$patients_pids = [];
		foreach($patients as $patient) {
			$patients_pids[] = $patient['pid'];
		}
		$denominator = count($patients_pids);
		$patients_pids = join("','", $patients_pids);

		$sth = $this->conn->prepare("SELECT count(*) as count FROM patient_education_resources WHERE pid IN ('{$patients_pids}') AND provided_date BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE) GROUP BY pid");
		$sth->execute();
		$numerator =  $sth->fetch(PDO::FETCH_ASSOC);
		$numerator = $numerator['count'];

		$records[] = [
			'group' => 'Patient Education',
			'title' => 'Promoting Interoperability Transition Measure',
			'description' => 'The MIPS EC must use clinically relevant information from certified EHR technology to identify patient-specific educational resources and provide access to those materials to at least one unique patient seen by the MIPS EC.',
			'denominator' => $denominator,
			'numerator' => $numerator,
			'goal' => '1'
		];

		/**
		 * Promoting Interoperability Measure (2018 only):
		 * The MIPS EC must use clinically relevant information from certified EHR technology to identify patient-specific
		 * educational resources and provide electronic access to those materials to at least one unique patient seen by the MIPS eligible clinician.
		 *
		 * Promoting Interoperability English Statements (2018 only):
		 * umerator: The number of patients in the denominator who were provided electronic access to patient-specific
		 * educational resources using clinically relevant information identified from certified EHR technology during the performance period.
		 * Denominator: The number of unique patients seen by the MIPS EC during the performance period.
		 *
		 * Promoting Interoperability Transition Measure Elements:
		 * Numerator: Provision of electronic access to patient-specific education resource(s) identified by the CEHRT.
		 * Denominator: Number of patients seen by the EC.
		 */
		$office_visit_codes = implode("','", $this->office_visit_codes);
		$sth = $this->conn->prepare("SELECT pid FROM encounters WHERE provider_uid = '{$provider_id}' AND service_date BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE) AND visit_category_code IN ('{$office_visit_codes}') GROUP BY pid");
		$sth->execute();
		$patients =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$patients_pids = [];
		foreach($patients as $patient) {
			$patients_pids[] = $patient['pid'];
		}
		$denominator = count($patients_pids);
		$patients_pids = join("','", $patients_pids);

		$sth = $this->conn->prepare("SELECT count(*) as count FROM patient_education_resources WHERE pid IN ('{$patients_pids}') AND provided_date BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE) GROUP BY pid");
		$sth->execute();
		$numerator =  $sth->fetch(PDO::FETCH_ASSOC);
		$numerator = $numerator['count'];

		$records[] = [
			'group' => 'Patient Education',
			'title' => 'Promoting Interoperability Measure',
			'description' => 'The MIPS EC must use clinically relevant information from certified EHR technology to identify patient-specific educational resources and provide electronic access to those materials to at least one unique patient seen by the MIPS eligible clinician.',
			'denominator' => $denominator,
			'numerator' => $numerator,
			'goal' => '1'
		];

		return $records;

	}

	// Required Test 6 – Patient Generated Health Data
	// TODO

	// Required Test 7 – Support Electronic Referral Loops by Sending Health Information (formerly Transitions of Care)
	// TODO

	// Required Test 8 Receive and Incorporate
	// TODO

	// Required Test 9 – Medication/Clinical Information Reconciliation
	// TODO


	// Required Test 10 – CPOE Medications
	private function getCPOEMedications($provider_id, $start_date, $end_date){

		$records = [];

		/**
		 * Required Test 10 – CPOE Medications
		 * Modified Stage 2 Objective 3 Measure 1 and Stage 3 Objective 4 Measure 1
		 */

		/**
		 * Modified Stage 2 Measure:
		 * Eligible Professional/Eligible Hospital/Critical Access Hospital (EP/EH/CAH): More than 60 percent of
		 * medication orders created by the EP or by authorized providers of the eligible hospital's or CAH's inpatient
		 * or emergency department (POS 21 or 23) during the EHR reporting period are recorded using computerized
		 * provider order entry.
		 *
		 * Modified Stage 2 Measure English Statements:
		 * Numerator: The number of medication orders in the denominator recorded using CPOE.
		 * Denominator: Number of medication orders created by the EP or authorized providers in the eligible hospital's
		 * or CAH's inpatient or emergency department (POS 21 or 23) during the EHR reporting period.
		 *
		 * Modified Stage 2 Measure Elements:
		 * Numerator: Medication order recorded using CPOE.
		 * Denominator: Number of medication orders.
		 */
		$sth = $this->conn->prepare("SELECT m.id FROM patient_medications as m
										  INNER JOIN encounters as e ON e.eid = m.eid
										  INNER JOIN facility as f ON f.id = e.facility AND f.pos_code IN ('21', '23')
											   WHERE e.provider_uid = '{$provider_id}' AND m.date_ordered IS NOT NULL AND m.date_ordered BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE)
											GROUP BY m.id");
		$sth->execute();
		$medications =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$medication_ids = [];
		foreach($medications as $medication) {
			$medication_ids[] = $medication['id'];
		}
		$denominator = count($medication_ids);
		$medication_ids = join("','", $medication_ids);

		$sth = $this->conn->prepare("SELECT count(*) as count FROM patient_medications as m WHERE m.id IN ('{$medication_ids}')");
		$sth->execute();
		$numerator =  $sth->fetch(PDO::FETCH_ASSOC);
		$numerator = $numerator['count'];

		$records[] = [
			'group' => 'CPOE Medications',
			'title' => 'Modified Stage 2 Measure',
			'description' => 'Eligible Professional/Eligible Hospital/Critical Access Hospital (EP/EH/CAH): More than 60 percent of medication orders created by the EP or by authorized providers of the eligible hospital\'s or CAH\'s inpatient or emergency department (POS 21 or 23) during the EHR reporting period are recorded using computerized provider order entry.',
			'denominator' => $denominator,
			'numerator' => $numerator,
			'goal' => '60%'
		];

		/**
		 * Stage 3 Measure:
		 * Eligible Professional/Eligible Hospital/Critical Access Hospital (EP/EH/CAH): More than 60 percent of
		 * diagnostic imaging orders created by the EP or authorized providers of the eligible hospital or CAH inpatient
		 * or emergency department (POS 21 or 23) during the EHR reporting period are recorded using computerized
		 * provider order entry.
		 *
		 * tage 3 Measure English Statements:
		 * Numerator: Numerator: The number of orders in the denominator recorded using CPOE.
		 * Denominator: Number of radiology orders created by the EP or authorized providers in the eligible hospital's
		 * or CAH's inpatient or emergency department (POS 21 or 23) during the EHR reporting period.
		 *
		 * Stage 3 Measure Elements:
		 * Numerator: Radiology order recorded using CPOE.
		 * Denominator: Number of radiology orders.
		 */
		$sth = $this->conn->prepare("SELECT m.id FROM patient_medications as m
										  INNER JOIN encounters as e ON e.eid = m.eid
										  INNER JOIN facility as f ON f.id = e.facility AND f.pos_code IN ('21', '23')
											   WHERE e.provider_uid = '{$provider_id}' AND m.date_ordered IS NOT NULL AND m.date_ordered BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE)
											GROUP BY m.id");
		$sth->execute();
		$medications =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$medication_ids = [];
		foreach($medications as $medication) {
			$medication_ids[] = $medication['id'];
		}
		$denominator = count($medication_ids);
		$medication_ids = join("','", $medication_ids);

		$sth = $this->conn->prepare("SELECT count(*) as count FROM patient_medications as m WHERE m.id IN ('{$medication_ids}')");
		$sth->execute();
		$numerator =  $sth->fetch(PDO::FETCH_ASSOC);
		$numerator = $numerator['count'];

		$records[] = [
			'group' => 'CPOE Medications',
			'title' => 'Stage 3 Measure',
			'description' => 'Eligible Professional/Eligible Hospital/Critical Access Hospital (EP/EH/CAH): More than 60 percent of medication orders created by the EP or authorized providers of the eligible hospital or CAH inpatient or emergency department (POS 21 or 23) during the EHR reporting period are recorded using computerized provider order entry.',
			'denominator' => $denominator,
			'numerator' => $numerator,
			'goal' => '60%'
		];


		return $records;
	}

	// Required Test 11 – CPOE Laboratory
	private function getCPOELaboratory($provider_id, $start_date, $end_date){

		$records = [];

		/**
		 * Required Test 11 – CPOE Laboratory
		 * Modified Stage 2 Objective 3 Measure 2 and Stage 3 Objective 4 Measure 2
		 */


		/**
		 * Modified Stage 2 Measure:
		 * Eligible Professional/Eligible Hospital/Critical Access Hospital (EP/EH/CAH): More than 30 percent of
		 * laboratory orders created by the EP or by authorized providers of the eligible hospital's or CAH's inpatient
		 * or emergency department (POS 21 or 23) during the EHR reporting period are recorded using computerized
		 * provider order entry.
		 *
		 * Modified Stage 2 Measure English Statements:
		 * Numerator: The number of laboratory orders in the denominator recorded using CPOE.
		 * Denominator: Number of laboratory orders created by the EP or authorized providers in the eligible
		 * hospital's or CAH's inpatient or emergency department (POS 21 or 23) during the EHR reporting period.
		 *
		 * Modified Stage 2 Measure Elements:
		 * Numerator: Laboratory order recorded using CPOE.
		 * Denominator: Number of laboratory orders.
		 */
		$sth = $this->conn->prepare("SELECT o.id FROM patient_orders as o
										  INNER JOIN encounters as e ON e.eid = o.eid
										  INNER JOIN facility as f ON f.id = e.facility AND f.pos_code IN ('21', '23')
											   WHERE e.provider_uid = '{$provider_id}' AND o.order_type = 'lab' AND o.date_ordered BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE)
											GROUP BY o.id");
		$sth->execute();
		$orders =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$orders_ids = [];
		foreach($orders as $order) {
			$orders_ids[] = $order['id'];
		}
		$denominator = count($orders_ids);
		$orders_ids = join("','", $orders_ids);

		$sth = $this->conn->prepare("SELECT count(*) as count FROM patient_orders as o WHERE o.id IN ('{$orders_ids}')");
		$sth->execute();
		$numerator =  $sth->fetch(PDO::FETCH_ASSOC);
		$numerator = $numerator['count'];

		$records[] = [
			'group' => 'CPOE Laboratory',
			'title' => 'Modified Stage 2 Measure',
			'description' => 'Eligible Professional/Eligible Hospital/Critical Access Hospital (EP/EH/CAH): More than 30 percent of laboratory orders created by the EP or by authorized providers of the eligible hospital\'s or CAH\'s inpatient or emergency department (POS 21 or 23) during the EHR reporting period are recorded using computerized provider order entry.',
			'denominator' => $denominator,
			'numerator' => $numerator,
			'goal' => '30%'
		];

		/**
		 * Stage 3 Measure:
		 * Eligible Professional/Eligible Hospital/Critical Access Hospital (EP/EH/CAH): More than 60 percent of
		 * laboratory orders created by the EP or authorized providers of the eligible hospital or CAH inpatient or
		 * emergency department (POS 21 or 23) during the EHR reporting period are recorded using computerized
		 * provider order entry.
		 *
		 * Stage 3 Measure English Statements:
		 * Numerator: The number of laboratory orders in the denominator recorded using CPOE.
		 * Denominator: Number of laboratory orders created by the EP or authorized providers in the eligible
		 * hospital or CAH inpatient or emergency department (POS 21 or 23) during the EHR reporting period.
		 *
		 * Stage 3 Measure Elements:
		 * Numerator: Laboratory order recorded using CPOE.
		 * Denominator: Number of laboratory orders.
		 */
		$sth = $this->conn->prepare("SELECT o.id FROM patient_orders as o
										  INNER JOIN encounters as e ON e.eid = o.eid
										  INNER JOIN facility as f ON f.id = e.facility AND f.pos_code IN ('21', '23')
											   WHERE e.provider_uid = '{$provider_id}' AND o.order_type = 'lab' AND o.date_ordered BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE)
											GROUP BY o.id");
		$sth->execute();
		$orders =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$orders_ids = [];
		foreach($orders as $order) {
			$orders_ids[] = $order['id'];
		}
		$denominator = count($orders_ids);
		$orders_ids = join("','", $orders_ids);

		$sth = $this->conn->prepare("SELECT count(*) as count FROM patient_orders as o WHERE o.id IN ('{$orders_ids}')");
		$sth->execute();
		$numerator =  $sth->fetch(PDO::FETCH_ASSOC);
		$numerator = $numerator['count'];

		$records[] = [
			'group' => 'CPOE Laboratory',
			'title' => 'Stage 3 Measure',
			'description' => 'Eligible Professional/Eligible Hospital/Critical Access Hospital (EP/EH/CAH): More than 60 percent of laboratory orders created by the EP or authorized providers of the eligible hospital or CAH inpatient or emergency department (POS 21 or 23) during the EHR reporting period are recorded using computerized provider order entry.',
			'denominator' => $denominator,
			'numerator' => $numerator,
			'goal' => '60%'
		];

		return $records;
	}

	// Required Test 12 – CPOE Radiology/Diagnostic Imaging
	private function getCPOERadiology($provider_id, $start_date, $end_date){

		$records = [];

		/**
		 * Required Test 12 – CPOE Radiology/Diagnostic Imaging
		 * Modified Stage 2 Objective 3 Measure 3 and Stage 3 Objective 4 Measure 3
		 */


		/**
		 * Modified Stage 2 Measure:
		 * Eligible Professional/Eligible Hospital/Critical Access Hospital (EP/EH/CAH): More than 30 percent of
		 * radiology orders created by the EP or by authorized providers of the eligible hospital's or CAH's inpatient
		 * or emergency department (POS 21 or 23) during the EHR reporting period are recorded using computerized
		 * provider order entry.
		 *
		 * Modified Stage 2 Measure English Statements:
		 * Numerator: The number of radiology orders in the denominator recorded using CPOE.
		 * Denominator: Number of radiology orders created by the EP or authorized providers in the eligible hospital's
		 * or CAH's inpatient or emergency department (POS 21 or 23) during the EHR reporting period.
		 *
		 * Modified Stage 2 Measure Elements:
		 * Numerator: Radiology order recorded using CPOE.
		 * Denominator: Number of radiology orders.
		 */
		$sth = $this->conn->prepare("SELECT o.id as o FROM patient_orders as o
										  INNER JOIN encounters as e ON e.eid = o.eid
										  INNER JOIN facility as f ON f.id = e.facility AND f.pos_code IN ('21', '23')
											   WHERE e.provider_uid = '{$provider_id}' AND o.order_type = 'rad' AND o.date_ordered BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE)
											GROUP BY o.id");
		$sth->execute();
		$orders =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$orders_ids = [];
		foreach($orders as $order) {
			$orders_ids[] = $order['id'];
		}
		$denominator = count($orders_ids);
		$orders_ids = join("','", $orders_ids);

		$sth = $this->conn->prepare("SELECT count(*) as count FROM patient_orders as o WHERE o.id IN ('{$orders_ids}')");
		$sth->execute();
		$numerator =  $sth->fetch(PDO::FETCH_ASSOC);
		$numerator = $numerator['count'];

		$records[] = [
			'group' => 'CPOE Radiology/Diagnostic Imaging',
			'title' => 'Modified Stage 2 Measure',
			'description' => 'Eligible Professional/Eligible Hospital/Critical Access Hospital (EP/EH/CAH): More than 30 percent of radiology orders created by the EP or by authorized providers of the eligible hospital\'s or CAH\'s inpatient or emergency department (POS 21 or 23) during the EHR reporting period are recorded using computerized provider order entry',
			'denominator' => $denominator,
			'numerator' => $numerator,
			'goal' => '30%'
		];

		/**
		 * Stage 3 Measure:
		 * Eligible Professional/Eligible Hospital/Critical Access Hospital (EP/EH/CAH): More than 60 percent of
		 * diagnostic imaging orders created by the EP or authorized providers of the eligible hospital or CAH inpatient
		 * or emergency department (POS 21 or 23) during the EHR reporting period are recorded using computerized
		 * provider order entry.
		 *
		 * tage 3 Measure English Statements:
		 * Numerator: Numerator: The number of orders in the denominator recorded using CPOE.
		 * Denominator: Number of radiology orders created by the EP or authorized providers in the eligible hospital's
		 * or CAH's inpatient or emergency department (POS 21 or 23) during the EHR reporting period.
		 *
		 * Stage 3 Measure Elements:
		 * Numerator: Radiology order recorded using CPOE.
		 * Denominator: Number of radiology orders.
		 */
		$sth = $this->conn->prepare("SELECT o.id as o FROM patient_orders as o
										  INNER JOIN encounters as e ON e.eid = o.eid
										  INNER JOIN facility as f ON f.id = e.facility AND f.pos_code IN ('21', '23')
											   WHERE e.provider_uid = '{$provider_id}' AND o.order_type = 'rad' AND o.date_ordered BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE)
											GROUP BY o.id");
		$sth->execute();
		$orders =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$orders_ids = [];
		foreach($orders as $order) {
			$orders_ids[] = $order['id'];
		}
		$denominator = count($orders_ids);
		$orders_ids = join("','", $orders_ids);

		$sth = $this->conn->prepare("SELECT count(*) as count FROM patient_orders as o WHERE o.id IN ('{$orders_ids}')");
		$sth->execute();
		$numerator =  $sth->fetch(PDO::FETCH_ASSOC);
		$numerator = $numerator['count'];

		$records[] = [
			'group' => 'CPOE Radiology/Diagnostic Imaging',
			'title' => 'Stage 3 Measure',
			'description' => 'Eligible Professional/Eligible Hospital/Critical Access Hospital (EP/EH/CAH): More than 60 percent of diagnostic imaging orders created by the EP or authorized providers of the eligible hospital or CAH inpatient or emergency department (POS 21 or 23) during the EHR reporting period are recorded using computerized provider order entry.',
			'denominator' => $denominator,
			'numerator' => $numerator,
			'goal' => '60%'
		];





		return $records;
	}

}