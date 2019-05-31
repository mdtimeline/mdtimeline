<?php
/**
 * mdTimeLine EHR (Electronic Health Records)
 * Copyright (C) 2017 mdTimeLine, Inc.
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

include_once(ROOT . '/dataProvider/User.php');
include_once(ROOT . '/dataProvider/Patient.php');
include_once(ROOT . '/dataProvider/Facilities.php');
include_once(ROOT . '/dataProvider/ReferringProviders.php');
include_once(ROOT . '/dataProvider/CombosData.php');

class PatientRecord {
	/**
	 * @var PDO
	 */
	private $db;

	/**
	 * @var array
	 */
	private $buff = [];

	/**
	 * @var array
	 */
	private $patient_record = [];

	/**
	 * @var int
	 */
	private $pid;

	/**
	 * @var int
	 */
	private $eid;

	/**
	 * @var int
	 */
	private $referral_id;

	/**
	 * @var string
	 */
	private $start_date;

	/**
	 * @var string
	 */
	private $end_date;

	/**
	 * @var array
	 */
	private $excludes = [];

	/**
	 * @var Patient
	 */
	private $Patient;

	/**
	 * @var User
	 */
	private $User;

	/**
	 * @var Facilities
	 */
	private $Facilities;

	/**
	 * @var ReferringProviders
	 */
	private $ReferringProviders;

	/**
	 * @var CombosData
	 */
	private $CombosData;

	/**
	 * @var string
	 */
	private $returnType;

	/**
	 * @var string
	 */
	private $measuringUnits = 'metric';

	/**
	 * PatientRecord constructor.
	 *
	 * @param string $returnType
	 */
	function __construct($returnType = 'array'){
		$this->returnType = $returnType;
		$this->db = Matcha::getConn();
		$this->User = new User();
		$this->User = new User();
		$this->Facilities = new Facilities();
		$this->ReferringProviders = new ReferringProviders();
		$this->CombosData = new CombosData();
	}

	/**
	 * @param int         $fid Facility ID
	 * @param int         $uid User ID
	 * @param int         $pid Patient ID
	 * @param null|int    $eid Encounter ID
	 * @param null        $referral_id
	 * @param null|string $start_date
	 * @param null|string $end_date
	 * @param array       $excludes
	 *
	 * @return array|string
	 */
	public function getRecord($fid, $uid, $pid, $eid = null, $referral_id = null, $start_date = null, $end_date = null, $excludes = []){

		// globals
		$this->buff = [];
		$this->patient_record = [];
		$this->pid = $pid;
		$this->eid = $eid;
		$this->referral_id = $referral_id;

		if(isset($start_date) && $start_date !== ''){
			$this->start_date = $start_date;
		}
		if(isset($end_date) && $end_date !== ''){
			$this->end_date = $end_date;
		}

		if(isset($excludes)){
			$this->excludes = $excludes;
		}

		$this->getPatientData();
		$this->resetHeader();

		$this->getRecordTarget();
		$this->getAuthor($uid);
		$this->getDataEnterer($uid);
		$this->getInformant($uid);
		$this->getCustodian($uid);
		$this->getInformationRecipient($uid);
		$this->getLegalAuthenticator($uid);
		$this->getAuthenticator($uid);

		$this->getProblemSection(); // Problems
		$this->getMedicationsSection(); // Medications
		$this->getAllergiesAndIntolerancesSection(); // MedicationAllergies
		$this->getResultsSection(); // LaboratoryTests & LaboratoryValues
		$this->getVitalSignsSection(); // VitalSigns
		$this->getProceduresSection(); // Procedures
		$this->getImmunizationsSection(); // Immunizations
		$this->getImplantableDevices();
		$this->getEncountersSection();
		$this->getSmokingStatus(); // SmokingStatus
		$this->getSocialHistorySection();
		$this->getFamilyHistorySection();
		$this->getFunctionalStatusSection();
		$this->getMentalStatusSection();
		$this->getMedicationsAdministeredSection(); // MedicationsAdministered

		// CarePlanFields
		// CareTeamMembers
		// ProvidersName
		// DateLocationVisit
		// ReasonForVisit
		// DiagnosticTestsPending
		// FutureAppointments
		// ReferralsToOtherProviders
		// FutureScheduledTests
		// ClinicalInstructions

		if(isset($this->eid)){
			$this->getEncounterSection();
			$this->getReasonForReferralSection();
			$this->getPlanOfTreatment();
			$this->getCarePlanGoals();
		}

    	$this->getHealthConcernsSection();


		if($this->returnType === 'json'){
			return json_encode($this->patient_record);
		}

		if($this->returnType === 'xml'){
			include_once(ROOT . '/classes/Array2XML.php');
			Array2XML::init('1.0', 'UTF-8', true);
			$xml = Array2XML::createXML('Record', $this->patient_record);

			return $xml->saveXML();
		}

		return $this->patient_record;
	}

	/**
	 *
	 */
	private function getProblemSection(){
		if($this->isExcluded('ProblemSection')) return;

		include_once(ROOT . '/dataProvider/ActiveProblems.php');
		$ActiveProblems = new ActiveProblems();
		$results = $ActiveProblems->getPatientAllProblemsByPidAndDates($this->pid, $this->start_date, $this->end_date);
		unset($ActiveProblems);
		$problems = [];

		foreach($results as $result){
			$problem = [];
			$problem['Id'] = $result['id'];
			$problem['Code'] = $this->code($result['code'], $result['code_type'], $result['code_text']);
			$problem['Status'] = $this->code($result['status_code'], $result['status_code_type'], $result['status']);
			$problem['Type'] = $this->code($result['problem_type_code'], $result['problem_type_code_type'], $result['problem_type']);
			$problem['Dates'] = $this->dates($result['begin_date'], $result['end_date']);
			$problem['CreatedDate'] = $result['create_date'];
			$problems[] = $problem;
		}
		unset($results);

		if(empty($problems)){
			$this->patient_record['ProblemSection'] = [];
			return;
		}

		$this->patient_record['ProblemSection']['Problem'] = $problems;

	}

	private function getMedicationsSection(){
		if($this->isExcluded('MedicationsSection')) return;

		include_once(ROOT . '/dataProvider/Medications.php');
		$Medications = new Medications();
		$results = $Medications->getPatientActiveMedicationsByPidAndDates($this->pid, true);
		unset($Medications);
		$medications = [];

		foreach($results as $result){
			$active = $this->isActiveByDate($result['end_date']);
			$medication = [];
			$medication['Id'] = $result['id'];
			$medication['Medication'] = $result['STR'];
			$medication['Instructions'] = $result['directions'];
			$medication['Route'] = $result['route'];
			$medication['Quantity'] = $result['dispense'];
			$medication['Status'] = $active ? 'Active' : 'Inactive';
			$medication['RXCUI'] = $result['RXCUI'];
			$medication['NDC'] = $result['NDC'];
			$medication['Dates'] = $this->dates($result['begin_date'], $result['end_date']);
			$medication['Performer'] = $this->performer($result['uid']);

			$medications[] = $medication;
		}

		$this->patient_record['MedicationsSection']['Medication'] = $medications;
	}

	private function getAllergiesAndIntolerancesSection(){
		if($this->isExcluded('AllergiesAndIntolerancesSection')) return;

		include_once(ROOT . '/dataProvider/Allergies.php');
		$Allergies = new Allergies();
		$results = $Allergies->getPatientAllergiesByPidAndDates($this->pid, $this->start_date, $this->end_date);
		unset($Allergies);
		$allergies = [];

		foreach($results as $result){
			$allergy = [];
			$allergy['Id'] = $result['id'];
			$allergy['Type'] = $this->code(
				$result['allergy_type_code'],
				$result['allergy_type_code_type'],
				$result['allergy_type']
			);
			$allergy['Substance'] = $this->code(
				$result['allergy_code'],
				$result['allergy_code_type'],
				$result['allergy']
			);
			$allergy['Reaction'] = $this->code(
				$result['reaction_code'],
				$result['reaction_code_type'],
				$result['reaction']
			);

			$allergy['Severity'] = $this->code(
				$result['severity_code'],
				$result['severity_code_type'],
				$result['severity']
			);

			$allergy['Status'] = $this->code(
				$result['status_code'],
				$result['status_code_type'],
				$result['status']
			);
			$allergy['Dates'] = $this->dates($result['begin_date'], $result['end_date']);
			$allergies[] = $allergy;
		}

		$this->patient_record['AllergiesAndIntolerancesSection']['AllergyAndIntolerance'] = $allergies;
	}

	private function getResultsSection(){
		if($this->isExcluded('ResultsSection')) return;

		include_once(ROOT . '/dataProvider/Orders.php');
		$Orders = new Orders();
		$results = $Orders->getOrderWithResultsByPidAndDates($this->pid, $this->start_date, $this->end_date);
		unset($Orders);
		$results_data = [];

		foreach($results as $result){
			$result_data = [];

			$result_data['Order'] = $this->code(
				$result['code'],
				$result['code_type'],
				$result['description']
			);

			$result_data['LabOrderNumber'] = $result['result']['lab_order_id'];
			$result_data['LabName'] = $result['result']['lab_name'];
			$result_data['LabAddress'] = $result['result']['lab_address'];
			$result_data['Status'] = $result['result']['result_status'];

			$result_data['Dates'] = $this->dates(
				$result['result']['result_date'],
				$result['result']['result_date']
			);

			$result_data['Specimen'] = $this->code(
				$result['result']['specimen_code'],
				$result['result']['specimen_code_type'],
				$result['result']['specimen_text']
			);
			$result_data['SpecimenNotes'] = $result['result']['specimen_notes'];
			$result_data['Observations'] = [];

			foreach($result['result']['observations'] as $observation){
				$obs = [];

				$obs['Id'] = $observation['id'];
				$obs['ParentId'] = isset($observation['parent_id']) ? $observation['parent_id'] : '0';
				$obs['Observation'] = $this->code(
					$observation['code'],
					$observation['code_type'],
					$observation['code_text']
				);

				$obs['Value'] = $observation['value'];
				$obs['Unit'] = $observation['units'];
				$obs['InterpretationCode'] = $this->code(
					$observation['abnormal_flag'],
					'ObservationInterpretation'
				);

				$ranges = preg_split("/to|-/", $observation['reference_rage']);
				if(is_array($ranges) && count($ranges) > 2){
					$obs['ReferenceRange']['Low']['Value'] = $ranges[0];
					$obs['ReferenceRange']['Low']['Unit'] = $observation['units'];
					$obs['ReferenceRange']['High']['Value'] = $ranges[1];
					$obs['ReferenceRange']['High']['Unit'] = $observation['units'];
				}else if(is_array($ranges) && count($ranges) == 1){
					$obs['ReferenceRange']['Value'] = $ranges[0];
					$obs['ReferenceRange']['Unit'] = $observation['units'];
				}else{
					$obs['ReferenceRange'] = 'UNK';
				}

				$obs['Dates'] = $this->dates(
					$observation['date_observation'],
					$observation['date_observation']
				);

				$result_data['Observations'][] = $obs;
			}

			$results_data[] = $result_data;
		}

		$this->patient_record['ResultsSection']['Results'] = $results_data;
	}

	private function getVitalSignsSection(){
		if($this->isExcluded('VitalSignsSection')) return;

		include_once(ROOT . '/dataProvider/Vitals.php');
		$Vitals = new Vitals();
		$results = $Vitals->getVitalsByPidAndDate($this->pid, $this->start_date, $this->end_date);
		unset($Vitals);
		$vitals = [];

		$height_units = $this->isMetric() ? 'cm' : 'in';
		$weight_units = $this->isMetric() ? 'kg' : 'lbs';

		foreach($results as $result){
			$vital = [];
			$vital['Id'] = $result['id'];

			$vital['Dates'] = $this->dates(
				$result['date'],
				$result['date']
			);

			$vital['Performer'] = $this->performer($result['uid']);
			$vital['Authorized'] = $this->performer($result['auth_uid']);

			$vital['Observations'] = [];

			$height = $this->isMetric() ? $result['height_cm'] : $result['height_in'];
			$obs = [];
			$obs['Alias'] = 'HT';
			$obs['Observation'] = $this->code(
				'8302-2',
				'LOINC',
				'Height'
			);
			$obs['Value']['Value'] = $height;
			$obs['Value']['Unit'] = $height_units;
			$vital['Observations'][] = $obs;

			$weight = $this->isMetric() ? $result['weight_kg'] : $result['weight_lbs'];
			$obs = [];
			$obs['Alias'] = 'WT';
			$obs['Observation'] = $this->code(
				'3141-9',
				'LOINC',
				'Weight Measured'
			);
			$obs['Value']['Value'] = $weight;
			$obs['Value']['Unit'] = $weight_units;
			$vital['Observations'][] = $obs;

			$obs = [];
			$obs['Alias'] = 'BPS';
			$obs['Observation'] = $this->code(
				'8480-6',
				'LOINC',
				'BP Systolic'
			);
			$obs['Value']['Value'] = $result['bp_systolic'];
			$obs['Value']['Unit'] = 'mm[Hg]';
			$vital['Observations'][] = $obs;

			$obs = [];
			$obs['Alias'] = 'BPD';
			$obs['Observation'] = $this->code(
				'8462-4',
				'LOINC',
				'BP Diastolic'
			);
			$obs['Value']['Value'] = $result['bp_diastolic'];
			$obs['Value']['Unit'] = 'mm[Hg]';
			$vital['Observations'][] = $obs;

			$obs = [];
			$obs['Alias'] = 'BM';
			$obs['Observation'] = $this->code(
				'39156-5',
				'LOINC',
				'Body mass index (BMI) [Ratio]'
			);
			$obs['Value']['Value'] = $result['bmi'];
			$obs['Value']['Unit'] = 'kg/m2';
			$vital['Observations'][] = $obs;

			$obs = [];
			$obs['Alias'] = 'O2';
			$obs['Observation'] = $this->code(
				'59408-5',
				'LOINC',
				'O2 % BldC Oximetry'
			);
			$obs['Value']['Value'] = $result['oxygen_saturation'];
			$obs['Value']['Unit'] = '%';
			$vital['Observations'][] = $obs;

			$obs = [];
			$obs['Alias'] = 'O2';
			$obs['Observation'] = $this->code(
				'3150-0',
				'LOINC',
				'Inhaled Oxygen Concentration'
			);
			$obs['Value']['Value'] = $result['oxygen_inhaled_concentration'];
			$obs['Value']['Unit'] = '%';
			$vital['Observations'][] = $obs;

			$obs = [];
			$obs['Alias'] = 'BT';
			$obs['Observation'] = $this->code(
				'8310-5',
				'LOINC',
				'Body Temperature'
			);
			$obs['Value']['Value'] = '';
			$obs['Value']['Unit'] = 'Cel';
			$vital['Observations'][] = $obs;

			$obs = [];
			$obs['Alias'] = 'RR';
			$obs['Observation'] = $this->code(
				'9279-1',
				'LOINC',
				'Respiratory Rate'
			);
			$obs['Value']['Value'] = '';
			$obs['Value']['Unit'] = 'Cel';
			$vital['Observations'][] = $obs;

			$obs = [];
			$obs['Alias'] = 'HR';
			$obs['Observation'] = $this->code(
				'8867-4',
				'LOINC',
				'Heart Rate'
			);
			$obs['Value']['Value'] = '';
			$obs['Value']['Unit'] = '/min';
			$vital['Observations'][] = $obs;

			$vitals[] = $vital;
		}

		$this->patient_record['VitalSignsSection']['VitalSigns'] = $vitals;
	}

	private function getProceduresSection(){
		if($this->isExcluded('ProceduresSection')) return;

		include_once(ROOT . '/dataProvider/Procedures.php');
		$Procedures = new Procedures();
		$results = $Procedures->getPatientProceduresByPidAndDates($this->pid, $this->start_date, $this->end_date);
		unset($Procedures);
		$procedures = [];

		foreach($results as $result){
			$procedure = [];

			$procedure['Id'] = $result['id'];
			$procedure['Procedure'] = $this->code(
				$result['code'],
				$result['code_type'],
				$result['code_text']
			);

			$procedure['Status'] = $this->code(
				$result['status_code'],
				$result['status_code_type'],
				$result['status_code_text']
			);

			$procedure['TargetSite'] = $this->code(
				$result['target_site_code'],
				$result['target_site_code_type'],
				$result['target_site_code_text']
			);

			$procedure['Dates'] = $this->dates(
				$result['procedure_date'], null
			);

			$procedure['Observation'] = $result['observation'];

			$procedure['Performer'] = $this->performer($result['performer_id']);

			$procedures[] = $procedure;
		}


		include_once(ROOT . '/dataProvider/ProcedureHistory.php');
		$Procedures = new ProcedureHistory();
		$results = $Procedures->getPatientProcedureHistoryByPidAndDates($this->pid, $this->start_date, $this->end_date);
		unset($Procedures);

		foreach($results as $result){
			$procedure = [];

			$procedure['Id'] = $result['id'];
			$procedure['Procedure'] = $this->code(
				$result['procedure_code'],
				$result['procedure_code_type'],
				$result['procedure']
			);

			$procedure['Status'] = $this->code(
				'completed',
				null,
				'Completed'
			);

			$procedure['TargetSite'] = $this->code(
				$result['target_site_code'],
				$result['target_site_code_type'],
				$result['target_site_code_text']
			);

			$procedure['Dates'] = $this->dates(
				$result['performed_date'], null
			);

			$procedure['Observation'] = $result['observation'];
			$procedure['Performer'] = $this->externalPerformer($result['performer_id']);
			$procedure['ServiceLocation'] = $this->externalPerformer($result['service_location_id']);

			$procedures[] = $procedure;
		}



		$this->patient_record['ProceduresSection']['Procedure'] = $procedures;
	}

	private function getImmunizationsSection(){
		if($this->isExcluded('ImmunizationsSection')) return;

		include_once(ROOT . '/dataProvider/Immunizations.php');
		$Immunizations = new Immunizations();
		$results = $Immunizations->getPatientImmunizationsByPidAndDates($this->pid, $this->start_date, $this->end_date);
		unset($Immunizations);
		$immunizations = [];

		foreach($results as $result){

			$immunization = [];
			$immunization['Id'] = $result['id'];
			$immunization['Vaccine'] = $this->code(
				$result['code'],
				$result['code_type'],
				$result['vaccine_name']
			);
			$immunization['Route'] = $result['route'];
			$immunization['LotNumber'] = $result['lot_number'];
			$immunization['Manufacturer'] = $result['manufacturer'];

			if(isset($result['refusal_reason_code']) && $result['refusal_reason_code'] != ''){
				$immunization['RefusalReason'] = $this->code(
					$result['refusal_reason_code'],
					'ActNoImmunizationReason'
				);
			}else{
				$immunization['RefusalReason'] = 'NI';
			}

			$immunization['Status'] = isset($result['status']) ? ucfirst($result['status']) : 'UNK';
			$immunization['Note'] = $result['note'];

			$immunization['Administration']['Dose'] = $result['administer_amount'];
			$immunization['Administration']['Unit'] = $result['administer_units'];
			$immunization['Administration']['Dates'] = $this->dates(
				$result['administered_date'],
				$result['administered_date']
			);

			$educationGiven = isset($result['education_resource_1_id']) && $result['education_resource_1_id'] !== 0;
			$immunization['Administration']['EducationGiven'] = $educationGiven ? 'Yes' : 'No';
			$immunization['Administration']['Performer'] = $this->performer($result['administered_uid']);

			$immunizations[] = $immunization;
		}

		$this->patient_record['ImmunizationsSection']['Immunization'] = $immunizations;
	}

	private function getImplantableDevices(){
		if($this->isExcluded('implantable_devices')) return;

		include_once(ROOT . '/dataProvider/ImplantableDevice.php');
		$ImplantableDevice = new ImplantableDevice();
		$results = $ImplantableDevice->getPatientImplantableDeviceByPidAndDates($this->pid, $this->start_date, $this->end_date);
		unset($ImplantableDevice);
		$devices = [];

		foreach($results as $result){
			$device = [];

			$device['Id'] = $result['id'];

			$device['Dates'] = $this->dates(
				$result['implanted_date'],
				$result['removed_date']
			);

			$device['Description'] = $this->code(
				$result['description_code'],
				$result['description_code_type'],
				$result['description']
			);

			$device['Status'] = $this->code(
				$result['status_code'],
				$result['status_code_type'],
				$result['status']
			);

			$device['Device']['DI'] = $result['di'];
			$device['Device']['LotNumber'] = $result['lot_number'];
			$device['Device']['SerialNumber'] = $result['serial_number'];
			$device['Device']['ExpirationDate'] = $result['exp_date'];
			$device['Device']['ManufacturedDate'] = $result['mfg_date'];
			$device['Device']['DonationID'] = $result['donation_id'];

			$device['Production']['UDI'] = $result['udi'];
			$device['Production']['GMDNPTName'] = $result['gmdnpt_name'];
			$device['Production']['BrandName'] = $result['brand_name'];
			$device['Production']['VersionModel'] = $result['version_model'];
			$device['Production']['CompanyName'] = $result['company_name'];
			$device['Production']['MRISafetyInfo'] = $result['mri_safety_info'];
			$device['Production']['RequiredLNR'] = $result['required_lnr'];

			$device['Performer'] = $this->performer($result['create_uid']);

			$devices[] = $device;
		}
		$this->patient_record['ImplantableDevices']['ImplantableDevice'] = $devices;
	}

	private function getAssessmentAndPlanSection(){
		if($this->isExcluded('AssessmentAndPlanSection')) return;

		// TODO...

		$this->patient_record['AssessmentAndPlanSection']['AssessmentAndPlan'] = [];
	}

	private function getGoalsSection(){
		if($this->isExcluded('GoalsSection')) return;

		include_once(ROOT . '/dataProvider/CarePlanGoals.php');
		$CarePlanGoals = new CarePlanGoals();
		$results = $CarePlanGoals->getPatientCarePlanGoalsByPid($this->pid);
		unset($CarePlanGoals);
		$goals = [];

		// Procedures
		foreach($results as $result){
			$goal = [];
			$goal['MoodCode'] = 'RQO';
			$goal['ClassCode'] = 'PROC';
			$goal['Type'] = 'Procedure';
			$goal['Goal'] = $this->code(
				$result['goal_code'],
				$result['goal_code_type'],
				$result['goal']
			);
			$goal['Dates'] = $this->dates(
				$result['plan_date'],
				$result['plan_date']
			);
			$goal['Instructions'] = $result['instructions'];

			$goals[] = $goal;
		}

		// Appointments
		foreach($results as $result){
			$goal = [];
			$goal['MoodCode'] = 'RQO';
			$goal['ClassCode'] = 'ACT';
			$goal['Type'] = 'Appointment';
			$goal['Goal'] = $this->code(
				$result['goal_code'],
				$result['goal_code_type'],
				$result['goal']
			);
			$goal['Dates'] = $this->dates(
				$result['plan_date'],
				$result['plan_date']
			);
			$goal['Instructions'] = $result['instructions'];

			$goals[] = $goal;
		}

		// Schedule Tests
		foreach($results as $result){
			$goal = [];
			$goal['MoodCode'] = 'RQO';
			$goal['ClassCode'] = 'OBS';
			$goal['Type'] = 'ScheduleTests';
			$goal['Goal'] = $this->code(
				$result['goal_code'],
				$result['goal_code_type'],
				$result['goal']
			);
			$goal['Dates'] = $this->dates(
				$result['plan_date'],
				$result['plan_date']
			);
			$goal['Instructions'] = $result['instructions'];

			$goals[] = $goal;
		}

		// Referrals
		foreach($results as $result){
			$goal = [];
			$goal['MoodCode'] = 'RQO';
			$goal['ClassCode'] = 'ACT';
			$goal['Type'] = 'ScheduleTests';
			$goal['Goal'] = $this->code(
				$result['goal_code'],
				$result['goal_code_type'],
				$result['goal']
			);
			$goal['Dates'] = $this->dates(
				$result['plan_date'],
				$result['plan_date']
			);
			$goal['Instructions'] = $result['instructions'];

			$goals[] = $goal;
		}

		$this->patient_record['GoalsSection']['Goal'] = $goals;
	}

	private function getHealthConcernsSection(){
		if($this->isExcluded('HealthConcernsSection')) return;

		$health_concerns = [];

		// SOAP health status
		include_once(ROOT . '/dataProvider/Encounter.php');
		$Encounter = new Encounter();
		if(isset($this->eid)){
			$soaps = [ $Encounter->getSoapByEid($this->eid) ];
		}else{
			$soaps = $Encounter->getSoaps(['pid' => $this->pid]);
		}

		foreach ($soaps as $soap){

			if(!isset($soap['health_status'])) continue;

			$health_concern = [
				'Id' => 'e-'. $soap['id'],
				'Type' => 'HealthStatusObservation',
				'Description' => $this->code($soap['health_status_code'],$soap['health_status_code_type'], $soap['health_status']),
				'Instructions' => '',
				'Dates' => $this->dates($soap['date'], null)
			];

			$health_concerns[] = $health_concern;
		}


		include_once(ROOT . '/dataProvider/HealthConcerns.php');
		$HealthConcerns = new HealthConcerns();
		if(isset($this->eid)){
			$concerns = $HealthConcerns->getPatientHealthConcernsByEid($this->eid);
		}else{
			$concerns = $HealthConcerns->getPatientHealthConcernsByPid($this->pid);
		}

		foreach ($concerns as $concern){

			$health_concern = [
				'Id' => 'c-' . $concern['id'],
				'Type' => $concern['health_concern_type'],
				'Description' => $concern['description'],
				'Instructions' => $concern['instructions'],
				'Dates' => $this->dates($concern['active_from'], $concern['active_to'])
			];

			$health_concerns[] = $health_concern;

		}

		$this->patient_record['HealthConcernsSection']['HealthConcern'] = $health_concerns;
	}

	private function getEncounterSection(){

		$encounter = [];

		if(!isset($this->eid)){
			$this->patient_record['EncounterSection']['Encounter'] = $encounter;

			return;
		}

		include_once(ROOT . '/dataProvider/Encounter.php');
		$Encounter = new Encounter();
		$result = $Encounter->getEncounterByEid($this->eid);

		if($result === false){
			$this->patient_record['EncounterSection']['Encounter'] = $encounter;

			return;
		}
		$soap = $Encounter->getSoapByEid($result['eid']);
		$encounter = $this->encounter($result, $soap);
		unset($Encounter);

		$this->patient_record['EncounterSection']['Encounter'] = $encounter;
	}

	private function getEncountersSection(){
		if($this->isExcluded('EncountersSection')) return;

		include_once(ROOT . '/dataProvider/Encounter.php');
		$Encounter = new Encounter();
		$results = $Encounter->getEncountersByPidAndDates($this->pid, $this->start_date, $this->end_date);

		$encounters = [];

		foreach($results as $result){
			$soap = $Encounter->getSoapByEid($result['eid']);
			$encounters[] = $this->encounter($result, $soap);
		}

		unset($Encounter);

		$this->patient_record['EncountersSection']['Encounter'] = $encounters;
	}

	private function getSmokingStatus(){
		if($this->isExcluded('smoking_status')) return;

		include_once(ROOT . '/dataProvider/SocialHistory.php');
		$SocialHistory = new SocialHistory();
		$results = $SocialHistory->getSmokeStatusByPidAndDates($this->pid, $this->start_date, $this->end_date);
		unset($SocialHistory);
		$status = [];

		if(count($results)){
			$result = end($results);

			$status['Dates'] = $this->dates(
				$result['create_date'],
				$result['create_date']
			);
			$status['Status'] = $this->code(
				$result['status_code'],
				$result['status_code_type'],
				$result['status']
			);
			$status['Note'] = $result['note'];
			$status['CounselingGiven'] = $result['counseling'] ? 'Yes' : 'No';
			$status['Performer'] = $this->performer($result['create_uid']);
		}

		$this->patient_record['SmokingStatus'] = $status;
	}

	private function getSocialHistorySection(){
		if($this->isExcluded('SocialHistorySection')) return;

		include_once(ROOT . '/dataProvider/SocialHistory.php');
		$SocialHistory = new SocialHistory();
		$results = $SocialHistory->getSocialHistoriesByPidAndDates($this->pid, $this->start_date, $this->end_date);
		$smoke_statuses = $SocialHistory->getSmokeStatusByPidAndDates($this->pid, $this->start_date, $this->end_date);

		unset($SocialHistory);
		$histories = [];

		foreach($results as $result){
			$history = [];
			$history['Id'] = 'a-' . $result['id'];
			$history['Dates'] = $this->dates(
				$result['start_date'],
				$result['end_date']
			);

			$history['Category'] = $this->code(
				$result['category_code'],
				$result['category_code_type'],
				$result['category_code_text']
			);

			$history['Observation'] = $this->code(
				$result['observation_code'],
				$result['observation_code_type'],
				$result['observation']
			);

			$history['Note'] = $result['note'];
			$status['CounselingGiven'] = 'UNK';
			$history['Performer'] = $this->performer($result['create_uid']);

			$histories[] = $history;
		}

		$current_smoke_status = array_pop($smoke_statuses);
		if(isset($current_smoke_status)){

			$status = [];
			$status['Id'] = 'b-' . $current_smoke_status['id'];
			$status['Dates'] = $this->dates(
				$current_smoke_status['start_date'],
				$current_smoke_status['end_date']
			);

			$status['Category'] = $this->code(
				'72166-2',
				'LOINC',
				'Tobacco smoking status NHIS'
			);

			$status['Observation'] = $this->code(
				$current_smoke_status['status_code'],
				$current_smoke_status['status_code_type'],
				$current_smoke_status['status']
			);
			$status['Note'] = $current_smoke_status['note'];
			$status['CounselingGiven'] = $current_smoke_status['counseling'] ? 'Yes' : 'No';
			$status['Performer'] = $this->performer($current_smoke_status['create_uid']);

			$histories[] = $status;
		}


		// smoke status
		foreach ($smoke_statuses as $smoke_status){

			$status = [];
			$status['Id'] = 'c-' . $smoke_status['id'];
			$status['Dates'] = $this->dates(
				$smoke_status['start_date'],
				$smoke_status['end_date']
			);

			$status['Category'] = $this->code(
				'11367-0',
				'LOINC',
				'History of Tobacco use'
			);

			$status['Observation'] = $this->code(
				$smoke_status['status_code'],
				$smoke_status['status_code_type'],
				$smoke_status['status']
			);
			$status['Note'] = $smoke_status['note'];
			$status['CounselingGiven'] = $smoke_status['counseling'] ? 'Yes' : 'No';
			$status['Performer'] = $this->performer($smoke_status['create_uid']);

			$histories[] = $status;
		}


		// AdministrativeGender
		if(isset($this->patient_record['RecordTarget']['PatientRole']['Patient']['AdministrativeGenderCode'])){

			$status = [];

			$gender =  $this->patient_record['RecordTarget']['PatientRole']['Patient']['AdministrativeGenderCode'];
			$status['Id'] = '-g-' . $this->patient_record['RecordTarget']['PatientRole']['Patient']['Id'];
			$status['Dates'] = $this->dates(
				null, null
			);

			$status['Category'] = $this->code(
				'76689-9',
				'LOINC',
				'Sex assigned at birth'
			);

			$status['Observation'] = $this->code(
				$gender['Code'],
				$gender['CodeSystemName'],
				$gender['DisplayName']
			);

			$status['Note'] = '';
			$status['CounselingGiven'] = 'UNK';
			$status['Performer'] = 'UNK';

			$histories[] = $status;
		}






		$this->patient_record['SocialHistorySection']['SocialHistory'] = $histories;
	}

	private function getFamilyHistorySection(){
		if($this->isExcluded('social_history')) return;

		include_once(ROOT . '/dataProvider/FamilyHistory.php');
		$SocialHistory = new FamilyHistory();
		$results = $SocialHistory->getFamilyHistoryByPidAndDates($this->pid, $this->start_date, $this->end_date);
		unset($SocialHistory);
		$histories = [];

		foreach($results as $result){

			$history = [];

			$history['Dates'] = $this->dates(
				$result['create_date'],
				$result['create_date']
			);

			$history['Condition'] = $this->code(
				$result['condition_code'],
				$result['condition_code_type'],
				$result['condition']
			);

			$history['Relation'] = $this->code(
				$result['relation_code'],
				$result['relation_code_type'],
				$result['relation']
			);

			$history['Performer'] = $this->performer($result['create_uid']);

			$histories[] = $history;
		}

		$this->patient_record['FamilyHistorySection']['FamilyHistory'] = $histories;
	}

	private function getMentalStatusSection(){
		if($this->isExcluded('MentalStatusSection')) return;

		include_once(ROOT . '/dataProvider/CognitiveAndFunctionalStatus.php');
		$CognitiveAndFunctionalStatus = new CognitiveAndFunctionalStatus();
		$results = $CognitiveAndFunctionalStatus->getPatientMentalStatusesByPidAndDates($this->pid, $this->start_date, $this->end_date);
		unset($CognitiveAndFunctionalStatus);

		$statuses = [];
		foreach($results as $result){
			$status = [];
			$status['Id'] = $result['id'];
			$status['Observation'] = $this->code(
				$result['code'],
				$result['code_type'],
				$result['code_text']
			);

			$status['Status'] = $this->code(
				$result['status_code'],
				$result['status_code_type'],
				$result['status']
			);

			$status['Dates'] = $this->dates(
				$result['begin_date'],
				$result['end_date']
			);

			$status['Note'] = $result['note'];
			$status['Performer'] = $this->performer($result['uid']);

			$statuses[] = $status;
		}


		$this->patient_record['MentalStatusSection']['MentalStatus'] = $statuses;
	}

	private function getFunctionalStatusSection(){
		if($this->isExcluded('FunctionalStatusSection')) return;

		include_once(ROOT . '/dataProvider/CognitiveAndFunctionalStatus.php');
		$CognitiveAndFunctionalStatus = new CognitiveAndFunctionalStatus();
		$results = $CognitiveAndFunctionalStatus->getPatientFunctionalStatusesByPidAndDates($this->pid, $this->start_date, $this->end_date);
		unset($CognitiveAndFunctionalStatus);

		$statuses = [];
		foreach($results as $result){
			$status = [];

			$status['Id'] = $result['id'];

			$status['Observation'] = $this->code(
				$result['code'],
				$result['code_type'],
				$result['code_text']
			);

			$status['Category'] = $this->code(
				$result['category_code'],
				$result['category_code_type'],
				$result['category']
			);

			$status['Status'] = $this->code(
				$result['status_code'],
				$result['status_code_type'],
				$result['status']
			);

			$status['Dates'] = $this->dates(
				$result['begin_date'],
				$result['end_date']
			);

			$status['Note'] = $result['note'];
			$status['Performer'] = $this->performer($result['uid']);

			$statuses[] = $status;
		}

		$this->patient_record['FunctionalStatusSection']['FunctionalStatus'] = $statuses;
	}

	private function getMedicationsAdministeredSection(){
		if($this->isExcluded('MedicationsAdministeredSection')) return;

		include_once(ROOT . '/dataProvider/Medications.php');
		$Medications = new Medications();
		$results = $Medications->getPatientAdministeredMedicationsByPidAndEid($this->pid, $this->eid);
		unset($Medications);
		$medications = [];

		foreach($results as $result){
			$active = $this->isActiveByDate($result['end_date']);
			$medication = [];
			$medication['Medication'] = $result['STR'];
			$medication['Instructions'] = $result['directions'];
			$medication['Status'] = $active ? 'Active' : 'Inactive';
			$medication['RXCUI'] = $result['RXCUI'];
			$medication['NDC'] = $result['NDC'];
			$medication['Dates'] = $this->dates($result['administered_date'], $result['administered_date']);
			$medication['Performer'] = $this->performer($result['administered_uid']);

			$medications[] = $medication;
		}

		$this->patient_record['MedicationsAdministeredSection']['MedicationAdministered'] = $medications;
	}

	private function getReasonForReferralSection(){
		if($this->isExcluded('ReasonForReferralSection')) return;

		include_once(ROOT . '/dataProvider/Referrals.php');
		$Referrals = new Referrals();

		$results = $Referrals->getPatientReferralsByEid($this->eid);

		$referrals = [];

		foreach ($results as $result){

			$referral = [];
			$referral['Id'] = $result['id'];
			$referral['Reason'] = $result['referal_reason'];
			$referral['ToProvider'] = '';
			$referral['Dates'] = '';
			$referral['ScheduleDate '] = '';
			$referral['Organization'] = $this->organization($result['facility_id']);
			$referral['ReferTo'] = $this->externalPerformer($result['refer_to']);

			if(
				isset($this->patient_record['EncounterSection']['Encounter']) &&
				isset($this->patient_record['EncounterSection']['Encounter']['Id']) &&
				$this->patient_record['EncounterSection']['Encounter']['Id'] ==  $result['eid']
			){
				$referrals[] = $referral;
				$this->patient_record['EncounterSection']['Encounter']['ReferTo'][] = $referral['ReferTo'];
			}

			$this->patient_record['ReasonForReferralSection']['ReasonForReferrals'] = $referrals;
		}

	}

	private function getCarePlanGoals(){
		include_once(ROOT . '/dataProvider/CarePlanGoals.php');
		$CarePlanGoals = new CarePlanGoals();
		$goals = $CarePlanGoals->getPatientCarePlanGoalsByEid($this->eid);

		$goals_data = [];

		foreach ($goals as $goal){
			$data['Id'] = $goal['id'];
			$data['Code'] = $this->code($goal['goal_code'], $goal['goal_code_type'], $goal['goal']);
			$data['Dates'] = $this->dates($goal['plan_date'], $goal['plan_date']);
			$data['Narrative'] = $goal['instructions'];
			$data['Status'] = 'Active';
			if(is_string($data['Code'])){
				$data['ValueType'] = 'ST';
			}else{
				$data['ValueType'] = 'PQ';
			}

			$goals_data[] = $data;
		}

		$this->patient_record['CarePlanGoalSection']['CarePlanGoal'] = $goals_data;

	}

	private function getPlanOfTreatment(){

		include_once(ROOT . '/dataProvider/Orders.php');
		include_once(ROOT . '/dataProvider/Referrals.php');
		include_once(ROOT . '/dataProvider/AppointmentRequest.php');

		// lab orders
		$Orders = new Orders();
		$plan_of_care_data['LAB'] = $Orders->getPatientLabOrdersByEid($this->eid);

		// rad order
		$Orders = new Medications();
		$plan_of_care_data['MED'] = $Orders->getPatientMedicationsOrdersByEid($this->eid);

//		$Referrals = new Referrals();
//		$plan_of_care_data['REF'] = $Referrals->getPatientReferralsByEid($this->eid);

		$Appointments = new AppointmentRequest();
		$plan_of_care_data['APP'] = $Appointments->getAppointmentRequestsByEid($this->pid);

		$pocs_data = [];

		foreach ($plan_of_care_data as $type => $plan_of_care){

			foreach ($plan_of_care as $poc){

				$data = [];

				if($type === 'LAB'){

					$data['Id'] = 'lab-' . $poc['id'];
					$data['Code'] = $this->code($poc['code'], $poc['code_type'], $poc['description']);
					$data['Dates'] = $this->dates($poc['date_ordered'], null);
					$data['Narrative'] = '';
					$data['Status'] = $poc['status'];
					$data['Type'] = 'OBS';
					$data['TypeMoodCode'] = 'RQO';
					//$data['TargetSite'] = $this->code('','');

				}elseif ($type === 'MED'){

					$data['Id'] = 'med-' . $poc['id'];
					$data['Code'] = $this->code($poc['CODE'], $poc['RXNORM'], $poc['STR']);
					$data['Dates'] = $this->dates($poc['date_ordered'], null);
					$instruction = sprintf('Take %s %s beginning %s', $poc['STR'],$poc['directions'],  date('F j, Y', strtotime($poc['begin_date'])));
					$data['Narrative'] = $instruction;
					$data['Status'] = 'Active';
					$data['Type'] = 'OBS';
					$data['TypeMoodCode'] = 'RQO';
					//$data['TargetSite'] = $this->code('','');

				}elseif ($type === 'REF'){

//					$data['Id'] = 'ref-' . $poc['id'];
//					$data['Code'] = null;
//					$data['Dates'] = $this->dates($poc['referral_date'], $poc['referral_date']);
//					$data['Narrative'] = $poc['referal_reason'];
//					$data['Status'] = '1';
//					$data['Type'] = 'ACT';
//					$data['TypeMoodCode'] = 'RQO';

				}elseif ($type === 'APP'){

					$data['Id'] = 'app-' . $poc['id'];
					$data['Code'] = $this->code('281189005', 'SNOMEDCT', $poc['notes']);
					$data['Dates'] = $this->dates($poc['requested_date'], $poc['requested_date']);
					$data['Narrative'] = '';
					$data['Status'] = 'new';
					$data['Type'] = 'PROC';
					$data['TypeMoodCode'] = 'RQO';

				}

				if(!empty($data)){
					$pocs_data[] = $data;
				}
			}

		}

		$this->patient_record['PlanOfTreatmentSection']['PlanOfTreatment'] = $pocs_data;

	}

	private function getPatientData(){
		$this->Patient = new Patient($this->pid);
	}

	/**
	 *
	 */
	private function getRecordTarget(){
		$patientData = $this->Patient->getPatient();

		$PatientRole = [];

		if(!$this->isExcluded('patient_address')){
			$PatientRole['Address'] = $this->address(
				'HP',
				$patientData['postal_address'],
				$patientData['postal_address_cont'],
				$patientData['postal_city'],
				$patientData['postal_state'],
				$patientData['postal_zip'],
				$patientData['postal_country']
			);
		}

		if(!$this->isExcluded('patient_phones')){
			$PatientRole['Telecom'][] = $this->phone(
				'HP',
				$patientData['phone_home']
			);
			$PatientRole['Telecom'][] = $this->phone(
				'MC',
				$patientData['phone_mobile']
			);
			$PatientRole['Telecom'][] = $this->phone(
				'WP',
				$patientData['phone_work']
			);
		}

		$PatientRole['DirectAddress'] = $patientData['direct_address'];
		$PatientRole['Email'] = $patientData['email'];

		$religion = $this->CombosData->getValuesByListIdAndOptionValue(142, $patientData['religion']);
		if($religion){
			$PatientRole['Patient']['Religion'] = $this->code(
				$religion['option_value'],
				$religion['code_type'],
				$religion['option_name']
			);
		}else{
			$PatientRole['Religion'] = 'UNK';
		}

		$PatientRole['Patient']['Id'] = $patientData['pid'];
		$PatientRole['Patient']['RecordNumber'] = isset($patientData['pubpid']) && $patientData['pubpid'] != '' ?
			$patientData['pubpid'] : $patientData['id'];

		$PatientRole['Patient']['AccountNumber'] = isset($patientData['pubaccount']) && $patientData['pubaccount'] != '' ?
			$patientData['pubaccount'] : $PatientRole['Patient']['RecordNumber'];


		if(!$this->isExcluded('patient_name')){
			$PatientRole['Patient']['Name'] = $this->name(
				$patientData['title'],
				$patientData['fname'],
				$patientData['mname'],
				$patientData['lname']
			);
		}

		if(!$this->isExcluded('patient_sex')){
			$sex = $this->CombosData->getValuesByListIdAndOptionValue(95, $patientData['sex']);

			if($sex){
				$PatientRole['Patient']['AdministrativeGenderCode'] = $this->code(
					$sex['option_value'],
					'AdministrativeGender',
					$sex['option_name']
				);
			}else{
				$PatientRole['Patient']['AdministrativeGenderCode'] = 'UNK';
			}

			$PatientRole['Patient']['SexualOrientation'] = $this->code(
				$patientData['orientation'],
				'SNOMEDCT'
			);
			$PatientRole['Patient']['GenderIdentity'] = $this->code(
				$patientData['identity'],
				'SNOMEDCT'
			);
		}

		if(!$this->isExcluded('patient_dob')){
			$PatientRole['Patient']['DateOfBirth'] = str_replace([' ', '-'], '', substr($patientData['DOB'],0,10));
			$PatientRole['Patient']['BirthTime'] = str_replace([' ', ':'], '', substr($patientData['DOB'],11,14));
		}

		if(!$this->isExcluded('patient_race')){
			$races = json_decode(file_get_contents(ROOT. '/resources/code_sets/HL7v3-Race.json'), true);
			$race_key = array_search($patientData['race'], array_column($races, 'code'));

			$PatientRole['Patient']['RaceCode'] = $this->code(
				$races[$race_key]['code'],
				$races[$race_key]['code_type'],
				$races[$race_key]['code_description']
			);

			$race_key = array_search($patientData['race'], array_column($races, 'code'));
			$PatientRole['Patient']['SecondaryRaceCode'] = $this->code(
				$races[$race_key]['code'],
				$races[$race_key]['code_type'],
				$races[$race_key]['code_description']
			);

			// TODO
			$PatientRole['Patient']['SecondaryRaceCode'] = [];
		}

		if(!$this->isExcluded('patient_ethnicity')){
			$ethnicity = $this->CombosData->getValuesByListIdAndOptionValue(14, $patientData['ethnicity']);
			$code = $patientData['ethnicity'] == 'H' ? '2135-2' : '2186-5';
			$codeName = 'Race & Ethnicity - CDC';

			$PatientRole['Patient']['EthnicGroupCode'] = $this->code(
				$code,
				$codeName
			);

			// TODO
			$PatientRole['Patient']['SecondaryEthnicGroupCode'] = [];
		}

		if(!$this->isExcluded('patient_preferred_language')){
			$PatientRole['Patient']['LanguageCommunication'] = [];
			$PatientRole['Patient']['LanguageCommunication']['LanguageCode'] = $patientData['language'];
			$PatientRole['Patient']['LanguageCommunication']['ModeCode'] = $this->code(
				'ESP',
				'LanguageAbilityMode',
				'Expressed spoken'
			);
			$PatientRole['Patient']['LanguageCommunication']['ProficiencyLevelCode'] = $this->code(
				'G',
				'LanguageAbilityProficiency',
				'Good'
			);
		}

		if(!$this->isExcluded('patient_marital_status')){
			$PatientRole['Patient']['MaritalStatusCode'] = $this->code(
				$patientData['marital_status'],
				'MaritalStatusCode'
			);
		}

		$PatientRole['Patient']['PostalAddress'] = $this->address(
			'H',
			$patientData['postal_address'],
			$patientData['postal_address_cont'],
			$patientData['postal_city'],
			$patientData['postal_state'],
			$patientData['postal_zip'],
			$patientData['postal_country']

		);

		$PatientRole['Patient']['PhysicalAddress'] = $this->address(
			'H',
			$patientData['physical_address'],
			$patientData['physical_address_cont'],
			$patientData['physical_city'],
			$patientData['physical_state'],
			$patientData['physical_zip'],
			$patientData['physical_country']
		);

		$RecordTarget['PatientRole'] = $PatientRole;

		$this->patient_record['RecordTarget'] = $RecordTarget;
	}

	/**
	 *
	 */
	private function resetHeader(){
		$this->patient_record['RecordTarget'] = [];
		$this->patient_record['Author'] = [];
		$this->patient_record['DataEnterer'] = [];
		$this->patient_record['Informant'] = [];
		$this->patient_record['Custodian'] = [];
		$this->patient_record['InformationRecipient'] = [];
		$this->patient_record['LegalAuthenticator'] = [];
		$this->patient_record['Authenticator'] = [];
//		$this->patient_record['DocumentationOf'] = [];
//		$this->patient_record['ComponentOf'] = [];
	}

	public function getAuthor($uid){
		return $this->patient_record['Authors']['Author'] = $this->performer($uid);
	}

	public function getAssignedAuthor($uid){
		return $this->patient_record['Authors']['AssignedAuthor'] = $this->performer($uid);
	}

	public function getDataEnterer($uid){
		return $this->patient_record['Authors']['DataEnterer'] = $this->performer($uid);
	}

	public function getPerformer($uid){
		return $this->patient_record['Authors']['Performer'] = $this->performer($uid);
	}

	public function getInformant($uid){
		return $this->patient_record['Authors']['Informant'] = $this->performer($uid);
	}

	public function getParticipant($uid){
		return $this->patient_record['Authors']['Participant'] = $this->performer($uid);
	}

	public function getInformantProvider($uid){
		return $this->patient_record['Authors']['InformantProvider'] = $this->performer($uid);
	}

	public function getInformantRelation($uid){
		return $this->patient_record['Authors']['InformantRelation'] = $this->performer($uid);
	}

	public function getCustodian($uid){
		return $this->patient_record['Authors']['Custodian'] = $this->performer($uid);
	}

	public function getInformationRecipient($uid){
		return $this->patient_record['Authors']['InformationRecipient'] = $this->performer($uid);
	}

	public function getLegalAuthenticator($uid){
		return $this->patient_record['Authors']['LegalAuthenticator'] = $this->performer($uid);
	}

	public function getAuthenticator($uid){
		return $this->patient_record['Authors']['Authenticator'] = $this->performer($uid);
	}

	/**
	 * @param $result
	 * @param $soap
	 *
	 * @return array
	 */
	private function encounter($result, $soap){
		$encounter = [];
		$encounter['Id'] = $result['eid'];
		$encounter['ChiefComplaint'] = $result['brief_description'];
		$encounter['ServiceDates'] = $this->dates(
			$result['service_date'],
			$result['service_date']
		);

		$encounter['Provider'] = $this->performer($result['provider_uid']);
		$encounter['Technician'] = $this->performer($result['technician_uid']);
		$encounter['Supervisor'] = $this->performer($result['supervisor_uid'], 'NA');
		$encounter['ReferredBy'] = $this->externalPerformer($result['referring_physician'], 'NA');
		$encounter['ReferTo'] = [];

		$encounter['Assessment'] = isset($soap['assessment']) ? $soap['assessment'] : 'UNK';
		$encounter['Instructions'] = isset($soap['instructions']) ? $soap['instructions'] : 'UNK';

		$encounter['Diagnosis'] = [];
		foreach($soap['dxCodes'] as $dx){
			$encounter['Diagnosis'][] = [
				'Code' => $this->code($dx['code'], $dx['code_type'], $dx['code_text']),
				'Priority' => $dx['code'],
				'Status' => $dx['dx_type']
			];
		}

		$encounter['Specialty'] = '';
		$encounter['VisitCode'] = $this->code(
			$result['visit_category_code'],
			$result['visit_category_code_type'],
			$result['visit_category']
		);
		$encounter['Organization'] = $this->organization($result['facility']);

		return $encounter;
	}

	/**
	 * @param $user_id
	 * @param $nullFlavor
	 *
	 * @return array|string
	 */
	private function performer($user_id, $nullFlavor = 'UNK'){
		if(
			isset($this->buff['performers']) &&
			isset($this->buff['performers'][$user_id])
		){
			return $this->buff['performers'][$user_id];
		}

		$user = $this->User->getUserByUid($user_id);

		if($user === false){
			return $nullFlavor;
		}

		$performer = [];
		$performer['Id'] = $user['id'];
		$performer['NPI'] = $user['npi'];
		$performer['Name'] = $this->name(
			$user['title'],
			$user['fname'],
			$user['mname'],
			$user['lname']
		);
		$performer['DirectAddress'] = $user['direct_address'];
		$performer['Email'] = $user['email'];

		$performer['Telecom'] = $this->phone(
			'WP',
			$user['phone']
		);
		$performer['Address'] = $this->address(
			'WP',
			$user['street'],
			null,
			$user['city'],
			$user['state'],
			$user['postal_code'],
			$user['country_code']
		);
		$performer['Taxonomy'] = $this->code($user['taxonomy'], 'TAXONOMY');
		$performer['Organization'] = $this->organization($user['facility_id']);

		return $this->buff['performers'][$user_id] = $performer;
	}

	/**
	 * @param $referring_id
	 * @param $nullFlavor
	 *
	 * @return array|string
	 */
	private function externalPerformer($referring_id, $nullFlavor = 'UNK'){
		if(
			isset($this->buff['externalPerformers']) &&
			isset($this->buff['externalPerformers'][$referring_id])
		){
			return $this->buff['externalPerformers'][$referring_id];
		}

		$referring = $this->ReferringProviders->getReferringProviderById($referring_id);

		if($referring === false){
			return $nullFlavor;
		}

		$performer = [];
		$performer['Id'] = $referring['id'];
		$performer['NPI'] = $referring['npi'];

		if(isset($referring['lname']) && $referring['lname'] !== ''){
			$performer['Name'] = $this->name(
				$referring['title'],
				$referring['fname'],
				$referring['mname'],
				$referring['lname']
			);
		}else{
			$performer['Name'] = $referring['organization_name'];
		}

		$performer['DirectAddress'] = $referring['direct_address'];
		$performer['Email'] = $referring['email'];

		$performer['Telecom'] = $this->phone(
			'WP',
			$referring['phone']
		);

		$performer['Taxonomy'] = $this->code($referring['taxonomy'], 'TAXONOMY');

		if(isset($referring['facilities']) && is_array($referring['facilities']) && !empty($referring['facilities'])){
			$performer['Address'] = $this->address(
				'WP',
				$referring['facilities'][0]['address'],
				$referring['facilities'][0]['address_cont'],
				$referring['facilities'][0]['city'],
				$referring['facilities'][0]['state'],
				$referring['facilities'][0]['postal_code'],
				$referring['facilities'][0]['country']
			);

			if(isset($referring['facilities'][0]['facility_id']) && $referring['facilities'][0]['facility_id'] > 0){
				$performer['Organization'] = $this->organization($referring['facilities'][0]['facility_id']);
			}else{
				$performer['Organization'] = null;
			}

		}else{
			$performer['Address'] = null;
			$performer['Organization'] = null;
		}

		return $this->buff['externalPerformers'][$referring_id] = $performer;
	}

	/**
	 * @param $facility_id
	 *
	 * @return array|string
	 */
	private function organization($facility_id){
		if(
			isset($this->buff['organizations']) &&
			isset($this->buff['organizations'][$facility_id])
		){
			return $this->buff['organizations'][$facility_id];
		}

		$facility = $this->Facilities->getFacility($facility_id);

		if($facility === false){
			return 'UNK';
		}

		$performer = [];
		$performer['Id'] = $facility_id;
		$performer['NPI'] = $facility['npi'];
		$performer['Name'] = $facility['name'];
		$performer['Email'] = $facility['email'];
		$performer['Attention'] = $facility['attn'];
		$performer['Telecom'] = $this->phone(
			'WP',
			$facility['phone']
		);
		$performer['Address'] = $this->address(
			'WP',
			$facility['address'],
			$facility['address_cont'],
			$facility['city'],
			$facility['state'],
			$facility['postal_code'],
			$facility['country_code']
		);

		$performer['PlaceOfService'] = $this->code($facility['pos_code'], 'Place of Service Codes');

		return $this->buff['organizations'][$facility_id] = $performer;
	}

	/**
	 * @param $title
	 * @param $fname
	 * @param $mname
	 * @param $lname
	 *
	 * @return array
	 */
	private function name($title, $fname, $mname, $lname){
		$name = [];
		$name['Title'] = $title;
		$name['Given'] = $fname;
		$name['Middle'] = $mname;
		$name['Family'] = $lname;

		return $name;
	}

	/**
	 * @param $low
	 * @param $high
	 * @param $nullFlavor
	 *
	 * @return array
	 */
	private function dates($low, $high, $nullFlavor = null){
		$dates = [];
		if(isset($low)){
			$dates['Low'] = str_replace([' ','-',':'], '', $low);
		}else{
			$dates['Low'] = $nullFlavor;
		}
		if(isset($high)){
			$dates['High'] = str_replace([' ','-',':'], '', $high);;
		}else{
			$dates['High'] = $nullFlavor;
		}

		return $dates;
	}

	/**
	 * @param $use
	 * @param $number
	 * @param $nullFlavor
	 *
	 * @return array
	 */
	private function phone($use, $number, $nullFlavor = null){

		$phone = [];
		$phone['Use'] = $use;

		if(!isset($number) || $number === ''){
			$phone['Number'] = $nullFlavor;
		}else{
			$number = str_replace(['(', ')', '-', ' '], '', trim($number));
			$phone['Number'] = $number;
		}

		return $phone;
	}

	/**
	 * @param $use
	 * @param $line1
	 * @param $line2
	 * @param $city
	 * @param $state
	 * @param $zip
	 * @param $country
	 * @param $nullFlavor
	 *
	 * @return array|string
	 */
	private function address($use, $line1, $line2, $city, $state, $zip, $country, $nullFlavor = null){

		if(
			(!isset($use) || $use === '') &&
			(!isset($line1) || $line1 === '') &&
			(!isset($line2) || $line2 === '') &&
			(!isset($city) || $city === '') &&
			(!isset($state) || $state === '') &&
			(!isset($zip) || $zip === '')
		){
			return $nullFlavor;
		}
		$address = [];
		$address['Use'] = $use;
		$address['AddressOne'] = $line1 . ' ' . $line2;
		$address['AddressTwo'] = $line2;
		$address['City'] = $city;
		$address['State'] = $state;
		$address['PostalCode'] = $zip;
		$address['Country'] = $country;

		return $address;
	}

	/**
	 * @param      $code
	 * @param      $codeSystemName
	 * @param null $displayName
	 * @param null $nullFlavor
	 *
	 * @return array|string
	 */
	private function code($code, $codeSystemName, $displayName = null, $nullFlavor = null){

		if(!isset($code) || $code === ''){
			if(isset($displayName) && $displayName !== ''){
				return $displayName;
			}

			return $nullFlavor;
		}

		$buff = [];
		$buff['Code'] = $code;
		$buff['CodeSystemName'] = $codeSystemName;

		$codeSystem = $this->getCodeSystemByCodeSystemName($codeSystemName);
		if(isset($codeSystem)) $buff['CodeSystem'] = $codeSystem;
		if(isset($displayName)) $buff['DisplayName'] = $displayName;

		return $buff;
	}

	/**
	 * @param $date
	 *
	 * @return bool
	 */
	private function isActiveByDate($date){
		if(!isset($date)){
			return true;
		}
		if($date == '0000-00-00' || $date == '0000-00-00 00:00:00'){
			return true;
		}

		$date = strtotime($date);
		$now = time();

		return $date > $now;
	}

	/**
	 * @param $section
	 *
	 * @return bool
	 */
	private function isExcluded($section){
		return array_search($section, $this->excludes) !== false;
	}

	/**
	 * @return bool
	 */
	private function isMetric(){
		return $this->measuringUnits == 'metric';
	}

	/**
	 * @param $codeSystemName
	 *
	 * @return null|string
	 */
	private function getCodeSystemByCodeSystemName($codeSystemName){
		switch($codeSystemName){
			case 'CPT':
			case 'CPT4':
			case 'CPT-4':
				return '2.16.840.1.113883.6.12';
			case 'ICD9':
			case 'ICD-9':
				return '2.16.840.1.113883.6.42';
			case 'ICD0':
			case 'ICD10':
			case 'ICD-10':
			case 'ICD10-CM':
				return '2.16.840.1.113883.6.3';
			case 'LN':
			case 'LOINC':
				return '2.16.840.1.113883.6.1';
			case 'NDC':
				return '2.16.840.1.113883.6.6';
			case 'RXNORM':
				return '2.16.840.1.113883.6.88';
			case 'SNOMED':
			case 'SNOMEDCT':
			case 'SNOMED-CT':
				return '2.16.840.1.113883.6.96';
			case 'CVX':
				return '2.16.840.1.113883.12.292';
			case 'NPI':
				return '2.16.840.1.113883.4.6';
			case 'UNII':
				return '2.16.840.1.113883.4.9';
			case 'NCI':
				return '2.16.840.1.113883.3.26.1.1';
			case 'ActPriority':
				return '2.16.840.1.113883.1.11.16866';
			case 'TAXONOMY':
				return '2.16.840.1.114222.4.11.106';
			case 'CDCREC':
			case 'PH_RaceAndEthnicity_CDC':
				return '2.16.840.1.113883.6.238';


			case 'AdministrativeGender':
				return '2.16.840.1.113883.5.1';
			case 'MaritalStatusCode':
				return '2.16.840.1.113883.5.2';
			case 'LanguageAbilityProficiency':
				return '2.16.840.1.113883.5.61';
			case 'Race & Ethnicity - CDC':
				return '2.16.840.1.113883.6.238';
			case 'ObservationInterpretation':
				return '2.16.840.1.113883.5.83';
			case 'ActNoImmunizationReason':
			case 'HL7 ActNoImmunizationReason':
				return '2.16.840.1.113883.1.11.19717';
			default:
				return null;
		}
	}
}
