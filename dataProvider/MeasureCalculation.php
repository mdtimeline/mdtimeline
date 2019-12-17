<?php

class MeasureCalculation {

	private $conn;

	function __construct(){
		$this->conn = Matcha::getConn();
	}

	/**
	 * @param $measure
	 * @param $provider_id
	 * @param $start_date
	 * @param $end_date
	 * @param $stages
	 * @return array
	 */
	public function getReportMeasureByDates($measure, $provider_id, $start_date, $end_date, $stages = '3'){

		if(is_array($provider_id)){
			$provider_id = implode("','", $provider_id);
		}

		$results = [];

		try{
			if($measure == 'ePrescribing'){
				$results = $this->getPrescribingReportByDates($provider_id, $start_date, $end_date, $stages);
			} else
			if($measure == 'ProvidePatientsElectronicAccess'){
				$results = $this->getProvidePatientsElectronicAccessReportByDates($provider_id, $start_date, $end_date, $stages);
			} else
			if($measure == 'PatientEducation'){
				$results = $this->getPatientEducationReportByDates($provider_id, $start_date, $end_date, $stages);
			} else
			if($measure == 'ViewDownloadTransmit'){
				$results = $this->getViewDownloadTransmitReportByDates($provider_id, $start_date, $end_date, $stages);
			} else
			if($measure == 'SecureMessaging'){
				$results = $this->getSecureMessagingReportByDates($provider_id, $start_date, $end_date, $stages);
			} else
			if($measure == 'PatientGeneratedHealthData'){
				$results = $this->getPatientGeneratedHealthDataReportByDates($provider_id, $start_date, $end_date, $stages);
			} else
			if($measure == 'SupportElectronicReferralLoopsSending'){
				return $this->getSupportElectronicReferralLoopsSendingReportByDates($provider_id, $start_date, $end_date, $stages);
			} else
			if($measure == 'ReceiveAndIncorporate'){
				$results = $this->getReceiveAndIncorporateReportByDates($provider_id, $start_date, $end_date, $stages);
			} else
			if($measure == 'MedicationClinicalInformationReconciliation'){
				$results = $this->getMedicationClinicalInformationReconciliationReportByDates($provider_id, $start_date, $end_date, $stages);
			} else
			if($measure == 'CPOEMedications'){
				$results = $this->getCPOEMedicationsReportByDates($provider_id, $start_date, $end_date, $stages);
			} else
			if($measure == 'CPOELaboratory'){
				$results = $this->getCPOELaboratoryReportByDates($provider_id, $start_date, $end_date, $stages);
			} else
			if($measure == 'CPOERadiology'){
				$results = $this->getCPOERadiologyReportByDates($provider_id, $start_date, $end_date, $stages);
			}

			return $results;
		}catch (Exception $e){
			return $results;
		}
	}

	// ....
	// Required Test 1 - ePrescribing
	public function getPrescribingReportByDates($provider_id, $start_date, $end_date, $stages){

		$records = [];

		/**
		 * Required Test 1 - ePrescribing
		 * Modified Stage 2 Objective 4 and Stage 3 Objective 2
		 * Promoting Interoperability Transition Objective 2 Measure 1 and Promoting Interoperability Objective 2 Measure 1
		 */


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
		$sth = $this->conn->prepare("CALL `getPrescribingReportByDates`(?, ?, ?, ?);");
		$sth->execute([$provider_id, $start_date, $end_date, $stages]);
		$report =  $sth->fetch(PDO::FETCH_ASSOC);

		$records[] = [
			'group' => '1. ePrescribing',
			'provider' => $report['provider'],
			'title' => $report['title'],
			'description' => 'Eligible Professional (EP): More than 60 percent of all permissible prescriptions written by the EP are queried for a drug formulary and transmitted electronically using CEHRT.',
			'denominator' => $report['denominator'],
			'numerator' => $report['numerator'],
			'denominator_pids' => $report['denominator_pids'],
			'numerator_pids' => $report['numerator_pids'],
			'goal' => '60%'
		];

		return $records;


	}

