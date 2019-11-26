<?php

class MeasureCalculation {

	private $conn;

	function __construct()
	{
		$this->conn = Matcha::getConn();
	}

	public function getReportMeasureByDates($measure, $provider_id, $start_date, $end_date){
		try{
			if($measure == 'ePrescribing'){
				return $this->getPrescribingReportByDates($provider_id, $start_date, $end_date);
			}
			if($measure == 'ProvidePatientsElectronicAccess'){
				return $this->getProvidePatientsElectronicAccessReportByDates($provider_id, $start_date, $end_date);
			}
			return [];
		}catch (Exception $e){
			return [];
		}
	}

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

		$sth = $this->conn->prepare("SELECT count(*) as `count` FROM erx_prescriptions WHERE orderId IN ('{$ordered_prescriptions_ids}') ");
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

		$sth = $this->conn->prepare("SELECT count(*) as `count` FROM erx_prescriptions WHERE orderId IN ('{$ordered_prescriptions_ids}') ");
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

		$sth = $this->conn->prepare("SELECT count(*) as `count` FROM erx_prescriptions WHERE orderId IN ('{$ordered_prescriptions_ids}') AND ndc IS NOT NULL ");
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

		$sth = $this->conn->prepare("SELECT count(*) as `count` FROM erx_prescriptions WHERE orderId IN ('{$ordered_prescriptions_ids}') AND ndc IS NOT NULL ");
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
}