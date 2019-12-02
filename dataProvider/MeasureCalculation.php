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

	/**
	 *
	 CREATE TABLE `a_measures` (
		`id` INT NOT NULL,
		`group` VARCHAR(120) NULL,
		`title` VARCHAR(120) NULL,
		`description` TEXT NULL,
		`denominator` VARCHAR(45) NULL,
		`numerator` VARCHAR(45) NULL,
		`denominator_pids` TEXT NULL,
		`numerator_pids` TEXT NULL,
		`goal` VARCHAR(45) NULL,
		PRIMARY KEY (`id`));

	 */


	public function getReportMeasureByDates($measure, $provider_id, $start_date, $end_date){

		if(is_array($provider_id)){
			$provider_id = implode("','", $provider_id);
		}

		$results = [];
		$sth = $this->conn->prepare("SELECT * FROM _measures WHERE `provider_id` IN ('{$provider_id}') AND `measure` = ?");
		$sth->execute([$provider_id,$measure]);
		$results =  $sth->fetchAll(PDO::FETCH_ASSOC);
		//return $results;

		try{
			if($measure == 'ePrescribing'){
				$results = $this->getPrescribingReportByDates($provider_id, $start_date, $end_date);
			} else
			if($measure == 'ProvidePatientsElectronicAccess'){
				$results = $this->getProvidePatientsElectronicAccessReportByDates($provider_id, $start_date, $end_date);
			} else
			if($measure == 'PatientEducation'){
				$results = $this->getPatientEducationReportByDates($provider_id, $start_date, $end_date);
			} else
			if($measure == 'ViewDownloadTransmit'){
				$results = $this->getViewDownloadTransmitReportByDates($provider_id, $start_date, $end_date);
			} else
			if($measure == 'SecureMessaging'){
				$results = $this->getSecureMessagingReportByDates($provider_id, $start_date, $end_date);
			} else
			if($measure == 'PatientGeneratedHealthData'){
				$results = $this->getPatientGeneratedHealthDataReportByDates($provider_id, $start_date, $end_date);
			} else
			if($measure == 'SupportElectronicReferralLoopsSending'){
				return $this->getSupportElectronicReferralLoopsSendingReportByDates($provider_id, $start_date, $end_date);
			} else
			if($measure == 'ReceiveAndIncorporate'){
				$results = $this->getReceiveAndIncorporateReportByDates($provider_id, $start_date, $end_date);
			} else
			if($measure == 'MedicationClinicalInformationReconciliation'){
				$results = $this->getMedicationClinicalInformationReconciliationReportByDates($provider_id, $start_date, $end_date);
			} else
			if($measure == 'CPOEMedications'){
				$results = $this->getCPOEMedications($provider_id, $start_date, $end_date);
			} else
			if($measure == 'CPOELaboratory'){
				$results = $this->getCPOELaboratory($provider_id, $start_date, $end_date);
			} else
			if($measure == 'CPOERadiology'){
				$results = $this->getCPOERadiology($provider_id, $start_date, $end_date);
			}

			$this->conn->exec("DELETE FROM `_measures` WHERE provider_id = '{$provider_id}' AND measure = '{$measure}'");

			foreach ($results as $result){
				$sql = "INSERT INTO `_measures` (`provider_id`,`measure`,`group`, `title`, `description`, `denominator`, `numerator`, `denominator_pids`, `numerator_pids`, `goal`) VALUES
                     (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
				$sth = $this->conn->prepare($sql);
				$sth->execute([
					$provider_id,
					$measure,
					$result['group'],
					$result['title'],
					$result['description'],
					$result['denominator'],
					$result['numerator'],
					$result['denominator_pids'],
					$result['numerator_pids'],
					$result['goal']
				]);
			}

			return $results;
		}catch (Exception $e){
			return $results;
		}
	}

	// ....
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
		$sth = $this->conn->prepare("SELECT id, pid FROM patient_medications WHERE uid = '{$provider_id}' AND date_ordered BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE) AND date_ordered IS NOT NULL");
		$sth->execute();
		$ordered_prescriptions =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$ordered_prescriptions_ids = [];
		$denominator_pids = [];
		foreach($ordered_prescriptions as $ordered_prescription) {
			$ordered_prescriptions_ids[] = $ordered_prescription['id'];
			if(!in_array($ordered_prescription['pid'], $denominator_pids)) $denominator_pids[] = $ordered_prescription['pid'];
		}
		$denominator = count($ordered_prescriptions_ids);
		$ordered_prescriptions_ids_str = join("','", $ordered_prescriptions_ids);

		$sth = $this->conn->prepare("SELECT pid FROM erx_prescriptions WHERE orderId IN ('{$ordered_prescriptions_ids_str}') GROUP BY orderId");
		$sth->execute();
		$numerator_records =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$numerator_pids = [];
		foreach($numerator_records as $numerator_record) {
			if(!in_array($numerator_record['pid'], $numerator_pids)) $numerator_pids[] = $numerator_record['pid'];
		}
		$numerator = count($numerator_records);

		$records[] = [
			'group' => '1. ePrescribing',
			'title' => 'Modified Stage 2 Measure',
			'description' => 'Eligible Professional (EP): More than 50 percent of all permissible prescriptions written by the EP are queried for a drug formulary and transmitted electronically using Certified Health IT.',
			'denominator' => $denominator,
			'numerator' => $numerator,
			'denominator_pids' => implode(',', $denominator_pids),
			'numerator_pids' => implode(',', $numerator_pids),
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
		$sth = $this->conn->prepare("SELECT id, pid FROM patient_medications WHERE uid = '{$provider_id}' AND date_ordered BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE) AND date_ordered IS NOT NULL");
		$sth->execute();
		$ordered_prescriptions =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$ordered_prescriptions_ids = [];
		$denominator_pids = [];
		foreach($ordered_prescriptions as $ordered_prescription) {
			$ordered_prescriptions_ids[] = $ordered_prescription['id'];
			if(!in_array($ordered_prescription['pid'], $denominator_pids)) $denominator_pids[] = $ordered_prescription['id'];
		}
		$denominator = count($ordered_prescriptions_ids);
		$ordered_prescriptions_ids_str = join("','", $ordered_prescriptions_ids);

		$sth = $this->conn->prepare("SELECT pid FROM erx_prescriptions WHERE orderId IN ('{$ordered_prescriptions_ids_str}') GROUP BY orderId");
		$sth->execute();
		$numerator_records =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$numerator_pids = [];
		foreach($numerator_records as $numerator_record) {
			if(!in_array($numerator_record['pid'], $numerator_pids)) $numerator_pids[] = $numerator_record['pid'];
		}
		$numerator = count($numerator_records);

		$records[] = [
			'group' => '1. ePrescribing',
			'title' => 'Stage 3 Measure',
			'description' => 'Eligible Professional (EP): More than 60 percent of all permissible prescriptions written by the EP are queried for a drug formulary and transmitted electronically using CEHRT.',
			'denominator' => $denominator,
			'numerator' => $numerator,
			'denominator_pids' => implode(',', $denominator_pids),
			'numerator_pids' => implode(',', $numerator_pids),
			'goal' => '60%'
		];

		return $records;

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
		$sth = $this->conn->prepare("SELECT id, pid FROM patient_medications WHERE uid = '{$provider_id}' AND date_ordered BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE) AND date_ordered IS NOT NULL");
		$sth->execute();
		$ordered_prescriptions =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$ordered_prescriptions_ids = [];
		$denominator_pids = [];
		foreach($ordered_prescriptions as $ordered_prescription) {
			$ordered_prescriptions_ids[] = $ordered_prescription['id'];
			if(!in_array($ordered_prescription['pid'], $denominator_pids)) $denominator_pids[] = $ordered_prescription['id'];
		}
		$denominator = count($ordered_prescriptions_ids);
		$ordered_prescriptions_ids_str = join("','", $ordered_prescriptions_ids);

		$sth = $this->conn->prepare("SELECT pid FROM erx_prescriptions WHERE orderId IN ('{$ordered_prescriptions_ids_str}') GROUP BY orderId");
		$sth->execute();
		$numerator_records =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$numerator_pids = [];
		foreach($numerator_records as $numerator_record) {
			if(!in_array($numerator_record['pid'], $numerator_pids)) $numerator_pids[] = $numerator_record['pid'];
		}
		$numerator = count($numerator_records);

		$records[] = [
			'group' => '1. ePrescribing',
			'title' => 'Promoting Interoperability Transition Measure',
			'description' => 'Eligible Clinician (EC): At least one permissible prescription written by the MIPS EC is queried for a drug formulary and transmitted electronically using certified EHR technology.',
			'denominator' => $denominator,
			'numerator' => $numerator,
			'denominator_pids' => implode(',', $denominator_pids),
			'numerator_pids' => implode(',', $numerator_pids),
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
		$sth = $this->conn->prepare("SELECT id, pid FROM patient_medications WHERE uid = '{$provider_id}' AND date_ordered BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE) AND date_ordered IS NOT NULL");
		$sth->execute();
		$ordered_prescriptions =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$ordered_prescriptions_ids = [];
		$denominator_pids = [];
		foreach($ordered_prescriptions as $ordered_prescription) {
			$ordered_prescriptions_ids[] = $ordered_prescription['id'];
			if(!in_array($ordered_prescription['pid'], $denominator_pids)) $denominator_pids[] = $ordered_prescription['id'];
		}
		$denominator = count($ordered_prescriptions_ids);
		$ordered_prescriptions_ids_str = join("','", $ordered_prescriptions_ids);

		$sth = $this->conn->prepare("SELECT pid FROM erx_prescriptions WHERE orderId IN ('{$ordered_prescriptions_ids_str}') GROUP BY orderId");
		$sth->execute();
		$numerator_records =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$numerator_pids = [];
		foreach($numerator_records as $numerator_record) {
			if(!in_array($numerator_record['pid'], $numerator_pids)) $numerator_pids[] = $numerator_record['pid'];
		}
		$numerator = count($numerator_records);

		$records[] = [
			'group' => '1. ePrescribing',
			'title' => 'Promoting Interoperability Measure',
			'description' => 'EC: At least one permissible prescription written by the MIPS EC is queried for a drug formulary and transmitted electronically using certified EHR technology.',
			'denominator' => $denominator,
			'numerator' => $numerator,
			'denominator_pids' => implode(',', $denominator_pids),
			'numerator_pids' => implode(',', $numerator_pids),
			'goal' => '1'
		];

		return $records;

	}

	// .... b
	// Required Test 2a, b, or c – Provide Patients Electronic Access to Their Health Information (formerly Patient Electronic Access)
	public function getProvidePatientsElectronicAccessReportByDates($provider_id, $start_date, $end_date){

		$records = [];

		/**
		 * Required Test 2a, b, or c – Provide Patients Electronic Access to Their Health Information (formerly Patient Electronic Access)
		 * Modified Stage 2 Objective 8 Measure 1 and Stage 3 Objective 5 Measure 1
		 * Promoting Interoperability Transition Objective 3 Measure 1 and Promoting Interoperability Objective 3 Measure 1
		 */

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

		$sth = $this->conn->prepare("SELECT pid FROM encounters WHERE provider_uid = '{$provider_id}' AND service_date BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE) AND service_date IS NOT NULL GROUP BY pid");
		$sth->execute();
		$ordered_prescriptions =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$denominator_pids = [];
		foreach($ordered_prescriptions as $ordered_prescription) {
			if(!in_array($ordered_prescription['pid'], $denominator_pids)) $denominator_pids[] = $ordered_prescription['pid'];
		}
		$denominator = count($ordered_prescription);
		$ordered_prescription_str = join("','", $ordered_prescription);

		$sth = $this->conn->prepare("SELECT e.pid FROM encounters as e
										  INNER JOIN audit_log as a ON a.pid = e.pid AND e.eid = a.eid AND a.event_date < DATE_ADD(e.service_date, INTERVAL 48 HOUR)
											   WHERE e.pid IN ('{$ordered_prescription_str}') GROUP BY e.pid");
		$sth->execute();
		$numerator_records =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$numerator_pids = [];
		foreach($numerator_records as $numerator_record) {
			if(!in_array($numerator_record['pid'], $numerator_pids)) $numerator_pids[] = $numerator_record['pid'];
		}
		$numerator = count($numerator_records);

		$records[] = [
			'group' => '2. Provide Patients Electronic Access',
			'title' => 'Modified Stage 2 Measure',
			'description' => 'Eligible Professional (EP): More than 50 percent of all unique patients seen by the EP during the EHR reporting period are provided timely access to view online, download, and transmit to a third party their health information subject to the EP\'s discretion to withhold certain information.',
			'denominator' => $denominator,
			'numerator' => $numerator,
			'denominator_pids' => implode(',', $denominator_pids),
			'numerator_pids' => implode(',', $numerator_pids),
			'goal' => '50%'
		];

		/**
		 * Stage 3 Measure:
		 * Eligible Professional (EP): For more than 80 percent of all unique patients seen by the EP: (1) The patient
		 * (or the patient-authorized representative) is provided timely access to view online, download, and transmit
		 * his or her health information; and (2) The provider ensures the patient’s health information is available
		 * for the patient (or patient-authorized representative) to access using any application of their choice that
		 * is configured to meet the technical specifications of the API in the provider’s CEHRT.
		 *
		 * Stage 3 Measure English Statements:
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

		$sth = $this->conn->prepare("SELECT pid FROM encounters WHERE provider_uid = '{$provider_id}' AND service_date BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE) AND service_date IS NOT NULL GROUP BY pid");
		$sth->execute();
		$ordered_prescriptions =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$denominator_pids = [];
		foreach($ordered_prescriptions as $ordered_prescription) {
			if(!in_array($ordered_prescription['pid'], $denominator_pids)) $denominator_pids[] = $ordered_prescription['pid'];
		}
		$denominator = count($ordered_prescription);
		$ordered_prescription_str = join("','", $ordered_prescription);

		$sth = $this->conn->prepare("SELECT e.pid FROM encounters as e
										  INNER JOIN audit_log as a ON a.pid = e.pid AND e.eid = a.eid AND a.event_date < DATE_ADD(e.service_date, INTERVAL 48 HOUR)
											   WHERE e.pid IN ('{$ordered_prescription_str}') GROUP BY e.pid");
		$sth->execute();
		$numerator_records =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$numerator_pids = [];
		foreach($numerator_records as $numerator_record) {
			if(!in_array($numerator_record['pid'], $numerator_pids)) $numerator_pids[] = $numerator_record['pid'];
		}
		$numerator = count($numerator_records);

		$records[] = [
			'group' => '2. Provide Patients Electronic Access',
			'title' => 'Stage 3 Measure',
			'description' => 'Eligible Professional (EP): For more than 80 percent of all unique patients seen by the EP: (1) The patient (or the patient-authorized representative) is provided timely access to view online, download, and transmit his or her health information; and (2) The provider ensures the patient’s health information is available for the patient (or patient-authorized representative) to access using any application of their choice that is configured to meet the technical specifications of the API in the provider’s CEHRT.',
			'denominator' => $denominator,
			'numerator' => $numerator,
			'denominator_pids' => implode(',', $denominator_pids),
			'numerator_pids' => implode(',', $numerator_pids),
			'goal' => '80%'
		];

		return $records;

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

		$sth = $this->conn->prepare("SELECT pid FROM encounters WHERE provider_uid = '{$provider_id}' AND service_date BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE) AND service_date IS NOT NULL GROUP BY pid");
		$sth->execute();
		$ordered_prescriptions =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$denominator_pids = [];
		foreach($ordered_prescriptions as $ordered_prescription) {
			if(!in_array($ordered_prescription['pid'], $denominator_pids)) $denominator_pids[] = $ordered_prescription['pid'];
		}
		$denominator = count($ordered_prescription);
		$ordered_prescription_str = join("','", $ordered_prescription);

		$sth = $this->conn->prepare("SELECT e.pid FROM encounters as e
										  INNER JOIN audit_log as a ON a.pid = e.pid AND e.eid = a.eid AND a.event_date < DATE_ADD(e.service_date, INTERVAL 48 HOUR)
											   WHERE e.pid IN ('{$ordered_prescription_str}') GROUP BY e.pid");
		$sth->execute();
		$numerator_records =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$numerator_pids = [];
		foreach($numerator_records as $numerator_record) {
			if(!in_array($numerator_record['pid'], $numerator_pids)) $numerator_pids[] = $numerator_record['pid'];
		}
		$numerator = count($numerator_records);

		$records[] = [
			'group' => '2. Provide Patients Electronic Access',
			'title' => 'Promoting Interoperability Transition Measure',
			'description' => 'At least one patient seen by the MIPS EC during the performance period is provided timely access to view online, download, and transmit to a third party their health information subject to the MIPS EC\'s discretion to withhold certain information.',
			'denominator' => $denominator,
			'numerator' => $numerator,
			'denominator_pids' => implode(',', $denominator_pids),
			'numerator_pids' => implode(',', $numerator_pids),
			'goal' => '1'
		];

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

		$sth = $this->conn->prepare("SELECT pid FROM encounters WHERE provider_uid = '{$provider_id}' AND service_date BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE) AND service_date IS NOT NULL GROUP BY pid");
		$sth->execute();
		$ordered_prescriptions =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$denominator_pids = [];
		foreach($ordered_prescriptions as $ordered_prescription) {
			if(!in_array($ordered_prescription['pid'], $denominator_pids)) $denominator_pids[] = $ordered_prescription['pid'];
		}
		$denominator = count($ordered_prescription);
		$ordered_prescription_str = join("','", $ordered_prescription);

		$sth = $this->conn->prepare("SELECT e.pid FROM encounters as e
										  INNER JOIN audit_log as a ON a.pid = e.pid AND e.eid = a.eid AND a.event_date < DATE_ADD(e.service_date, INTERVAL 48 HOUR)
											   WHERE e.pid IN ('{$ordered_prescription_str}') GROUP BY e.pid");
		$sth->execute();
		$numerator_records =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$numerator_pids = [];
		foreach($numerator_records as $numerator_record) {
			if(!in_array($numerator_record['pid'], $numerator_pids)) $numerator_pids[] = $numerator_record['pid'];
		}
		$numerator = count($numerator_records);

		$records[] = [
			'group' => '2. Provide Patients Electronic Access',
			'title' => 'Promoting Interoperability Measure',
			'description' => 'EC: For at least one unique patient seen by the MIPS EC (1) the patient (or the patient authorized representative) is provided timely access to view online, download, and transmit his or her health information; and (2) the MIPS EC ensures the patient\'s health information is available for the patient (or patient-authorized representative) to access using any application of their choice that is configured to meet the technical specifications of the API in the MIPS EC\'s CEHRT.',
			'denominator' => $denominator,
			'numerator' => $numerator,
			'denominator_pids' => implode(',', $denominator_pids),
			'numerator_pids' => implode(',', $numerator_pids),
			'goal' => '1'
		];

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
		$sth = $this->conn->prepare("SELECT pid FROM encounters WHERE provider_uid IN ('{$provider_id}') AND service_date BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE) AND visit_category_code IN ('{$office_visit_codes}') GROUP BY pid");
		$sth->execute();
		$patients =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$patients_pids = [];
		$denominator_pids = [];
		foreach($patients as $patient) {
			if(!in_array($patient['pid'], $denominator_pids)) $denominator_pids[] = $patient['pid'];
			$patients_pids[] = $patient['pid'];
		}
		$denominator = count($patients_pids);
		$patients_pids = join("','", $patients_pids);

		$sth = $this->conn->prepare("SELECT pid FROM patient_education_resources WHERE pid IN ('{$patients_pids}') AND provided_date BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE) GROUP BY pid");
		$numerator_records =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$numerator_pids = [];
		foreach($numerator_records as $numerator_record) {
			if(!in_array($numerator_record['pid'], $numerator_pids)) $numerator_pids[] = $numerator_record['pid'];
		}
		$numerator = count($numerator_records);

		$records[] = [
			'group' => '3. Patient Education',
			'title' => 'Modified Stage 2 Measure',
			'description' => 'Eligible Professional (EP): Patient-specific education resources identified by Certified Health IT Module (CEHRT) are provided to patients for more than 10 percent of all unique patients with office visits seen by the EP during the reporting period.',
			'denominator' => $denominator,
			'numerator' => $numerator,
			'denominator_pids' => implode(',', $denominator_pids),
			'numerator_pids' => implode(',', $numerator_pids),
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
		$sth = $this->conn->prepare("SELECT pid FROM encounters WHERE provider_uid IN ('{$provider_id}') AND service_date BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE) AND visit_category_code IN ('{$office_visit_codes}') GROUP BY pid");
		$sth->execute();
		$patients =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$patients_pids = [];
		$denominator_pids = [];
		foreach($patients as $patient) {
			if(!in_array($patient['pid'], $denominator_pids)) $denominator_pids[] = $patient['pid'];
			$patients_pids[] = $patient['pid'];
		}
		$denominator = count($patients_pids);
		$patients_pids = join("','", $patients_pids);

		$sth = $this->conn->prepare("SELECT pid FROM patient_education_resources WHERE pid IN ('{$patients_pids}') AND provided_date BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE) GROUP BY pid");
		$numerator_records =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$numerator_pids = [];
		foreach($numerator_records as $numerator_record) {
			if(!in_array($numerator_record['pid'], $numerator_pids)) $numerator_pids[] = $numerator_record['pid'];
		}
		$numerator = count($numerator_records);

		$records[] = [
			'group' => '3. Patient Education',
			'title' => 'Stage 3 Measure',
			'description' => 'Eligible Professional: The EP must use clinically relevant information from CEHRT to identify patient-specific educational resources and provide electronic access to those materials to more than 35 percent of unique patients seen by the EP during the EHR reporting period.',
			'denominator' => $denominator,
			'numerator' => $numerator,
			'denominator_pids' => implode(',', $denominator_pids),
			'numerator_pids' => implode(',', $numerator_pids),
			'goal' => '35%'
		];

		return $records;

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
		$sth = $this->conn->prepare("SELECT pid FROM encounters WHERE provider_uid IN ('{$provider_id}') AND service_date BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE) AND visit_category_code IN ('{$office_visit_codes}') GROUP BY pid");
		$sth->execute();
		$patients =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$patients_pids = [];
		$denominator_pids = [];
		foreach($patients as $patient) {
			if(!in_array($patient['pid'], $denominator_pids)) $denominator_pids[] = $patient['pid'];
			$patients_pids[] = $patient['pid'];
		}
		$denominator = count($patients_pids);
		$patients_pids = join("','", $patients_pids);

		$sth = $this->conn->prepare("SELECT pid FROM patient_education_resources WHERE pid IN ('{$patients_pids}') AND provided_date BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE) GROUP BY pid");
		$numerator_records =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$numerator_pids = [];
		foreach($numerator_records as $numerator_record) {
			if(!in_array($numerator_record['pid'], $numerator_pids)) $numerator_pids[] = $numerator_record['pid'];
		}
		$numerator = count($numerator_records);

		$records[] = [
			'group' => '3. Patient Education',
			'title' => 'Promoting Interoperability Transition Measure',
			'description' => 'The MIPS EC must use clinically relevant information from certified EHR technology to identify patient-specific educational resources and provide access to those materials to at least one unique patient seen by the MIPS EC.',
			'denominator' => $denominator,
			'numerator' => $numerator,
			'denominator_pids' => implode(',', $denominator_pids),
			'numerator_pids' => implode(',', $numerator_pids),
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
		$sth = $this->conn->prepare("SELECT pid FROM encounters WHERE provider_uid IN ('{$provider_id}') AND service_date BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE) AND visit_category_code IN ('{$office_visit_codes}') GROUP BY pid");
		$sth->execute();
		$patients =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$patients_pids = [];
		$denominator_pids = [];
		foreach($patients as $patient) {
			if(!in_array($patient['pid'], $denominator_pids)) $denominator_pids[] = $patient['pid'];
			$patients_pids[] = $patient['pid'];
		}
		$denominator = count($patients_pids);
		$patients_pids = join("','", $patients_pids);

		$sth = $this->conn->prepare("SELECT pid FROM patient_education_resources WHERE pid IN ('{$patients_pids}') AND provided_date BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE) GROUP BY pid");
		$numerator_records =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$numerator_pids = [];
		foreach($numerator_records as $numerator_record) {
			if(!in_array($numerator_record['pid'], $numerator_pids)) $numerator_pids[] = $numerator_record['pid'];
		}
		$numerator = count($numerator_records);

		$records[] = [
			'group' => '3. Patient Education',
			'title' => 'Promoting Interoperability Measure',
			'description' => 'The MIPS EC must use clinically relevant information from certified EHR technology to identify patient-specific educational resources and provide electronic access to those materials to at least one unique patient seen by the MIPS eligible clinician.',
			'denominator' => $denominator,
			'numerator' => $numerator,
			'denominator_pids' => implode(',', $denominator_pids),
			'numerator_pids' => implode(',', $numerator_pids),
			'goal' => '1'
		];

		return $records;

	}

	// Required Test 4a, b, or c – View, Download, Transmit
	public function getViewDownloadTransmitReportByDates($provider_id, $start_date, $end_date){

		$records = [];

		/**
		 * Required Test 4a, b, or c – View, Download, Transmit
		 * Modified Stage 2 Objective 8 Measure 2 and Stage 3 Objective 6 Measure 1
		 * Promoting Interoperability Transition Objective 3 Measure 2 and Promoting Interoperability Objective 4 Measure 1
		 */


		/**
		 * Modified Stage 2 Measure:
		 * Eligible Professional (EP): For an EHR reporting period in 2017, more than 5 percent of unique patients seen
		 * by the EP during the EHR reporting period (or his or her authorized representatives) view, download, or
		 * transmit to a third party their health information during the reporting period.
		 *
		 * MModified Stage 2 Measure English Statements:
		 * Numerator: The number of patients (or patient-authorized representative) in the denominator who view,
		 * download, or transmit to a third party their health information.
		 * Denominator: Number of unique patients seen by the EP during the EHR reporting period.
		 *
		 * Modified Stage 2 Measure Elements:
		 * Numerator: Patient/authorized representative views, downloads, or transmits their information.
		 * Denominator: Number of patients seen by the EP.
		 */

		$sth = $this->conn->prepare("SELECT pid, eid FROM encounters WHERE provider_uid IN ('{$provider_id}') AND service_date BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE) AND service_date IS NOT NULL GROUP BY pid");
		$sth->execute();
		$ordered_prescriptions =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$denominator_pids = [];
		$denominator_eids = [];
		foreach($ordered_prescriptions as $ordered_prescription) {
			if(!in_array($ordered_prescription['pid'], $denominator_pids)) $denominator_pids[] = $ordered_prescription['pid'];
			$denominator_eids[] = $ordered_prescription['eid'];
		}
		$denominator = count($denominator_pids);
		$denominator_eids_str = join("','", $denominator_eids);

		$sth = $this->conn->prepare("SELECT pid FROM audit_log as a WHERE a.eid IN ('{$denominator_eids_str}') AND event IN ('CCDA_VDT') GROUP BY a.pid");
		$sth->execute();
		$numerator_records =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$numerator_pids = [];
		foreach($numerator_records as $numerator_record) {
			if(!in_array($numerator_record['pid'], $numerator_pids)) $numerator_pids[] = $numerator_record['pid'];
		}
		$numerator = count($numerator_records);

		$records[] = [
			'group' => '4. View, Download, Transmit',
			'title' => 'Modified Stage 2 Measure',
			'description' => 'Eligible Professional (EP): For an EHR reporting period in 2017, more than 5 percent of unique patients seen by the EP during the EHR reporting period (or his or her authorized representatives) view, download, or transmit to a third party their health information during the reporting period.',
			'denominator' => $denominator,
			'numerator' => $numerator,
			'denominator_pids' => implode(',', $denominator_pids),
			'numerator_pids' => implode(',', $numerator_pids),
			'goal' => '5%'
		];


		/**
		 * Stage 3 Measure:
		 * Eligible Professional (EP): During the EHR reporting period, more than 10 percent of all unique patients
		 * (or their authorized representatives) seen by the EP actively engage with the electronic health record made
		 * accessible by the provider and either: (1) view, download, or transmit to a third party their health
		 * information; or (2) access their health information through the use of an API that can be used by
		 * applications chosen by the patient and configured to the API in the provider's CEHRT; or (3) a combination
		 * of (1) and (2).
		 *
		 * Stage 3 Measure English Statements:
		 * Numerator: The number of unique patients (or their authorized representatives) in the denominator who have
		 * viewed online, downloaded, or transmitted to a third party the patient's health information during the EHR
		 * reporting period and the number of unique patients (or their authorized representatives) in the denominator
		 * who have accessed their health information through the use of an API during the EHR reporting period.
		 * Denominator: Number of unique patients seen by the EP during the EHR reporting period.
		 *
		 *
		 * Stage 3 Measure Elements:
		 * Numerator:
		 *  Patient (or authorized representative) views, downloads, or transmits their information;
		 *  Patient (or authorized representative) accesses their information via API.
		 * Denominator:
		 *  Denominator: Number of patients seen by the EP.
		 */

		$sth = $this->conn->prepare("SELECT pid, eid FROM encounters WHERE provider_uid IN ('{$provider_id}') AND service_date BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE) AND service_date IS NOT NULL GROUP BY pid");
		$sth->execute();
		$ordered_prescriptions =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$denominator_pids = [];
		$denominator_eids = [];
		foreach($ordered_prescriptions as $ordered_prescription) {
			if(!in_array($ordered_prescription['pid'], $denominator_pids)) $denominator_pids[] = $ordered_prescription['pid'];
			$denominator_eids[] = $ordered_prescription['eid'];
		}
		$denominator = count($denominator_pids);
		$denominator_eids_str = join("','", $denominator_eids);

		$sth = $this->conn->prepare("SELECT pid FROM audit_log as a WHERE a.eid IN ('{$denominator_eids_str}') AND event IN ('CCDA_VDT') GROUP BY a.pid");
		$sth->execute();
		$numerator_records =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$numerator_pids = [];
		foreach($numerator_records as $numerator_record) {
			if(!in_array($numerator_record['pid'], $numerator_pids)) $numerator_pids[] = $numerator_record['pid'];
		}
		$numerator = count($numerator_records);

		$records[] = [
			'group' => '4. View, Download, Transmit',
			'title' => 'Stage 3 Measure',
			'description' => 'Eligible Professional (EP): During the EHR reporting period, more than 10 percent of all unique patients (or their authorized representatives) seen by the EP actively engage with the electronic health record made accessible by the provider and either: (1) view, download, or transmit to a third party their health information; or (2) access their health information through the use of an API that can be used by applications chosen by the patient and configured to the API in the provider\'s CEHRT; or (3) a combination of (1) and (2).',
			'denominator' => $denominator,
			'numerator' => $numerator,
			'denominator_pids' => implode(',', $denominator_pids),
			'numerator_pids' => implode(',', $numerator_pids),
			'goal' => '10%'
		];

		return $records;

		/**
		 * Promoting Interoperability Transition Measure:
		 * At least one patient seen by the MIPS EC during the performance period (or patient-authorized representative)
		 * views, downloads, or transmits their health information to a third party during the performance period.
		 *
		 * Promoting Interoperability Transition English Statements:
		 * Numerator: Numerator: The number of unique patients in the denominator (or their authorized representatives)
		 * who have viewed online, downloaded, or transmitted to a third party the patient’s health information during
		 * the performance period.
		 * Denominator: Number of unique patients seen by the MIPS EC during the performance period.
		 *
		 * Promoting Interoperability Transition Measure Elements:
		 * Numerator: Patient views, downloads, or transmits their information.
		 * Denominator: Number of patients seen by the EC.
		 */
		$sth = $this->conn->prepare("SELECT pid, eid FROM encounters WHERE provider_uid IN ('{$provider_id}') AND service_date BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE) AND service_date IS NOT NULL GROUP BY pid");
		$sth->execute();
		$ordered_prescriptions =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$denominator_pids = [];
		$denominator_eids = [];
		foreach($ordered_prescriptions as $ordered_prescription) {
			if(!in_array($ordered_prescription['pid'], $denominator_pids)) $denominator_pids[] = $ordered_prescription['pid'];
			$denominator_eids[] = $ordered_prescription['eid'];
		}
		$denominator = count($denominator_pids);
		$denominator_eids_str = join("','", $denominator_eids);

		$sth = $this->conn->prepare("SELECT pid FROM audit_log as a WHERE a.eid IN ('{$denominator_eids_str}') AND event IN ('CCDA_VDT') GROUP BY a.pid");
		$sth->execute();
		$numerator_records =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$numerator_pids = [];
		foreach($numerator_records as $numerator_record) {
			if(!in_array($numerator_record['pid'], $numerator_pids)) $numerator_pids[] = $numerator_record['pid'];
		}
		$numerator = count($numerator_records);

		$records[] = [
			'group' => '4. View, Download, Transmit',
			'title' => 'Promoting Interoperability Transition Measure',
			'description' => 'At least one patient seen by the MIPS EC during the performance period (or patient-authorized representative) views, downloads, or transmits their health information to a third party during the performance period.',
			'denominator' => $denominator,
			'numerator' => $numerator,
			'denominator_pids' => implode(',', $denominator_pids),
			'numerator_pids' => implode(',', $numerator_pids),
			'goal' => '1'
		];

		/**
		 * Promoting Interoperability Measure (2018 only):
		 * During the performance period, at least one unique patient (or patient-authorized representatives) seen by
		 * the MIPS EC actively engages with the EHR made accessible by the MIPS EC by either: (1) viewing, downloading,
		 * or transmitting to a third party their health information; or (2) accessing their health information through
		 * the use of an API that can be used by applications chosen by the patient and configured to the API in the
		 * MIPS eligible clinician's certified EHR technology; or (3) a combination of (1) and (2).
		 *
		 * Promoting Interoperability English Statements (2018 only):
		 * Numerator: The number of unique patients (or their authorized representatives) who have viewed online,
		 * downloaded, or transmitted to a third party the patient’s health information during the performance period
		 * and the number of unique patients (or their authorized representatives) who have accessed their health
		 * information through the use of an API during the performance period.
		 * Denominator: Number of unique patients seen by the MIPS EC during the performance period.
		 *
		 * Promoting Interoperability Transition Measure Elements:
		 * Numerator:
		 *  Patient views, transmits, or downloads their information;
		 *  Patient accesses their information via an API.
		 * Denominator: Number of patients seen by the EC.
		 */
		$sth = $this->conn->prepare("SELECT pid, eid FROM encounters WHERE provider_uid IN ('{$provider_id}') AND service_date BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE) AND service_date IS NOT NULL GROUP BY pid");
		$sth->execute();
		$ordered_prescriptions =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$denominator_pids = [];
		$denominator_eids = [];
		foreach($ordered_prescriptions as $ordered_prescription) {
			if(!in_array($ordered_prescription['pid'], $denominator_pids)) $denominator_pids[] = $ordered_prescription['pid'];
			$denominator_eids[] = $ordered_prescription['eid'];
		}
		$denominator = count($denominator_pids);
		$denominator_eids_str = join("','", $denominator_eids);

		$sth = $this->conn->prepare("SELECT pid FROM audit_log as a WHERE a.eid IN ('{$denominator_eids_str}') AND event IN ('CCDA_VDT') GROUP BY a.pid");
		$sth->execute();
		$numerator_records =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$numerator_pids = [];
		foreach($numerator_records as $numerator_record) {
			if(!in_array($numerator_record['pid'], $numerator_pids)) $numerator_pids[] = $numerator_record['pid'];
		}
		$numerator = count($numerator_records);

		$records[] = [
			'group' => '4. View, Download, Transmit',
			'title' => 'Promoting Interoperability Measure (2018 only)',
			'description' => 'During the performance period, at least one unique patient (or patient-authorized representatives) seen by the MIPS EC actively engages with the EHR made accessible by the MIPS EC by either: (1) viewing, downloading, or transmitting to a third party their health information; or (2) accessing their health information through the use of an API that can be used by applications chosen by the patient and configured to the API in the MIPS eligible clinician\'s certified EHR technology; or (3) a combination of (1) and (2).',
			'denominator' => $denominator,
			'numerator' => $numerator,
			'denominator_pids' => implode(',', $denominator_pids),
			'numerator_pids' => implode(',', $numerator_pids),
			'goal' => '1'
		];

		return $records;

	}

	// Required Test 5 – Secure Messaging
	public function getSecureMessagingReportByDates($provider_id, $start_date, $end_date){

		$records = [];

		/**
		 * Required Test 5 – Secure Messaging
		 * Modified Stage 2 Objective 9 and Stage 3 Objective 6 Measure 2
		 * Promoting Interoperability Transition Objective 5 Measure 1 and Promoting Interoperability Objective 4 Measure 2
		 */

		/**
		 * Modified Stage 2 Measure:
		 * Eligible Professional (EP): For an EHR reporting period in 2017, for more than 5 percent of unique patients
		 * seen by the EP during the EHR reporting period, a secure message was sent using the electronic messaging
		 * function of CEHRT to the patient (or the patient-authorized representative), or in response to a secure
		 * message sent by the patient (or the patient-authorized representative) during the EHR reporting period.
		 * For an EHR reporting period in 2016, the threshold for this measure is at least one message sent.
		 *
		 * Modified Stage 2 Measure English Statements:
		 * Numerator: The number of patients in the denominator for whom a secure electronic message is sent to the
		 * patient (or patient-authorized representative), or in response to a secure message sent by the patient
		 * (or patient-authorized representative).
		 * Denominator: Number of unique patients seen by the EP during the EHR reporting period.
		 *
		 * Modified Stage 2 Measure Elements:
		 * Numerator:
		 *  EP Replies to Secure Electronic Message from Patient or Patient Representative;
		 *  EP Sends Secure Electronic Message to Patient or Patient Representative.
		 * Denominator: Number of patients seen by the EP.
		 */

		$sth = $this->conn->prepare("SELECT pid, eid FROM encounters WHERE provider_uid IN ('{$provider_id}') AND service_date BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE) AND service_date IS NOT NULL GROUP BY pid");
		$sth->execute();
		$ordered_prescriptions =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$denominator_pids = [];
		$denominator_eids = [];
		foreach($ordered_prescriptions as $ordered_prescription) {
			if(!in_array($ordered_prescription['pid'], $denominator_pids)) $denominator_pids[] = $ordered_prescription['pid'];
			$denominator_eids[] = $ordered_prescription['eid'];
		}
		$denominator = count($denominator_pids);
		$denominator_eids_str = join("','", $denominator_eids);

		$sth = $this->conn->prepare("SELECT pid FROM audit_log as a WHERE a.eid IN ('{$denominator_eids_str}') AND event IN ('SECURE_MSG_TO_PAT','SECURE_MSG_FROM_PAT_TO_PRO') GROUP BY a.pid");
		$sth->execute();
		$numerator_records =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$numerator_pids = [];
		foreach($numerator_records as $numerator_record) {
			if(!in_array($numerator_record['pid'], $numerator_pids)) $numerator_pids[] = $numerator_record['pid'];
		}
		$numerator = count($numerator_records);

		$records[] = [
			'group' => '5. Secure Messaging',
			'title' => 'Modified Stage 2 Measure',
			'description' => 'Eligible Professional (EP): For an EHR reporting period in 2017, more than 5 percent of unique patients seen by the EP during the EHR reporting period (or his or her authorized representatives) view, download, or transmit to a third party their health information during the reporting period.',
			'denominator' => $denominator,
			'numerator' => $numerator,
			'denominator_pids' => implode(',', $denominator_pids),
			'numerator_pids' => implode(',', $numerator_pids),
			'goal' => '5%'
		];


		/**
		 * Stage 3 Measure:
		 * Eligible Professional (EP): For more than 25 percent of all unique patients seen by the EP during the EHR
		 * reporting period, a secure message was sent using the electronic messaging function of CEHRT to the patient
		 * (or the patient-authorized representative), or in response to a secure message sent by the patient or their
		 * authorized representative. For an EHR reporting period in 2016, the threshold for this measure is at least
		 * one message sent rather than 25 percent. For an EHR reporting period in 2017, the threshold for this measure
		 * is 5 percent rather than 25 percent.
		 *
		 * Stage 3 Measure English Statements:
		 * Numerator: The number of patients in the denominator for whom a secure electronic message is sent to the
		 * patient (or patient-authorized representative) or in response to a secure message sent by the patient (or
		 * patient-authorized representative), during the EHR reporting period.
		 * Denominator: Number of unique patients seen by the EP or discharged from the EH or CAH inpatient or emergency
		 * department during the reporting period.
		 *
		 * Stage 3 Measure Elements:
		 * Numerator:
		 *  EP/EH Replies to Secure Electronic Message from Patient or Patient Representative;
		 *  EP/EH Sends Secure Electronic Message to Patient or Patient Representative;
		 *  EP/EH Sends Secure Message to Provider Including Patient or Patient Representative.
		 * Denominator: Number of unique patients seen by the EP or discharged from the EH or CAH.
		 */

		$sth = $this->conn->prepare("SELECT pid, eid FROM encounters WHERE provider_uid IN ('{$provider_id}') AND service_date BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE) AND service_date IS NOT NULL GROUP BY pid");
		$sth->execute();
		$ordered_prescriptions =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$denominator_pids = [];
		$denominator_eids = [];
		foreach($ordered_prescriptions as $ordered_prescription) {
			if(!in_array($ordered_prescription['pid'], $denominator_pids)) $denominator_pids[] = $ordered_prescription['pid'];
			$denominator_eids[] = $ordered_prescription['eid'];
		}
		$denominator = count($denominator_pids);
		$denominator_eids_str = join("','", $denominator_eids);

		$sth = $this->conn->prepare("SELECT pid FROM audit_log as a WHERE a.eid IN ('{$denominator_eids_str}') AND event IN ('SECURE_MSG_TO_PAT','SECURE_MSG_FROM_PAT_TO_PRO') GROUP BY a.pid");
		$sth->execute();
		$numerator_records =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$numerator_pids = [];
		foreach($numerator_records as $numerator_record) {
			if(!in_array($numerator_record['pid'], $numerator_pids)) $numerator_pids[] = $numerator_record['pid'];
		}
		$numerator = count($numerator_records);

		$records[] = [
			'group' => '5. Secure Messaging',
			'title' => 'Stage 3 Measure',
			'description' => 'Eligible Professional (EP): For more than 25 percent of all unique patients seen by the EP during the EHR reporting period, a secure message was sent using the electronic messaging function of CEHRT to the patient (or the patient-authorized representative), or in response to a secure message sent by the patient or their authorized representative. For an EHR reporting period in 2016, the threshold for this measure is at least one message sent rather than 25 percent. For an EHR reporting period in 2017, the threshold for this measure is 5 percent rather than 25 percent.',
			'denominator' => $denominator,
			'numerator' => $numerator,
			'denominator_pids' => implode(',', $denominator_pids),
			'numerator_pids' => implode(',', $numerator_pids),
			'goal' => '25%'
		];

		return $records;

		/**
		 * Promoting Interoperability Transition Measure:
		 * For at least one patient seen by the MIPS EC during the performance period, a secure message was sent using
		 * the electronic messaging function of certified EHR technology to the patient (or the patient-authorized
		 * representative), or in response to a secure message sent by the patient (or patient-authorized
		 * representative) during the performance period.
		 *
		 * Promoting Interoperability Transition Measure English Statements:
		 * Numerator: The number of patients in the denominator for whom a secure electronic message is sent to the
		 * patient (or patient-authorized representative) or in response to a secure message sent by the patient
		 * (or patient-authorized representative), during the performance period
		 * Denominator: Number of unique patients seen by the MIPS EC during the performance period.
		 *
		 * Promoting Interoperability Transition Measure Elements:
		 * Numerator:
		 *  EC Replies to Secure Electronic Message from Patient or Patient Representative;
		 *  EC Sends Secure Electronic Message to Patient or Patient Representative.
		 * Denominator: Number of patients seen by the EC.
		 */

		$sth = $this->conn->prepare("SELECT pid, eid FROM encounters WHERE provider_uid IN ('{$provider_id}') AND service_date BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE) AND service_date IS NOT NULL GROUP BY pid");
		$sth->execute();
		$ordered_prescriptions =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$denominator_pids = [];
		$denominator_eids = [];
		foreach($ordered_prescriptions as $ordered_prescription) {
			if(!in_array($ordered_prescription['pid'], $denominator_pids)) $denominator_pids[] = $ordered_prescription['pid'];
			$denominator_eids[] = $ordered_prescription['eid'];
		}
		$denominator = count($denominator_pids);
		$denominator_eids_str = join("','", $denominator_eids);

		$sth = $this->conn->prepare("SELECT pid FROM audit_log as a WHERE a.eid IN ('{$denominator_eids_str}') AND event IN ('SECURE_MSG_TO_PAT','SECURE_MSG_FROM_PAT_TO_PRO') GROUP BY a.pid");
		$sth->execute();
		$numerator_records =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$numerator_pids = [];
		foreach($numerator_records as $numerator_record) {
			if(!in_array($numerator_record['pid'], $numerator_pids)) $numerator_pids[] = $numerator_record['pid'];
		}
		$numerator = count($numerator_records);

		$records[] = [
			'group' => '5. Secure Messaging',
			'title' => 'Promoting Interoperability Transition Measure',
			'description' => 'For at least one patient seen by the MIPS EC during the performance period, a secure message was sent using the electronic messaging function of certified EHR technology to the patient (or the patient-authorized representative), or in response to a secure message sent by the patient (or patient-authorized representative) during the performance period.',
			'denominator' => $denominator,
			'numerator' => $numerator,
			'denominator_pids' => implode(',', $denominator_pids),
			'numerator_pids' => implode(',', $numerator_pids),
			'goal' => '1'
		];

		/**
		 * Promoting Interoperability Measure (2018 only):
		 * For at least one unique patient seen by the MIPS EC during the performance period, a secure message was sent
		 * using the electronic messaging function of certified EHR technology to the patient (or the patient-authorized
		 * representative), or in response to a secure message sent by the patient (or patient-authorized
		 * representative) during the performance period.
		 *
		 * Promoting Interoperability Measure English Statements (2018 only):
		 * Numerator: The number of patients in the denominator for whom a secure electronic message is sent to the
		 * patient (or patient-authorized representative) or in response to a secure message sent by the patient
		 * (or patient-authorized representative), during the performance period.
		 * Denominator: Number of unique patients seen by the MIPS EC during the performance period.
		 *
		 * Promoting Interoperability Measure Elements (2018 only):
		 * Numerator:
		 *  EC replies to secure electronic message from patient or patient representative;
		 *  EC sends secure electronic message to patient or patient representative;
		 *  EC sends secure message to provider including
		 * Denominator: Number of patients seen by the EC.
		 */

		$sth = $this->conn->prepare("SELECT pid, eid FROM encounters WHERE provider_uid IN ('{$provider_id}') AND service_date BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE) AND service_date IS NOT NULL GROUP BY pid");
		$sth->execute();
		$ordered_prescriptions =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$denominator_pids = [];
		$denominator_eids = [];
		foreach($ordered_prescriptions as $ordered_prescription) {
			if(!in_array($ordered_prescription['pid'], $denominator_pids)) $denominator_pids[] = $ordered_prescription['pid'];
			$denominator_eids[] = $ordered_prescription['eid'];
		}
		$denominator = count($denominator_pids);
		$denominator_eids_str = join("','", $denominator_eids);

		$sth = $this->conn->prepare("SELECT pid FROM audit_log as a WHERE a.eid IN ('{$denominator_eids_str}') AND event IN ('SECURE_MSG_TO_PAT','SECURE_MSG_FROM_PAT_TO_PRO') GROUP BY a.pid");
		$sth->execute();
		$numerator_records =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$numerator_pids = [];
		foreach($numerator_records as $numerator_record) {
			if(!in_array($numerator_record['pid'], $numerator_pids)) $numerator_pids[] = $numerator_record['pid'];
		}
		$numerator = count($numerator_records);

		$records[] = [
			'group' => '5. Secure Messaging',
			'title' => 'Promoting Interoperability Measure Elements (2018 only)',
			'description' => 'For at least one unique patient seen by the MIPS EC during the performance period, a secure message was sent using the electronic messaging function of certified EHR technology to the patient (or the patient-authorized representative), or in response to a secure message sent by the patient (or patient-authorized representative) during the performance period.',
			'denominator' => $denominator,
			'numerator' => $numerator,
			'denominator_pids' => implode(',', $denominator_pids),
			'numerator_pids' => implode(',', $numerator_pids),
			'goal' => '1'
		];

		return $records;

	}

	// Required Test 6 – Patient Generated Health Data
	public function getPatientGeneratedHealthDataReportByDates($provider_id, $start_date, $end_date){

		$records = [];

		/**
		 * Required Test 6 – Patient Generated Health Data
		 * Stage 3 Objective 6 Measure 3
		 * Promoting Interoperability Objective 4 Measure 3
		 */

		/**
		 * Stage 3 Measure:
		 * Eligible Professional/Eligible Hospital/Critical Access Hospital (EP/EH/CAH): Patient generated health
		 * data or data from a nonclinical setting is incorporated into the CEHRT for more than 5 percent of all
		 * unique patients seen by the EP or discharged from the eligible hospital or CAH inpatient or emergency
		 * department (POS 21 or 23) during the EHR reporting period.
		 *
		 * Stage 3 Measure English Statements:
		 * Numerator: The number of patients in the denominator for whom data from non-clinical settings, which may
		 * include patient-generated health data, is captured through the CEHRT into the patient record during the EHR
		 * reporting period.
		 * Denominator: Number of unique patients seen by the EP or the number of unique patients discharged from an
		 * eligible hospital or CAH inpatient or emergency department (POS 21 or 23) during the EHR reporting period.
		 *
		 * Stage 3 Measure Elements:
		 * Numerator:
		 *  Patients with non-clinical data incorporated into the record;
		 *  Patients with patient-generated health data incorporated into the record.
		 * Denominator: Number of patients seen by the EP.
		 */

		$sth = $this->conn->prepare("SELECT e.pid FROM encounters as e 
										  INNER JOIN facility as f ON f.id = e.facility AND f.pos_code IN ('21', '23')
											   WHERE e.provider_uid IN ('{$provider_id}') AND  e.service_date BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE)
											GROUP BY e.pid");
		$sth->execute();
		$ordered_prescriptions =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$denominator_pids = [];
		foreach($ordered_prescriptions as $ordered_prescription) {
			if(!in_array($ordered_prescription['pid'], $denominator_pids)) $denominator_pids[] = $ordered_prescription['pid'];
		}
		$denominator = count($denominator_pids);
		$denominator_pids_str = join("','", $denominator_pids);

		$sth = $this->conn->prepare("SELECT pid FROM audit_log as a WHERE a.pid IN ('{$denominator_pids_str}') AND event IN ('GENERATED_HEALTH_DATA') GROUP BY a.pid");
		$sth->execute();
		$numerator_records =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$numerator_pids = [];
		foreach($numerator_records as $numerator_record) {
			if(!in_array($numerator_record['pid'], $numerator_pids)) $numerator_pids[] = $numerator_record['pid'];
		}
		$numerator = count($numerator_records);

		$records[] = [
			'group' => '6. Patient Generated Health Data',
			'title' => 'Stage 3 Measure',
			'description' => 'Eligible Professional/Eligible Hospital/Critical Access Hospital (EP/EH/CAH): Patient generated health data or data from a nonclinical setting is incorporated into the CEHRT for more than 5 percent of all unique patients seen by the EP or discharged from the eligible hospital or CAH inpatient or emergency department (POS 21 or 23) during the EHR reporting period.',
			'denominator' => $denominator,
			'numerator' => $numerator,
			'denominator_pids' => implode(',', $denominator_pids),
			'numerator_pids' => implode(',', $numerator_pids),
			'goal' => '5%'
		];

		return $records;

		/**
		 * Promoting Interoperability Measure (2018 only):
		 * Patient-generated health data or data from a non-clinical setting is incorporated into the certified EHR
		 * technology for at least one unique patient seen by the MIPS EC during the performance period.
		 *
		 * Promoting Interoperability Measure English Statements (2018 only):
		 * Numerator: The number of patients in the denominator for whom data from non-clinical settings, which may
		 * include patient-generated health data, is captured through the certified EHR technology into the patient
		 * record during the performance period.
		 * Denominator: Number of unique patients seen by the MIPS EC during the performance period.
		 *
		 * Promoting Interoperability Measure Elements (2018 only):
		 * Numerator:
		 *  Patients with non-clinical data incorporated into the record;
		 *  Patients with patient-generated health data incorporated into the record.
		 * Denominator: Number of patients seen by the EP.
		 */

		$sth = $this->conn->prepare("SELECT e.pid FROM  encounters as e 
										  INNER JOIN facility as f ON f.id = e.facility AND f.pos_code IN ('21', '23')
											   WHERE e.provider_uid IN ('{$provider_id}') AND  e.service_date BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE)
											GROUP BY e.pid");
		$sth->execute();
		$ordered_prescriptions =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$denominator_pids = [];
		foreach($ordered_prescriptions as $ordered_prescription) {
			if(!in_array($ordered_prescription['pid'], $denominator_pids)) $denominator_pids[] = $ordered_prescription['pid'];
		}
		$denominator = count($denominator_pids);
		$denominator_pids_str = join("','", $denominator_pids);

		$sth = $this->conn->prepare("SELECT pid FROM audit_log as a WHERE a.pid IN ('{$denominator_pids_str}') AND event IN ('GENERATED_HEALTH_DATA') GROUP BY a.pid");
		$sth->execute();
		$numerator_records =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$numerator_pids = [];
		foreach($numerator_records as $numerator_record) {
			if(!in_array($numerator_record['pid'], $numerator_pids)) $numerator_pids[] = $numerator_record['pid'];
		}
		$numerator = count($numerator_records);

		$records[] = [
			'group' => '6. Patient Generated Health Data',
			'title' => 'Promoting Interoperability Measure (2018 only)',
			'description' => 'Patient-generated health data or data from a non-clinical setting is incorporated into the certified EHR technology for at least one unique patient seen by the MIPS EC during the performance period.',
			'denominator' => $denominator,
			'numerator' => $numerator,
			'denominator_pids' => implode(',', $denominator_pids),
			'numerator_pids' => implode(',', $numerator_pids),
			'goal' => '5%'
		];

		return $records;
	}

	// Required Test 7 – Support Electronic Referral Loops by Sending Health Information (formerly Transitions of Care)
	public function getSupportElectronicReferralLoopsSendingReportByDates($provider_id, $start_date, $end_date){

		$records = [];

		/**
		 * Required Test 7 – Support Electronic Referral Loops by Sending Health Information (formerly Transitions of Care)
		 * Modified Stage 2 Objective 5 and Stage 3 Objective 7 Measure 1
		 * Promoting Interoperability Transition Objective 6 Measure 1 and Promoting Interoperability Objective 5 Measure 1
		 */

		/**
		 * Modified Stage 2 Measure:
		 * Eligible Professional/Eligible Hospital/Critical Access Hospital (EP/EH/CAH): The EP, eligible hospital,
		 * or CAH that transitions or refers their patient to another setting of care or provider of care must – (1)
		 * use CEHRT to create a summary of care record; and (2) electronically transmit such summary to a receiving
		 * provider for more than 10 percent of transitions of care and referrals.
		 *
		 * Modified Stage 2 Measure English Statements:
		 * Numerator: The number of transitions of care and referrals in the denominator where a summary of care record
		 * was created using CEHRT and exchanged electronically.
		 * Denominator: Number of transitions of care and referrals during the EHR reporting period for which the EP
		 * or eligible hospital's or CAH's inpatient or emergency department (POS 21 or 23) was the transferring or
		 * referring provider.
		 *
		 * Modified Stage 2 Measure Elements:
		 * Numerator:
		 *  Summary of care record created and exchanged;
		 *  Summary of care record receipt confirmed.
		 * Denominator: Number of transitions of care and referrals for which the EP, EH or CAH was the transferring
		 * of referring provider.
		 */

		$sth = $this->conn->prepare("SELECT e.pid FROM encounters as e
										  INNER JOIN facility as f ON f.id = e.facility AND f.pos_code IN ('21', '23')
										  INNER JOIN patient_referrals as r ON r.eid = e.eid
											   WHERE e.provider_uid IN ('{$provider_id}') AND  e.service_date BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE)
											");
		$sth->execute();
		$ordered_prescriptions =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$denominator_pids = [];
		foreach($ordered_prescriptions as $ordered_prescription) {
			if(!in_array($ordered_prescription['pid'], $denominator_pids)) $denominator_pids[] = $ordered_prescription['pid'];
		}
		$denominator = count($denominator_pids);
		$denominator_pids_str = join("','", $denominator_pids);

		$sth = $this->conn->prepare("SELECT pid FROM audit_log as a WHERE a.pid IN ('{$denominator_pids_str}') AND event IN ('OUTBOUND_TOC') GROUP BY a.pid");
		$sth->execute();
		$numerator_records =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$numerator_pids = [];
		foreach($numerator_records as $numerator_record) {
			if(!in_array($numerator_record['pid'], $numerator_pids)) $numerator_pids[] = $numerator_record['pid'];
		}
		$numerator = count($numerator_records);

		$records[] = [
			'group' => '7. Support Electronic Referral Loops by Sending Health Information',
			'title' => 'Modified Stage 2 Measure',
			'description' => 'Eligible Professional/Eligible Hospital/Critical Access Hospital (EP/EH/CAH): Patient generated health data or data from a nonclinical setting is incorporated into the CEHRT for more than 5 percent of all unique patients seen by the EP or discharged from the eligible hospital or CAH inpatient or emergency department (POS 21 or 23) during the EHR reporting period.',
			'denominator' => $denominator,
			'numerator' => $numerator,
			'denominator_pids' => implode(',', $denominator_pids),
			'numerator_pids' => implode(',', $numerator_pids),
			'goal' => '10%'
		];

		/**
		 * Stage 3 Measure:
		 * Eligible Professional (EP): For more than 50% of transitions of care and referrals, the EP that transitions
		 * or refers their patient to another setting of care or provider of care: (1) creates a summary of care record
		 * using CEHRT; and (2) electronically exchanges the summary of care record.
		 *
		 * Stage 3 Measure English Statements:
		 * Numerator: The number of transitions of care and referrals in the denominator where a summary of care record
		 * was created using certified EHR technology and exchanged electronically.
		 * Denominator: Number of transitions of care and referrals during the EHR reporting period for which the EP
		 * or eligible hospital or CAH inpatient or emergency department (POS 21 or 23) was the transferring or
		 * referring provider.
		 *
		 * Stage 3 Measure Elements:
		 * Numerator:
		 *  Summary of care record created and exchanged;
		 *  Summary of care record receipt confirmed.
		 * Denominator: Number of transitions of care and referrals for which the EP, EH or CAH was the transferring
		 * of referring provider.
		 */

		$sth = $this->conn->prepare("SELECT e.pid FROM encounters as e
										  INNER JOIN facility as f ON f.id = e.facility AND f.pos_code IN ('21', '23')
										  INNER JOIN patient_referrals as r ON r.eid = e.eid
											   WHERE e.provider_uid IN ('{$provider_id}') AND  e.service_date BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE)
											");
		$sth->execute();
		$ordered_prescriptions =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$denominator_pids = [];
		foreach($ordered_prescriptions as $ordered_prescription) {
			if(!in_array($ordered_prescription['pid'], $denominator_pids)) $denominator_pids[] = $ordered_prescription['pid'];
		}
		$denominator = count($denominator_pids);
		$denominator_pids_str = join("','", $denominator_pids);

		$sth = $this->conn->prepare("SELECT pid FROM audit_log as a WHERE a.pid IN ('{$denominator_pids_str}') AND event IN ('OUTBOUND_TOC') GROUP BY a.pid");
		$sth->execute();
		$numerator_records =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$numerator_pids = [];
		foreach($numerator_records as $numerator_record) {
			if(!in_array($numerator_record['pid'], $numerator_pids)) $numerator_pids[] = $numerator_record['pid'];
		}
		$numerator = count($numerator_records);

		$records[] = [
			'group' => '7. Support Electronic Referral Loops by Sending Health Information',
			'title' => 'Stage 3 Measure',
			'description' => 'Eligible Professional (EP): For more than 50% of transitions of care and referrals, the EP that transitions or refers their patient to another setting of care or provider of care: (1) creates a summary of care record using CEHRT; and (2) electronically exchanges the summary of care record.',
			'denominator' => $denominator,
			'numerator' => $numerator,
			'denominator_pids' => implode(',', $denominator_pids),
			'numerator_pids' => implode(',', $numerator_pids),
			'goal' => '50%'
		];

		return $records;

		/**
		 * Promoting Interoperability Transition Measure:
		 * Eligible Professional (EP): For more than 50% of transitions of care and referrals, the EP that transitions
		 * or refers their patient to another setting of care or provider of care: (1) creates a summary of care record
		 * using CEHRT; and (2) electronically exchanges the summary of care record.
		 *
		 * SPromoting Interoperability Transition Measure English Statements:
		 * Numerator: The number of transitions of care and referrals in the denominator where a summary of care
		 * record was created using certified EHR technology and exchanged electronically.
		 * Denominator: Number of transitions of care and referrals during the performance period for which the EP
		 * was the transferring or referring health care provider.
		 *
		 * Promoting Interoperability Transition Measure Elements:
		 * Numerator:
		 *  Summary of care record created and exchanged;
		 *  Summary of care record receipt confirmed.
		 * Denominator: Number of transitions of care and referrals for which the EP, EH or CAH was the transferring
		 * of referring provider.
		 */

		$sth = $this->conn->prepare("SELECT e.pid FROM encounters as e
										  INNER JOIN facility as f ON f.id = e.facility AND f.pos_code IN ('21', '23')
										  INNER JOIN patient_referrals as r ON r.eid = e.eid
											   WHERE e.provider_uid IN ('{$provider_id}') AND  e.service_date BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE)
											");
		$sth->execute();
		$ordered_prescriptions =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$denominator_pids = [];
		foreach($ordered_prescriptions as $ordered_prescription) {
			if(!in_array($ordered_prescription['pid'], $denominator_pids)) $denominator_pids[] = $ordered_prescription['pid'];
		}
		$denominator = count($denominator_pids);
		$denominator_pids_str = join("','", $denominator_pids);

		$sth = $this->conn->prepare("SELECT pid FROM audit_log as a WHERE a.pid IN ('{$denominator_pids_str}') AND event IN ('OUTBOUND_TOC') GROUP BY a.pid");
		$sth->execute();
		$numerator_records =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$numerator_pids = [];
		foreach($numerator_records as $numerator_record) {
			if(!in_array($numerator_record['pid'], $numerator_pids)) $numerator_pids[] = $numerator_record['pid'];
		}
		$numerator = count($numerator_records);

		$records[] = [
			'group' => '7. Support Electronic Referral Loops by Sending Health Information',
			'title' => 'Promoting Interoperability Transition Measure',
			'description' => 'The MIPS EC that transitions or refers their patient to another setting of care or health care provider: (1) uses certified EHR technology to create a summary of care record; and (2) electronically transmits such summary to a receiving health care provider for at least one transition of care or referral.',
			'denominator' => $denominator,
			'numerator' => $numerator,
			'denominator_pids' => implode(',', $denominator_pids),
			'numerator_pids' => implode(',', $numerator_pids),
			'goal' => '1'
		];

		/**
		 * Promoting Interoperability Measure:
		 * For at least one transition of care or referral, the MIPS EC that transitions or refers their patient to
		 * another setting of care or health care provider: (1) creates a summary of care record using certified EHR
		 * technology; and (2) electronically exchanges the summary of care record.
		 *
		 * Promoting Interoperability Measure English Statements:
		 * Numerator: The number of transitions of care and referrals in the denominator where a summary of care
		 * record was created using certified EHR technology and exchanged electronically.
		 * Denominator: Number of transitions of care and referrals during the performance period for which the MIPS
		 * EC was the transferring or referring clinician.
		 *
		 * Promoting Interoperability Measure Elements:
		 * Numerator:
		 *  Summary of care record created and exchanged;
		 *  Summary of care record receipt confirmed.
		 * Denominator: Number of transitions of care and referrals for which the EP, EH or CAH was the transferring
		 * of referring provider.
		 */

		$sth = $this->conn->prepare("SELECT e.pid FROM encounters as e
										  INNER JOIN facility as f ON f.id = e.facility AND f.pos_code IN ('21', '23')
										  INNER JOIN patient_referrals as r ON r.eid = e.eid
											   WHERE e.provider_uid IN ('{$provider_id}') AND  e.service_date BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE)
											");
		$sth->execute();
		$ordered_prescriptions =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$denominator_pids = [];
		foreach($ordered_prescriptions as $ordered_prescription) {
			if(!in_array($ordered_prescription['pid'], $denominator_pids)) $denominator_pids[] = $ordered_prescription['pid'];
		}
		$denominator = count($denominator_pids);
		$denominator_pids_str = join("','", $denominator_pids);

		$sth = $this->conn->prepare("SELECT pid FROM audit_log as a WHERE a.pid IN ('{$denominator_pids_str}') AND event IN ('OUTBOUND_TOC') GROUP BY a.pid");
		$sth->execute();
		$numerator_records =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$numerator_pids = [];
		foreach($numerator_records as $numerator_record) {
			if(!in_array($numerator_record['pid'], $numerator_pids)) $numerator_pids[] = $numerator_record['pid'];
		}
		$numerator = count($numerator_records);

		$records[] = [
			'group' => '7. Support Electronic Referral Loops by Sending Health Information',
			'title' => 'Promoting Interoperability Measure',
			'description' => 'For at least one transition of care or referral, the MIPS EC that transitions or refers their patient to another setting of care or health care provider: (1) creates a summary of care record using certified EHR technology; and (2) electronically exchanges the summary of care record.',
			'denominator' => $denominator,
			'numerator' => $numerator,
			'denominator_pids' => implode(',', $denominator_pids),
			'numerator_pids' => implode(',', $numerator_pids),
			'goal' => '1'
		];

		return $records;
	}

	// TODO
	// Required Test 8 Receive and Incorporate
	public function getReceiveAndIncorporateReportByDates($provider_id, $start_date, $end_date){

		$records = [];

		/**
		 * Required Test 8 Receive and Incorporate
		 * Stage 3 Objective 7 Measure 2
		 * Promoting Interoperability Objective 5 Measure 2
		 */

		/**
		 * Stage 3 Measure:
		 * Eligible Professional (EP): For more than 40 percent of transitions or referrals received and patient
		 * encounters in which the provider has never before encountered the patient, the EP incorporates into the
		 * patient's EHR an electronic summary of care document.
		 *
		 * Stage 3 Measure English Statements:
		 * Numerator: Number of patient encounters in the denominator where an electronic summary of care record
		 * received is incorporated by the provider into the certified EHR technology.
		 * Denominator: Number of patient encounters during the EHR reporting period for which an EP, eligible
		 * hospital, or CAH was the receiving party of a transition or referral or has never before encountered the
		 * patient and for which an electronic summary of care record is available.
		 *
		 * Stage 3 Measure Elements:
		 * Numerator:
		 *  Requested and available;
		 *  Received through query or request;
		 *  Incorporated into the record.
		 * Denominator:
		 *  Number of patient encounters where the EP, EH or CAH was the receiving party of a transition or referral;
		 *  Number of patient encounters where the EP, EH, or CAH has never before encountered the patient;
		 *  Electronic summary of care record is available.
		 */

		$sth = $this->conn->prepare("SELECT e.pid FROM encounters as e
										  INNER JOIN facility as f ON f.id = e.facility AND f.pos_code IN ('21', '23')
										  INNER JOIN patient_referrals as r ON r.eid = e.eid
											   WHERE e.provider_uid IN ('{$provider_id}') AND  e.service_date BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE)
											");
		$sth->execute();
		$ordered_prescriptions =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$denominator_pids = [];
		foreach($ordered_prescriptions as $ordered_prescription) {
			if(!in_array($ordered_prescription['pid'], $denominator_pids)) $denominator_pids[] = $ordered_prescription['pid'];
		}
		$denominator = count($denominator_pids);
		$denominator_pids_str = join("','", $denominator_pids);

		$sth = $this->conn->prepare("SELECT pid FROM audit_log as a WHERE a.pid IN ('{$denominator_pids_str}') AND event IN ('OUTBOUND_TOC') GROUP BY a.pid");
		$sth->execute();
		$numerator_records =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$numerator_pids = [];
		foreach($numerator_records as $numerator_record) {
			if(!in_array($numerator_record['pid'], $numerator_pids)) $numerator_pids[] = $numerator_record['pid'];
		}
		$numerator = count($numerator_records);

		$records[] = [
			'group' => '8. Receive and Incorporate',
			'title' => 'Stage 3 Measure',
			'description' => 'Eligible Professional (EP): For more than 40 percent of transitions or referrals received and patient encounters in which the provider has never before encountered the patient, the EP incorporates into the patient\'s EHR an electronic summary of care document.',
			'denominator' => $denominator,
			'numerator' => $numerator,
			'denominator_pids' => implode(',', $denominator_pids),
			'numerator_pids' => implode(',', $numerator_pids),
			'goal' => '40%'
		];

		return $records;

		/**
		 * Promoting Interoperability Measure (2018 only):
		 * For at least one transition of care or referral received or patient encounter in which the MIPS EC has
		 * never before encountered the patient, the MIPS EC receives or retrieves and incorporates into the patient's
		 * record and electronic summary of care document.
		 *
		 * Promoting Interoperability Measure English Statements (2018 only):
		 * Numerator: The number of transitions of care or referrals in the denominator where an electronic summary
		 * of care record received is incorporated by the provider into the certified EHR technology.
		 * Denominator: Number of transitions of care or referrals during the performance period for which the MIPS
		 * EC was the recipient of the transition or referral or had never before encountered the patient.
		 *
		 * Promoting Interoperability Measure Elements (2018 only):
		 * Numerator:
		 *  Requested and available;
		 *  Received through query or request;
		 *  Incorporated into the record.
		 * Denominator:
		 *  Number of patient encounters where the EP, EH or CAH was the receiving party of a transition or referral;
		 *  Number of patient encounters where the EP, EH, or CAH has never before encountered the patient;
		 *  Electronic summary of care record is available.
		 */

		$sth = $this->conn->prepare("SELECT e.pid FROM encounters as e
										  INNER JOIN facility as f ON f.id = e.facility AND f.pos_code IN ('21', '23')
										  INNER JOIN patient_referrals as r ON r.eid = e.eid
											   WHERE e.provider_uid IN ('{$provider_id}') AND  e.service_date BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE)
											");
		$sth->execute();
		$ordered_prescriptions =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$denominator_pids = [];
		foreach($ordered_prescriptions as $ordered_prescription) {
			if(!in_array($ordered_prescription['pid'], $denominator_pids)) $denominator_pids[] = $ordered_prescription['pid'];
		}
		$denominator = count($denominator_pids);
		$denominator_pids_str = join("','", $denominator_pids);

		$sth = $this->conn->prepare("SELECT pid FROM audit_log as a WHERE a.pid IN ('{$denominator_pids_str}') AND event IN ('OUTBOUND_TOC') GROUP BY a.pid");
		$sth->execute();
		$numerator_records =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$numerator_pids = [];
		foreach($numerator_records as $numerator_record) {
			if(!in_array($numerator_record['pid'], $numerator_pids)) $numerator_pids[] = $numerator_record['pid'];
		}
		$numerator = count($numerator_records);

		$records[] = [
			'group' => '8. Receive and Incorporate',
			'title' => 'Promoting Interoperability Measure Elements (2018 only)',
			'description' => 'For at least one transition of care or referral received or patient encounter in which the MIPS EC has never before encountered the patient, the MIPS EC receives or retrieves and incorporates into the patient\'s record and electronic summary of care document.',
			'denominator' => $denominator,
			'numerator' => $numerator,
			'denominator_pids' => implode(',', $denominator_pids),
			'numerator_pids' => implode(',', $numerator_pids),
			'goal' => '1'
		];


		return $records;
	}

	// Required Test 9 – Medication/Clinical Information Reconciliation
	public function getMedicationClinicalInformationReconciliationReportByDates($provider_id, $start_date, $end_date){

		$records = [];

		/**
		 * Required Test 9 – Medication/Clinical Information Reconciliation
		 * Modified Stage 2 Objective 7 and Stage 3 Objective 7 Measure 3
		 * Promoting Interoperability Transition Objective 7 Measure 1 and Promoting Interoperability Objective 5 Measure 3
		 */

		/**
		 * Modified Stage 2 Measure:
		 * Eligible Professional (EP): The EP performs medication reconciliation for more than 50 percent of
		 * transitions of care in which the patient is transitioned into the care of the EP during the reporting period.
		 *
		 * Modified Stage 2 Measure English Statements:
		 * Numerator: The number of transitions of care in the denominator where medication reconciliation was performed.
		 * Denominator: Number of transitions of care during the EHR reporting period for which the EP was the receiving
		 * party of the transition.
		 *
		 * Modified Stage 2 Measure Elements:
		 * Numerator: Indication that medication reconciliation occurred.
		 * Denominator:
		 *  Provision of summary of care record of any type for an existing patient;
		 *  Number of transitions of care for which the EP was the receiving party;
		 *  Number of patients the EP has not previously encountered.
		 */
		$sth = $this->conn->prepare("SELECT a.id, a.pid, a.eid FROM audit_log as a WHERE a.uid IN ('{$provider_id}') AND a.event = 'INBOUND_TOC' AND a.event_date BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE)");
		$sth->execute();
		$medications =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$medication_ids = [];
		$denominator_pids = [];
		foreach($medications as $medication) {
			if(!in_array($medication['pid'], $denominator_pids)) $denominator_pids[] = $medication['pid'];
			$medication_ids[] = $medication['id'];
		}
		$denominator = count($medication_ids);
		$medication_ids = join("','", $medication_ids);

		$sth = $this->conn->prepare("SELECT a.pid FROM audit_log as a WHERE a.foreign_table = 'patient_medications' AND a.event IN ('RECONCILE')");
		$sth->execute();
		$numerator_records =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$numerator_pids = [];
		foreach($numerator_records as $numerator_record) {
			if(!in_array($numerator_record['pid'], $numerator_pids)) $numerator_pids[] = $numerator_record['pid'];
		}
		$numerator = count($numerator_records);

		$records[] = [
			'group' => '9. Medication/Clinical Information Reconciliation',
			'title' => 'Modified Stage 2 Measure',
			'description' => 'Eligible Professional (EP): The EP performs medication reconciliation for more than 50 percent of transitions of care in which the patient is transitioned into the care of the EP during the reporting period.',
			'denominator' => $denominator,
			'numerator' => $numerator,
			'denominator_pids' => implode(',', $denominator_pids),
			'numerator_pids' => implode(',', $numerator_pids),
			'goal' => '50%'
		];

		/**
		 * Stage 3 Measure:
		 * Eligible Professional (EP): For more than 80 percent of transitions or referrals received and patient
		 * encounters in which the provider has never before encountered the patient, the EP performs a clinical
		 * information reconciliation. The provider must implement clinical information reconciliation for the following
		 * three clinical information sets: (a) Review of the patient's medication, including the name, dosage,
		 * frequency, and route of each medication; (b) Review of the patient's known medication allergies; and (c)
		 * Review of the patient's current and active diagnoses.
		 *
		 * Stage 3 Measure English Statements:
		 * Numerator: The number of transitions of care or referrals in the denominator where the following three
		 * clinical information reconciliations were performed: Medication list, medication allergy list, and current
		 * problem list.
		 * Denominator: Number of transitions of care or referrals during the EHR reporting period for which the EP was
		 * the recipient of the transition or referral or has never before encountered the patient.
		 *
		 * Stage 3 Measure Elements:
		 * Numerator: Indication that medication, medication allergy, and problem list reconciliation occurred.
		 * Denominator:
		 *  Provision of summary of care record of any type for an existing patient;
		 *  Number of transitions of care or referrals for which the EP was the recipient;
		 *  Number of patients the EP has not previously encountered.
		 */
		$sth = $this->conn->prepare("SELECT a.id, a.pid, a.eid FROM audit_log as a WHERE a.uid IN ('{$provider_id}') AND a.event = 'INBOUND_TOC' AND a.event_date BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE)");
		$sth->execute();
		$medications =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$medication_ids = [];
		$denominator_pids = [];
		foreach($medications as $medication) {
			if(!in_array($medication['pid'], $denominator_pids)) $denominator_pids[] = $medication['pid'];
			$medication_ids[] = $medication['id'];
		}
		$denominator = count($medication_ids);
		$medication_ids = join("','", $medication_ids);

		$sth = $this->conn->prepare("SELECT a.pid FROM audit_log as a WHERE a.foreign_table = 'patient_medications' AND a.event IN ('RECONCILE')");
		$sth->execute();
		$numerator_records =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$numerator_pids = [];
		foreach($numerator_records as $numerator_record) {
			if(!in_array($numerator_record['pid'], $numerator_pids)) $numerator_pids[] = $numerator_record['pid'];
		}
		$numerator = count($numerator_records);

		$records[] = [
			'group' => '9. Medication/Clinical Information Reconciliation',
			'title' => 'Stage 3 Measure',
			'description' => 'Eligible Professional (EP): For more than 80 percent of transitions or referrals received and patient encounters in which the provider has never before encountered the patient, the EP performs a clinical information reconciliation. The provider must implement clinical information reconciliation for the following three clinical information sets: (a) Review of the patient\'s medication, including the name, dosage, frequency, and route of each medication; (b) Review of the patient\'s known medication allergies; and (c) Review of the patient\'s current and active diagnoses.',
			'denominator' => $denominator,
			'numerator' => $numerator,
			'denominator_pids' => implode(',', $denominator_pids),
			'numerator_pids' => implode(',', $numerator_pids),
			'goal' => '80%'
		];

		return $records;

		/**
		 * Promoting Interoperability Transition Measure:
		 * The MIPS EC performs medication reconciliation for at least one transition of care in which the patient is
		 * transitioned into the care of the MIPS EC.
		 *
		 * Promoting Interoperability Transition Measure English Statements:
		 * Numerator: The number of transitions of care or referrals in the denominator where medication reconciliation
		 * was performed.
		 * Denominator: Number of transitions of care or referrals during the performance period for which the MIPS EC
		 * was the recipient of the transition or referral or has never been encountered by the patient.
		 *
		 * Promoting Interoperability Transition Measure Elements:
		 * Numerator: Indication that medication reconciliation occurred.
		 * Denominator:
		 *  Provision of summary of care record of any type for an existing patient;
		 *  Number of transitions of care for which the EC was the receiving party;
		 *  Number of patients the EC has not previously encountered.
		 */
		$sth = $this->conn->prepare("SELECT a.id, a.pid, a.eid FROM audit_log as a WHERE a.uid IN ('{$provider_id}') AND a.event = 'INBOUND_TOC' BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE)
											");
		$sth->execute();
		$medications =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$medication_ids = [];
		$denominator_pids = [];
		foreach($medications as $medication) {
			if(!in_array($medication['pid'], $denominator_pids)) $denominator_pids[] = $medication['pid'];
			$medication_ids[] = $medication['id'];
		}
		$denominator = count($medication_ids);
		$medication_ids = join("','", $medication_ids);

		$sth = $this->conn->prepare("SELECT a.pid FROM audit_log as a WHERE a.foreign_table = 'patient_medications' AND a.event IN ('RECONCILE')");
		$sth->execute();
		$numerator_records =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$numerator_pids = [];
		foreach($numerator_records as $numerator_record) {
			if(!in_array($numerator_record['pid'], $numerator_pids)) $numerator_pids[] = $numerator_record['pid'];
		}
		$numerator = count($numerator_records);

		$records[] = [
			'group' => '9. Medication/Clinical Information Reconciliation',
			'title' => 'Promoting Interoperability Transition Measure',
			'description' => 'The MIPS EC performs medication reconciliation for at least one transition of care in which the patient is transitioned into the care of the MIPS EC.',
			'denominator' => $denominator,
			'numerator' => $numerator,
			'denominator_pids' => implode(',', $denominator_pids),
			'numerator_pids' => implode(',', $numerator_pids),
			'goal' => '1'
		];

		/**
		 * Promoting Interoperability Measure (2018 only):
		 * For at least one transition of care or referral received or patient encounter in which the MIPS EC has never
		 * before encountered the patient, the MIPS EC performs clinical information reconciliation. The clinician must
		 * implement clinical information reconciliation for the following three clinical information sets: (1)
		 * Medication. Review of the patient's medication, including the name, dosage, frequency, and route of each
		 * medication. (2) Medication allergy. Review of the patient's known medication allergies. (3) Current Problem
		 * list. Review of the patient's current and active diagnoses.
		 *
		 * Promoting Interoperability Measure English Statements (2018 only):
		 * Numerator: The number of transitions of care or referrals in the denominator where the following three
		 * clinical information reconciliations were performed: medication list, medication allergy list, and current
		 * problem list.
		 * Denominator: Number of transitions of care or referrals during the performance period for which the MIPS EC
		 * was the recipient of the transition or referral or has never before encountered the patient.
		 *
		 * Promoting Interoperability Measure Elements (2018 only):
		 * Numerator: Indication that medication reconciliation occurred.
		 * Denominator:
		 *  Provision of summary of care record of any type for an existing patient;
		 *  Number of transitions of care for which the EC was the receiving party;
		 *  Number of patients the EC has not previously encountered.
		 */
		$sth = $this->conn->prepare("SELECT a.id, a.pid, a.eid FROM audit_log as a WHERE a.uid IN ('{$provider_id}') AND a.event = 'INBOUND_TOC' BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE)
											");
		$sth->execute();
		$medications =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$medication_ids = [];
		$denominator_pids = [];
		foreach($medications as $medication) {
			if(!in_array($medication['pid'], $denominator_pids)) $denominator_pids[] = $medication['pid'];
			$medication_ids[] = $medication['id'];
		}
		$denominator = count($medication_ids);
		$medication_ids = join("','", $medication_ids);

		$sth = $this->conn->prepare("SELECT a.pid FROM audit_log as a WHERE a.foreign_table = 'patient_medications' AND a.event IN ('RECONCILE')");
		$sth->execute();
		$numerator_records =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$numerator_pids = [];
		foreach($numerator_records as $numerator_record) {
			if(!in_array($numerator_record['pid'], $numerator_pids)) $numerator_pids[] = $numerator_record['pid'];
		}
		$numerator = count($numerator_records);

		$records[] = [
			'group' => '9. Medication/Clinical Information Reconciliation',
			'title' => 'Promoting Interoperability Measure (2018 only)',
			'description' => 'For at least one transition of care or referral received or patient encounter in which the MIPS EC has never before encountered the patient, the MIPS EC performs clinical information reconciliation. The clinician must implement clinical information reconciliation for the following three clinical information sets: (1) Medication. Review of the patient\'s medication, including the name, dosage, frequency, and route of each medication. (2) Medication allergy. Review of the patient\'s known medication allergies. (3) Current Problem list. Review of the patient\'s current and active diagnoses.',
			'denominator' => $denominator,
			'numerator' => $numerator,
			'denominator_pids' => implode(',', $denominator_pids),
			'numerator_pids' => implode(',', $numerator_pids),
			'goal' => '1'
		];

		return $records;
	}

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
		$sth = $this->conn->prepare("SELECT m.id, m.pid FROM patient_medications as m
										  INNER JOIN encounters as e ON e.eid = m.eid
										 --  INNER JOIN facility as f ON f.id = e.facility AND f.pos_code IN ('21', '23')
											   WHERE e.provider_uid IN ('{$provider_id}') AND m.created_date BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE)
											GROUP BY m.id");
		$sth->execute();
		$medications =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$medication_ids = [];
		$denominator_pids = [];
		foreach($medications as $medication) {
			if(!in_array($medication['pid'], $denominator_pids)) $denominator_pids[] = $medication['pid'];
			$medication_ids[] = $medication['id'];
		}
		$denominator = count($medication_ids);
		$medication_ids = join("','", $medication_ids);

		$sth = $this->conn->prepare("SELECT pid FROM patient_medications as m WHERE m.id IN ('{$medication_ids}') AND m.date_ordered IS NOT NULL");
		$sth->execute();
		$numerator_records =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$numerator_pids = [];
		foreach($numerator_records as $numerator_record) {
			if(!in_array($numerator_record['pid'], $numerator_pids)) $numerator_pids[] = $numerator_record['pid'];
		}
		$numerator = count($numerator_records);

		$records[] = [
			'group' => '10. CPOE Medications',
			'title' => 'Modified Stage 2 Measure',
			'description' => 'Eligible Professional/Eligible Hospital/Critical Access Hospital (EP/EH/CAH): More than 60 percent of medication orders created by the EP or by authorized providers of the eligible hospital\'s or CAH\'s inpatient or emergency department (POS 21 or 23) during the EHR reporting period are recorded using computerized provider order entry.',
			'denominator' => $denominator,
			'numerator' => $numerator,
			'denominator_pids' => implode(',', $denominator_pids),
			'numerator_pids' => implode(',', $numerator_pids),
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
		$sth = $this->conn->prepare("SELECT m.id, m.pid FROM patient_medications as m
										  INNER JOIN encounters as e ON e.eid = m.eid
										 --  INNER JOIN facility as f ON f.id = e.facility AND f.pos_code IN ('21', '23')
											   WHERE e.provider_uid IN ('{$provider_id}') AND m.created_date BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE)
											GROUP BY m.id");
		$sth->execute();
		$medications =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$medication_ids = [];
		$denominator_pids = [];
		foreach($medications as $medication) {
			if(!in_array($medication['pid'], $denominator_pids)) $denominator_pids[] = $medication['pid'];
			$medication_ids[] = $medication['id'];
		}
		$denominator = count($medication_ids);
		$medication_ids = join("','", $medication_ids);

		$sth = $this->conn->prepare("SELECT pid FROM patient_medications as m WHERE m.id IN ('{$medication_ids}') AND m.date_ordered IS NOT NULL");
		$sth->execute();
		$numerator_records =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$numerator_pids = [];
		foreach($numerator_records as $numerator_record) {
			if(!in_array($numerator_record['pid'], $numerator_pids)) $numerator_pids[] = $numerator_record['pid'];
		}
		$numerator = count($numerator_records);

		$records[] = [
			'group' => '10. CPOE Medications',
			'title' => 'Stage 3 Measure',
			'description' => 'Eligible Professional/Eligible Hospital/Critical Access Hospital (EP/EH/CAH): More than 60 percent of medication orders created by the EP or authorized providers of the eligible hospital or CAH inpatient or emergency department (POS 21 or 23) during the EHR reporting period are recorded using computerized provider order entry.',
			'denominator' => $denominator,
			'numerator' => $numerator,
			'denominator_pids' => implode(',', $denominator_pids),
			'numerator_pids' => implode(',', $numerator_pids),
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
		$sth = $this->conn->prepare("SELECT o.id, o.pid FROM patient_orders as o
										  INNER JOIN encounters as e ON e.eid = o.eid
										 -- INNER JOIN facility as f ON f.id = e.facility AND f.pos_code IN ('21', '23')
											   WHERE e.provider_uid IN ('{$provider_id}') AND o.order_type = 'lab' AND o.date_ordered BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE)
											GROUP BY o.id");
		$sth->execute();
		$orders =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$orders_ids = [];
		$denominator_pids = [];
		foreach($orders as $order) {
			if(!in_array($order['pid'], $denominator_pids)) $denominator_pids[] = $order['pid'];
			$orders_ids[] = $order['id'];
		}
		$denominator = count($orders_ids);
		$orders_ids = join("','", $orders_ids);

		$sth = $this->conn->prepare("SELECT pid FROM patient_orders as o WHERE o.id IN ('{$orders_ids}') AND (o.priority IS NOT NULL AND o.priority != '')");
		$sth->execute();
		$numerator_records =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$numerator_pids = [];
		foreach($numerator_records as $numerator_record) {
			if(!in_array($numerator_record['pid'], $numerator_pids)) $numerator_pids[] = $numerator_record['pid'];
		}
		$numerator = count($numerator_records);

		$records[] = [
			'group' => '11. CPOE Laboratory',
			'title' => 'Modified Stage 2 Measure',
			'description' => 'Eligible Professional/Eligible Hospital/Critical Access Hospital (EP/EH/CAH): More than 30 percent of laboratory orders created by the EP or by authorized providers of the eligible hospital\'s or CAH\'s inpatient or emergency department (POS 21 or 23) during the EHR reporting period are recorded using computerized provider order entry.',
			'denominator' => $denominator,
			'numerator' => $numerator,
			'denominator_pids' => implode(',', $denominator_pids),
			'numerator_pids' => implode(',', $numerator_pids),
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
		$sth = $this->conn->prepare("SELECT o.id, o.pid FROM patient_orders as o
										  INNER JOIN encounters as e ON e.eid = o.eid
										 -- INNER JOIN facility as f ON f.id = e.facility AND f.pos_code IN ('21', '23')
											   WHERE e.provider_uid IN ('{$provider_id}') AND o.order_type = 'lab' AND o.date_ordered BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE)
											GROUP BY o.id");
		$sth->execute();
		$orders =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$orders_ids = [];
		$denominator_pids = [];
		foreach($orders as $order) {
			if(!in_array($order['pid'], $denominator_pids)) $denominator_pids[] = $order['pid'];
			$orders_ids[] = $order['id'];
		}
		$denominator = count($orders_ids);
		$orders_ids = join("','", $orders_ids);

		$sth = $this->conn->prepare("SELECT pid FROM patient_orders as o WHERE o.id IN ('{$orders_ids}') AND (o.priority IS NOT NULL AND o.priority != '')");
		$sth->execute();
		$numerator_records =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$numerator_pids = [];
		foreach($numerator_records as $numerator_record) {
			if(!in_array($numerator_record['pid'], $numerator_pids)) $numerator_pids[] = $numerator_record['pid'];
		}
		$numerator = count($numerator_records);

		$records[] = [
			'group' => '11. CPOE Laboratory',
			'title' => 'Stage 3 Measure',
			'description' => 'Eligible Professional/Eligible Hospital/Critical Access Hospital (EP/EH/CAH): More than 60 percent of laboratory orders created by the EP or authorized providers of the eligible hospital or CAH inpatient or emergency department (POS 21 or 23) during the EHR reporting period are recorded using computerized provider order entry.',
			'denominator' => $denominator,
			'numerator' => $numerator,
			'denominator_pids' => implode(',', $denominator_pids),
			'numerator_pids' => implode(',', $numerator_pids),
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
		$sth = $this->conn->prepare("SELECT o.id, o.pid FROM patient_orders as o
										  INNER JOIN encounters as e ON e.eid = o.eid
										 -- INNER JOIN facility as f ON f.id = e.facility AND f.pos_code IN ('21', '23')
											   WHERE e.provider_uid IN ('{$provider_id}') AND o.order_type = 'rad' AND o.date_ordered BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE)
											GROUP BY o.id");
		$sth->execute();
		$orders =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$orders_ids = [];
		$denominator_pids = [];
		foreach($orders as $order) {
			if(!in_array($order['pid'], $denominator_pids)) $denominator_pids[] = $order['pid'];
			$orders_ids[] = $order['id'];
		}
		$denominator = count($orders_ids);
		$orders_ids = join("','", $orders_ids);

		$sth = $this->conn->prepare("SELECT pid FROM patient_orders as o WHERE o.id IN ('{$orders_ids}') AND (o.priority IS NOT NULL AND o.priority != '')");
		$sth->execute();
		$numerator_records =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$numerator_pids = [];
		foreach($numerator_records as $numerator_record) {
			if(!in_array($numerator_record['pid'], $numerator_pids)) $numerator_pids[] = $numerator_record['pid'];
		}
		$numerator = count($numerator_records);

		$records[] = [
			'group' => '12. CPOE Radiology/Diagnostic Imaging',
			'title' => 'Modified Stage 2 Measure',
			'description' => 'Eligible Professional/Eligible Hospital/Critical Access Hospital (EP/EH/CAH): More than 30 percent of radiology orders created by the EP or by authorized providers of the eligible hospital\'s or CAH\'s inpatient or emergency department (POS 21 or 23) during the EHR reporting period are recorded using computerized provider order entry',
			'denominator' => $denominator,
			'numerator' => $numerator,
			'denominator_pids' => implode(',', $denominator_pids),
			'numerator_pids' => implode(',', $numerator_pids),
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
		$sth = $this->conn->prepare("SELECT o.id, o.pid FROM patient_orders as o
										  INNER JOIN encounters as e ON e.eid = o.eid
										 -- INNER JOIN facility as f ON f.id = e.facility AND f.pos_code IN ('21', '23')
											   WHERE e.provider_uid IN ('{$provider_id}') AND o.order_type = 'rad' AND o.date_ordered BETWEEN CAST('{$start_date}' AS DATE) AND CAST('{$end_date}' AS DATE)
											GROUP BY o.id");
		$sth->execute();
		$orders =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$orders_ids = [];
		$denominator_pids = [];
		foreach($orders as $order) {
			if(!in_array($order['pid'], $denominator_pids)) $denominator_pids[] = $order['pid'];
			$orders_ids[] = $order['id'];
		}
		$denominator = count($orders_ids);
		$orders_ids = join("','", $orders_ids);

		$sth = $this->conn->prepare("SELECT pid FROM patient_orders as o WHERE o.id IN ('{$orders_ids}') AND (o.priority IS NOT NULL AND o.priority != '')");
		$sth->execute();
		$numerator_records =  $sth->fetchAll(PDO::FETCH_ASSOC);
		$numerator_pids = [];
		foreach($numerator_records as $numerator_record) {
			if(!in_array($numerator_record['pid'], $numerator_pids)) $numerator_pids[] = $numerator_record['pid'];
		}
		$numerator = count($numerator_records);

		$records[] = [
			'group' => '12. CPOE Radiology/Diagnostic Imaging',
			'title' => 'Stage 3 Measure',
			'description' => 'Eligible Professional/Eligible Hospital/Critical Access Hospital (EP/EH/CAH): More than 60 percent of diagnostic imaging orders created by the EP or authorized providers of the eligible hospital or CAH inpatient or emergency department (POS 21 or 23) during the EHR reporting period are recorded using computerized provider order entry.',
			'denominator' => $denominator,
			'numerator' => $numerator,
			'denominator_pids' => implode(',', $denominator_pids),
			'numerator_pids' => implode(',', $numerator_pids),
			'goal' => '60%'
		];

		return $records;
	}

	public function getPatientList($pids){
		$sth = $this->conn->prepare("SELECT pid,pubpid,fname,mname,lname,DOB,sex FROM patient WHERE pid IN ({$pids})");
		$sth->execute();
		return $sth->fetchAll(PDO::FETCH_ASSOC);

	}
}