	// .... b
	// Required Test 2a, b, or c – Provide Patients Electronic Access to Their Health Information (formerly Patient Electronic Access)
	public function getProvidePatientsElectronicAccessReportByDates($provider_id, $start_date, $end_date, $stages){

		$records = [];

		/**
		 * Required Test 2a, b, or c – Provide Patients Electronic Access to Their Health Information (formerly Patient Electronic Access)
		 * Modified Stage 2 Objective 8 Measure 1 and Stage 3 Objective 5 Measure 1
		 * Promoting Interoperability Transition Objective 3 Measure 1 and Promoting Interoperability Objective 3 Measure 1
		 */

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

		$sth = $this->conn->prepare("CALL `getProvidePatientsElectronicAccessReportByDates`(?, ?, ?, ?);");
		$sth->execute([$provider_id, $start_date, $end_date, $stages]);
		$report =  $sth->fetch(PDO::FETCH_ASSOC);

		$records[] = [
			'group' => '2. Provide Patients Electronic Access',
			'provider' => $report['provider'],
			'title' => $report['title'],
			'description' => 'Eligible Professional (EP): For more than 80 percent of all unique patients seen by the EP: (1) The patient (or the patient-authorized representative) is provided timely access to view online, download, and transmit his or her health information; and (2) The provider ensures the patient’s health information is available for the patient (or patient-authorized representative) to access using any application of their choice that is configured to meet the technical specifications of the API in the provider’s CEHRT.',
			'denominator' => $report['denominator'],
			'numerator' => $report['numerator'],
			'denominator_pids' => $report['denominator_pids'],
			'numerator_pids' => $report['numerator_pids'],
			'goal' => '80%'
		];

		return $records;
	}

	// Required Test 3 – Patient Education
	public function getPatientEducationReportByDates($provider_id, $start_date, $end_date, $stages){

		$records = [];

		/**
		 * Required Test 3 – Patient Education
		 * Modified Stage 2 Objective 6 and Stage 3 Objective 5 Measure 2
		 * Promoting Interoperability Transition Objective 4 Measure 1 and Promoting Interoperability Objective 3 Measure 2
		 */


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

		$sth = $this->conn->prepare("CALL `getPatientEducationReportByDates`(?, ?, ?, ?);");
		$sth->execute([$provider_id, $start_date, $end_date, $stages]);
		$report =  $sth->fetch(PDO::FETCH_ASSOC);

		$records[] = [
			'group' => '3. Patient Education',
			'provider' => $report['provider'],
			'title' => $report['title'],
			'description' => 'Eligible Professional: The EP must use clinically relevant information from CEHRT to identify patient-specific educational resources and provide electronic access to those materials to more than 35 percent of unique patients seen by the EP during the EHR reporting period.',
			'denominator' => $report['denominator'],
			'numerator' => $report['numerator'],
			'denominator_pids' => $report['denominator_pids'],
			'numerator_pids' => $report['numerator_pids'],
			'goal' => '35%'
		];

		return $records;

	}

