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
	 * @param $insurance_id
	 * @return array
	 */
	public function getReportMeasureByDates($measure, $provider_id, $start_date, $end_date, $insurance_id, $stages = '3'){

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
			} else


            // MIPS
			if($measure == 'AdvanceCarePlan'){
				$results = $this->getAdvanceCarePlanReportByDates($provider_id, $insurance_id, $start_date, $end_date);
			}

            // MIPS
			if($measure == 'ControllingHighBloodPressure'){
				$results = $this->getControllingHighBloodPressureReportByDates($provider_id, $insurance_id, $start_date, $end_date);
			}

            // MIPS
			if($measure == 'CoronaryArteryDisease'){
				$results = $this->getCoronaryArteryDiseaseReportByDates($provider_id, $insurance_id, $start_date, $end_date);
			}

            // MIPS
			if($measure == 'CoronaryArteryDiseaseWIthMIorLVSD'){
				$results = $this->getCoronaryArteryDiseaseWIthMIorLVSDReportByDates($provider_id, $insurance_id, $start_date, $end_date);
			}

            // MIPS
			if($measure == 'CoronaryArteryDiseaseAntiplatelet'){
				$results = $this->getCoronaryArteryDiseaseAntiplateletReportByDates($provider_id, $insurance_id, $start_date, $end_date);
			}

            // MIPS
			if($measure == 'CoronaryArteryDiseaseBetaBlocker'){
				$results = $this->getCoronaryArteryDiseaseBetaBlockerReportByDates($provider_id, $insurance_id, $start_date, $end_date);
			}

            // MIPS
			if($measure == 'HeartFailureBetaBlocker'){
				$results = $this->getHeartFailureBetaBlockerReportByDates($provider_id, $insurance_id, $start_date, $end_date);
			}

            // MIPS
			if($measure == 'InfluenzaImmunization'){
				$results = $this->getInfluenzaImmunizationReportByDates($provider_id, $insurance_id, $start_date, $end_date);
			}

            // MIPS
			if($measure == 'PneumococcalImmunization'){
				$results = $this->getPneumococcalImmunizationReportByDates($provider_id, $insurance_id, $start_date, $end_date);
			}

            // MIPS
			if($measure == 'FallsRiskAssessment'){
				$results = $this->getFallsRiskAssessmentReportByDates($provider_id, $insurance_id, $start_date, $end_date);
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

		return [
			[
				"group" => "2. Provide Patients Electronic Access",
				"provider" => " RT2, ONE  (NPI:1861479502)",
				"title" => "Stage 3 Measure",
				"description" => "",
				"denominator" => 1,
				"numerator" => 1,
				"denominator_pids" => '1,2,3,4,5',
				"numerator_pids" => '1,2,3',
				"goal" => "80%"
			]
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

	//
	private function getAdvanceCarePlanReportByDates($provider_id, $insurance_id, $start_date, $end_date){

		$records = [];

		/**
		 */

		/**
		 */

		$sth = $this->conn->prepare("CALL `getAdvanceCarePlanReportByDates`(?, ?, ?, ?);");
		$sth->execute([$provider_id, $insurance_id, $start_date, $end_date]);
		$report =  $sth->fetch(PDO::FETCH_ASSOC);

		$records[] = [
			'group' => 'Quality ID #047 (NQF 0326): Advance Care Plan',
			'provider' => $report['provider'],
			'insurance' => $report['insurance'],
			'title' => $report['title'],
			'description' => 'Measure Description: Percentage of patients aged 65 years and older who have an advance care plan or surrogate decision maker documented in the medical record or documentation in the medical record that an advance care plan was discussed but the patient did not wish or was not able to name a surrogate decision maker or provide an advance care plan',
			'denominator' => $report['denominator'],
			'numerator' => $report['numerator'],
			'denominator_pids' => $report['denominator_pids'],
			'numerator_pids' => $report['numerator_pids'],
			'goal' => 'N/A'
		];

		return $records;
	}

	//
	private function getControllingHighBloodPressureReportByDates($provider_id, $insurance_id, $start_date, $end_date){

		$records = [];

		/**
		 */

		/**
		 */

		$sth = $this->conn->prepare("CALL `getControllingHighBloodPressureReportByDates`(?, ?, ?, ?);");
		$sth->execute([$provider_id, $insurance_id, $start_date, $end_date]);
		$report =  $sth->fetch(PDO::FETCH_ASSOC);

		$records[] = [
			'group' => 'Quality ID #0236: Controlling High Blood Pressure',
			'provider' => $report['provider'],
			'insurance' => $report['insurance'],
			'title' => $report['title'],
			'description' => 'Measure Description: Percentage of patients 18 - 85 years of age who had a diagnosis of hypertension overlapping the measurement period and whose most recent blood pressure was adequately controlled (< 140/90 mmHg) during the measurement period',
			'denominator' => $report['denominator'],
			'numerator' => $report['numerator'],
			'denominator_pids' => $report['denominator_pids'],
			'numerator_pids' => $report['numerator_pids'],
			'goal' => 'N/A'
		];

		return $records;
	}

	//
	private function getCoronaryArteryDiseaseReportByDates($provider_id, $insurance_id, $start_date, $end_date){

		$records = [];

		/**
		 */

		/**
		 */

		$sth = $this->conn->prepare("CALL `getCoronaryArteryDiseaseReportByDates`(?, ?, ?, ?);");
		$sth->execute([$provider_id, $insurance_id, $start_date, $end_date]);
		$report =  $sth->fetch(PDO::FETCH_ASSOC);

		$records[] = [
			'group' => 'Quality ID #0118 (NQF: 0066): Coronary Artery Disease (CAD): Angiotensin-Converting Enzyme (ACE) Inhibitor or Angiotensin Receptor Blocker (ARB) Thera',
			'provider' => $report['provider'],
			'insurance' => $report['insurance'],
			'title' => $report['title'],
			'description' => 'Measure Description: Percentage of patients aged 18 years and older with a diagnosis of coronary artery disease seen within a 12 month period who also have diabetes OR a current or prior Left Ventricular Ejection Fraction (LVEF) < 40% who were prescribed ACE inhibitor or ARB therapy',
			'denominator' => $report['denominator'],
			'numerator' => $report['numerator'],
			'denominator_pids' => $report['denominator_pids'],
			'numerator_pids' => $report['numerator_pids'],
			'goal' => 'N/A'
		];

		return $records;
	}

	//
	private function getCoronaryArteryDiseaseAntiplateletReportByDates($provider_id, $insurance_id, $start_date, $end_date){

		$records = [];

		/**
		 */

		/**
		 */

		$sth = $this->conn->prepare("CALL `getCoronaryArteryDiseaseAntiplateletReportByDates`(?, ?, ?, ?);");
		$sth->execute([$provider_id, $insurance_id, $start_date, $end_date]);
		$report =  $sth->fetch(PDO::FETCH_ASSOC);

		$records[] = [
			'group' => 'Quality ID #006 (NQF 0067): Coronary Artery Disease (CAD): Antiplatelet Therapy',
			'provider' => $report['provider'],
			'insurance' => $report['insurance'],
			'title' => $report['title'],
			'description' => 'Measure Description: Percentage of patients aged 18 years and older with a diagnosis of coronary artery disease (CAD) seen within a 12-month period who were prescribed aspirin or clopidogrel',
			'denominator' => $report['denominator'],
			'numerator' => $report['numerator'],
			'denominator_pids' => $report['denominator_pids'],
			'numerator_pids' => $report['numerator_pids'],
			'goal' => 'N/A'
		];

		return $records;
	}

	//
	private function getCoronaryArteryDiseaseBetaBlockerReportByDates($provider_id, $insurance_id, $start_date, $end_date){

		$records = [];

		/**
		 */

		/**
		 */

		$sth = $this->conn->prepare("CALL `getCoronaryArteryDiseaseBetaBlockerReportByDates`(?, ?, ?, ?);");
		$sth->execute([$provider_id, $insurance_id, $start_date, $end_date]);
		$report =  $sth->fetch(PDO::FETCH_ASSOC);

		$records[] = [
			'group' => 'Quality ID #007 (NQF 0070): Coronary Artery Disease (CAD): Beta-Blocker Therapy – Prior Myocardial Infarction (MI) or Left Ventricular Systolic Dysfunction',
			'provider' => $report['provider'],
			'insurance' => $report['insurance'],
			'title' => $report['title'],
			'description' => 'Measure Description: Percentage of patients aged 18 years and older with a diagnosis of coronary artery disease seen within a 12-month period who also have a prior MI or a current or prior LVEF < 40% who were prescribed beta-blocker therapy',
			'denominator' => $report['denominator'],
			'numerator' => $report['numerator'],
			'denominator_pids' => $report['denominator_pids'],
			'numerator_pids' => $report['numerator_pids'],
			'goal' => 'N/A'
		];

		return $records;
	}

	//
	private function getHeartFailureBetaBlockerReportByDates($provider_id, $insurance_id, $start_date, $end_date){

		$records = [];

		/**
		 */

		/**
		 */

		$sth = $this->conn->prepare("CALL `getHeartFailureBetaBlockerReportByDates`(?, ?, ?, ?);");
		$sth->execute([$provider_id, $insurance_id, $start_date, $end_date]);
		$report =  $sth->fetch(PDO::FETCH_ASSOC);

		$records[] = [
			'group' => 'Quality ID #008 (NQF 0083): Heart Failure (HF): Beta-Blocker Therapy for Left Ventricular Systolic Dysfunction (LVSD)',
			'provider' => $report['provider'],
			'insurance' => $report['insurance'],
			'title' => $report['title'],
			'description' => 'Measure Description: Percentage of patients aged 18 years and older with a diagnosis of heart failure (HF) with a current or prior left ventricular ejection fraction (LVEF) < 40% who were prescribed beta-blocker therapy either within a 12-month period when seen in the outpatient setting OR at each hospital discharge',
			'denominator' => $report['denominator'],
			'numerator' => $report['numerator'],
			'denominator_pids' => $report['denominator_pids'],
			'numerator_pids' => $report['numerator_pids'],
			'goal' => 'N/A'
		];

		return $records;
	}

	//
	private function getInfluenzaImmunizationReportByDates($provider_id, $insurance_id, $start_date, $end_date){

		$records = [];

		/**
		 */

		/**
		 */

		$sth = $this->conn->prepare("CALL `getInfluenzaImmunizationReportByDates`(?, ?, ?, ?);");
		$sth->execute([$provider_id, $insurance_id, $start_date, $end_date]);
		$report =  $sth->fetch(PDO::FETCH_ASSOC);

		$records[] = [
			'group' => 'Quality ID #0041 (NQF 0110): Preventive Care and Screening: Influenza Immunization',
			'provider' => $report['provider'],
			'insurance' => $report['insurance'],
			'title' => $report['title'],
			'description' => 'Measure Description: Percentage of patients aged 6 months and older seen for a visit between October 1 and March 31 who received an influenza immunization OR who reported previous receipt of an influenza immunization',
			'denominator' => $report['denominator'],
			'numerator' => $report['numerator'],
			'denominator_pids' => $report['denominator_pids'],
			'numerator_pids' => $report['numerator_pids'],
			'goal' => 'N/A'
		];

		return $records;
	}

	//
	private function getPneumococcalImmunizationReportByDates($provider_id, $insurance_id, $start_date, $end_date){

		$records = [];

		/**
		 */

		/**
		 */

		$sth = $this->conn->prepare("CALL `getPneumococcalImmunizationReportByDates`(?, ?, ?, ?);");
		$sth->execute([$provider_id, $insurance_id, $start_date, $end_date]);
		$report =  $sth->fetch(PDO::FETCH_ASSOC);

		$records[] = [
			'group' => 'Quality ID #0111: Pneumococcal Vaccination Status for Older Adults',
			'provider' => $report['provider'],
			'insurance' => $report['insurance'],
			'title' => $report['title'],
			'description' => 'Measure Description: Percentage of patients aged 6 months and older seen for a visit between October 1 and March 31 who received an influenza immunization OR who reported previous receipt of an influenza immunization',
			'denominator' => $report['denominator'],
			'numerator' => $report['numerator'],
			'denominator_pids' => $report['denominator_pids'],
			'numerator_pids' => $report['numerator_pids'],
			'goal' => 'N/A'
		];

		return $records;
	}

	//
	private function getFallsRiskAssessmentReportByDates($provider_id, $insurance_id, $start_date, $end_date){

		$records = [];

		/**
		 */

		/**
		 */

		$sth = $this->conn->prepare("CALL `getFallsRiskAssessmentReportByDates`(?, ?, ?, ?);");
		$sth->execute([$provider_id, $insurance_id, $start_date, $end_date]);
		$report =  $sth->fetch(PDO::FETCH_ASSOC);

		$records[] = [
			'group' => 'Quality ID #154 (NQF 0101): Falls: Risk Assessment',
			'provider' => $report['provider'],
			'insurance' => $report['insurance'],
			'title' => $report['title'],
			'description' => 'Measure Description: Percentage of patients 65 years of age and older who were screened for future fall risk during the measurement period',
			'denominator' => $report['denominator'],
			'numerator' => $report['numerator'],
			'denominator_pids' => $report['denominator_pids'],
			'numerator_pids' => $report['numerator_pids'],
			'goal' => 'N/A'
		];

		return $records;
	}

	public function getPatientList($pids){
		$sth = $this->conn->prepare("SELECT pid,pubpid,fname,mname,lname,DOB,sex FROM patient WHERE pid IN ({$pids})");
		$sth->execute();
		return $sth->fetchAll(PDO::FETCH_ASSOC);

	}
}