	// Required Test 4a, b, or c – View, Download, Transmit
	public function getViewDownloadTransmitReportByDates($provider_id, $start_date, $end_date, $stages){

		$records = [];

		/**
		 * Required Test 4a, b, or c – View, Download, Transmit
		 * Modified Stage 2 Objective 8 Measure 2 and Stage 3 Objective 6 Measure 1
		 * Promoting Interoperability Transition Objective 3 Measure 2 and Promoting Interoperability Objective 4 Measure 1
		 */


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

		$sth = $this->conn->prepare("CALL `getViewDownloadTransmitReportByDates`(?, ?, ?, ?);");
		$sth->execute([$provider_id, $start_date, $end_date, $stages]);
		$report =  $sth->fetch(PDO::FETCH_ASSOC);

		$records[] = [
			'group' => '4. View, Download, Transmit',
			'provider' => $report['provider'],
			'title' => $report['title'],
			'description' => 'Eligible Professional (EP): During the EHR reporting period, more than 10 percent of all unique patients (or their authorized representatives) seen by the EP actively engage with the electronic health record made accessible by the provider and either: (1) view, download, or transmit to a third party their health information; or (2) access their health information through the use of an API that can be used by applications chosen by the patient and configured to the API in the provider\'s CEHRT; or (3) a combination of (1) and (2).',
			'denominator' => $report['denominator'],
			'numerator' => $report['numerator'],
			'denominator_pids' => $report['denominator_pids'],
			'numerator_pids' => $report['numerator_pids'],
			'numerator_types' => $report['numerator_types'],
			'goal' => '10%'
		];

		return $records;
	}

	// Required Test 5 – Secure Messaging
	public function getSecureMessagingReportByDates($provider_id, $start_date, $end_date, $stages){

		$records = [];

		/**
		 * Required Test 5 – Secure Messaging
		 * Modified Stage 2 Objective 9 and Stage 3 Objective 6 Measure 2
		 * Promoting Interoperability Transition Objective 5 Measure 1 and Promoting Interoperability Objective 4 Measure 2
		 */

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

		$sth = $this->conn->prepare("CALL `getSecureMessagingReportByDates`(?, ?, ?, ?);");
		$sth->execute([$provider_id, $start_date, $end_date, $stages]);
		$report =  $sth->fetch(PDO::FETCH_ASSOC);

		$records[] = [
			'group' => '5. Secure Messaging',
			'provider' => $report['provider'],
			'title' => $report['title'],
			'description' => 'Eligible Professional (EP): For more than 25 percent of all unique patients seen by the EP during the EHR reporting period, a secure message was sent using the electronic messaging function of CEHRT to the patient (or the patient-authorized representative), or in response to a secure message sent by the patient or their authorized representative. For an EHR reporting period in 2016, the threshold for this measure is at least one message sent rather than 25 percent. For an EHR reporting period in 2017, the threshold for this measure is 5 percent rather than 25 percent.',
			'denominator' => $report['denominator'],
			'numerator' => $report['numerator'],
			'denominator_pids' => $report['denominator_pids'],
			'numerator_pids' => $report['numerator_pids'],
			'goal' => '25%'
		];

		return $records;

	}

	// Required Test 6 – Patient Generated Health Data
	public function getPatientGeneratedHealthDataReportByDates($provider_id, $start_date, $end_date, $stages){

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

		$sth = $this->conn->prepare("CALL `getPatientGeneratedHealthDataReportByDates`(?, ?, ?, ?);");
		$sth->execute([$provider_id, $start_date, $end_date, $stages]);
		$report =  $sth->fetch(PDO::FETCH_ASSOC);

		$records[] = [
			'group' => '6. Patient Generated Health Data',
			'provider' => $report['provider'],
			'title' => $report['title'],
			'description' => 'Eligible Professional/Eligible Hospital/Critical Access Hospital (EP/EH/CAH): Patient generated health data or data from a nonclinical setting is incorporated into the CEHRT for more than 5 percent of all unique patients seen by the EP or discharged from the eligible hospital or CAH inpatient or emergency department (POS 21 or 23) during the EHR reporting period.',
			'denominator' => $report['denominator'],
			'numerator' => $report['numerator'],
			'denominator_pids' => $report['denominator_pids'],
			'numerator_pids' => $report['numerator_pids'],
			'goal' => '5%'
		];

		return $records;

	}

	// Required Test 7 – Support Electronic Referral Loops by Sending Health Information (formerly Transitions of Care)
	public function getSupportElectronicReferralLoopsSendingReportByDates($provider_id, $start_date, $end_date, $stages){

		$records = [];

		/**
		 * Required Test 7 – Support Electronic Referral Loops by Sending Health Information (formerly Transitions of Care)
		 * Modified Stage 2 Objective 5 and Stage 3 Objective 7 Measure 1
		 * Promoting Interoperability Transition Objective 6 Measure 1 and Promoting Interoperability Objective 5 Measure 1
		 */

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

		$sth = $this->conn->prepare("CALL `getSupportElectronicReferralLoopsSendingReportByDates`(?, ?, ?, ?);");
		$sth->execute([$provider_id, $start_date, $end_date, $stages]);
		$report =  $sth->fetch(PDO::FETCH_ASSOC);

		$records[] = [
			'group' => '7. Support Electronic Referral Loops by Sending Health Information',
			'provider' => $report['provider'],
			'title' => $report['title'],
			'description' => 'Eligible Professional (EP): For more than 50% of transitions of care and referrals, the EP that transitions or refers their patient to another setting of care or provider of care: (1) creates a summary of care record using CEHRT; and (2) electronically exchanges the summary of care record.',
			'denominator' => $report['denominator'],
			'numerator' => $report['numerator'],
			'denominator_pids' => $report['denominator_pids'],
			'numerator_pids' => $report['numerator_pids'],
			'goal' => '50%'
		];

		return $records;

	}

	// Required Test 8 Receive and Incorporate
	public function getReceiveAndIncorporateReportByDates($provider_id, $start_date, $end_date, $stages){

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

		$sth = $this->conn->prepare("CALL `getReceiveAndIncorporateReportByDates`(?, ?, ?, ?);");
		$sth->execute([$provider_id, $start_date, $end_date, $stages]);
		$report =  $sth->fetch(PDO::FETCH_ASSOC);

		$records[] = [
			'group' => '8. Receive and Incorporate',
			'provider' => $report['provider'],
			'title' => $report['title'],
			'description' => 'Eligible Professional (EP): For more than 40 percent of transitions or referrals received and patient encounters in which the provider has never before encountered the patient, the EP incorporates into the patient\'s EHR an electronic summary of care document.',
			'denominator' => $report['denominator'],
			'numerator' => $report['numerator'],
			'denominator_pids' => $report['denominator_pids'],
			'numerator_pids' => $report['numerator_pids'],
			'goal' => '40%'
		];

		return $records;

	}

	// Required Test 9 – Medication/Clinical Information Reconciliation
	public function getMedicationClinicalInformationReconciliationReportByDates($provider_id, $start_date, $end_date, $stages){

		$records = [];

		/**
		 * Required Test 9 – Medication/Clinical Information Reconciliation
		 * Modified Stage 2 Objective 7 and Stage 3 Objective 7 Measure 3
		 * Promoting Interoperability Transition Objective 7 Measure 1 and Promoting Interoperability Objective 5 Measure 3
		 */

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
		$sth = $this->conn->prepare("CALL `getMedicationClinicalInformationReconciliationReportByDates`(?, ?, ?, ?);");
		$sth->execute([$provider_id, $start_date, $end_date, $stages]);
		$report =  $sth->fetch(PDO::FETCH_ASSOC);

		$records[] = [
			'group' => '9. Medication/Clinical Information Reconciliation',
			'provider' => $report['provider'],
			'title' => $report['title'],
			'description' => 'Eligible Professional (EP): For more than 80 percent of transitions or referrals received and patient encounters in which the provider has never before encountered the patient, the EP performs a clinical information reconciliation. The provider must implement clinical information reconciliation for the following three clinical information sets: (a) Review of the patient\'s medication, including the name, dosage, frequency, and route of each medication; (b) Review of the patient\'s known medication allergies; and (c) Review of the patient\'s current and active diagnoses.',
			'denominator' => $report['denominator'],
			'numerator' => $report['numerator'],
			'denominator_pids' => $report['denominator_pids'],
			'numerator_pids' => $report['numerator_pids'],
			'goal' => '80%'
		];

		return $records;

	}

	// Required Test 10 – CPOE Medications
	private function getCPOEMedicationsReportByDates($provider_id, $start_date, $end_date, $stages){

		$records = [];

		/**
		 * Required Test 10 – CPOE Medications
		 * Modified Stage 2 Objective 3 Measure 1 and Stage 3 Objective 4 Measure 1
		 */

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

		$sth = $this->conn->prepare("CALL `getCPOEMedicationsReportByDates`(?, ?, ?, ?);");
		$sth->execute([$provider_id, $start_date, $end_date, $stages]);
		$report =  $sth->fetch(PDO::FETCH_ASSOC);

		$records[] = [
			'group' => '10. CPOE Medications',
			'provider' => $report['provider'],
			'title' => $report['title'],
			'description' => 'Eligible Professional/Eligible Hospital/Critical Access Hospital (EP/EH/CAH): More than 60 percent of medication orders created by the EP or authorized providers of the eligible hospital or CAH inpatient or emergency department (POS 21 or 23) during the EHR reporting period are recorded using computerized provider order entry.',
			'denominator' => $report['denominator'],
			'numerator' => $report['numerator'],
			'denominator_pids' => $report['denominator_pids'],
			'numerator_pids' => $report['numerator_pids'],
			'goal' => '60%'
		];

		return $records;
	}

	// Required Test 11 – CPOE Laboratory
	private function getCPOELaboratoryReportByDates($provider_id, $start_date, $end_date, $stages){

		$records = [];

		/**
		 * Required Test 11 – CPOE Laboratory
		 * Modified Stage 2 Objective 3 Measure 2 and Stage 3 Objective 4 Measure 2
		 */

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

		$sth = $this->conn->prepare("CALL `getCPOELaboratoryReportByDates`(?, ?, ?, ?);");
		$sth->execute([$provider_id, $start_date, $end_date, $stages]);
		$report =  $sth->fetch(PDO::FETCH_ASSOC);

		$records[] = [
			'group' => '11. CPOE Laboratory',
			'provider' => $report['provider'],
			'title' => $report['title'],
			'description' => 'Eligible Professional/Eligible Hospital/Critical Access Hospital (EP/EH/CAH): More than 60 percent of laboratory orders created by the EP or authorized providers of the eligible hospital or CAH inpatient or emergency department (POS 21 or 23) during the EHR reporting period are recorded using computerized provider order entry.',
			'denominator' => $report['denominator'],
			'numerator' => $report['numerator'],
			'denominator_pids' => $report['denominator_pids'],
			'numerator_pids' => $report['numerator_pids'],
			'goal' => '60%'
		];

		return $records;
	}

	// Required Test 12 – CPOE Radiology/Diagnostic Imaging
	private function getCPOERadiologyReportByDates($provider_id, $start_date, $end_date, $stages){

		$records = [];

		/**
		 * Required Test 12 – CPOE Radiology/Diagnostic Imaging
		 * Modified Stage 2 Objective 3 Measure 3 and Stage 3 Objective 4 Measure 3
		 */

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

		$sth = $this->conn->prepare("CALL `getCPOERadiologyReportByDates`(?, ?, ?, ?);");
		$sth->execute([$provider_id, $start_date, $end_date, $stages]);
		$report =  $sth->fetch(PDO::FETCH_ASSOC);

		$records[] = [
			'group' => '12. CPOE Radiology/Diagnostic Imaging',
			'provider' => $report['provider'],
			'title' => $report['title'],
			'description' => 'Eligible Professional/Eligible Hospital/Critical Access Hospital (EP/EH/CAH): More than 60 percent of diagnostic imaging orders created by the EP or authorized providers of the eligible hospital or CAH inpatient or emergency department (POS 21 or 23) during the EHR reporting period are recorded using computerized provider order entry.',
			'denominator' => $report['denominator'],
			'numerator' => $report['numerator'],
			'denominator_pids' => $report['denominator_pids'],
			'numerator_pids' => $report['numerator_pids'],
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