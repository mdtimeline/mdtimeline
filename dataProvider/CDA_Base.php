<?php

include_once(ROOT . '/classes/Array2XML.php');
include_once(ROOT . '/dataProvider/PatientRecord.php');
include_once(ROOT . '/dataProvider/CDA_Templates.php');
include_once(ROOT . '/dataProvider/CDA_Utilities.php');
/**
 * Created by IntelliJ IDEA.
 * User: ernesto
 * Date: 4/18/17
 * Time: 8:12 PM
 */
abstract class CDA_Base extends CDA_Utilities {

	/**
	 * @var DomDocument
	 */
	public $xml;

	/**
	 * @var array
	 */
	public $data = [];

	/**
	 * @var PatientRecord
	 */
	protected $PatientRecord;

	/**
	 * @var array
	 */
	protected $patientRecord = [];

	/**
	 * @var false|string
	 */
	protected $nowDate;

	/**
	 * @var false|string
	 */
	protected $nowTime;

	/**
	 * @var array
	 */
	protected $healthConcerns = [];

	/**
	 * @var array
	 */
	protected $sessions = [];

	/**
	 * @var array
	 */
	protected $excludes = [];

	/**
	 * @var CDA_Templates
	 */
	protected $CDA_Templates = [];

	/**
	 * CDA_Base constructor.
	 */
	protected function __construct(){

		$this->nowDate = date('Ymd');
		$this->nowTime = date('YmdHisO');
		$this->PatientRecord = new PatientRecord();
		$this->CDA_Templates = new CDA_Templates();
	}

	/**
	 * TransferSummaryV2
	 *
	 * @param $pid
	 * @param $eid
	 * @param $referring_id
	 */
	public function TS($pid, $eid, $referring_id){

		$code = [
			'code' => '34133-9',
			'codeSystem' => '2.16.840.1.113883.6.1',
			'codeSystemName' => 'LOINC',
			'displayName' => 'Summarization of Episode Note'
		];

		$templateIds = [
			['root' => '2.16.840.1.113883.10.20.22.1.1'],
			['root' => '2.16.840.1.113883.10.20.22.1.13']
		];

		$id = [
			'root' => 'MDTL',
			'extension' => '1912668293'
		];

		$this->setHeader($code, $templateIds, $id);
		$this->getPatientRecord($pid, $eid, $referring_id);

		$this->setSession('AssessmentAndPlanSection', 'R');
		$this->setSession('ReasonForReferralSection', 'R');
		$this->setSession('ProblemSection', 'R');
		$this->setSession('MedicationsSection', 'R');
		$this->setSession('AllergiesAndIntolerancesSection', 'R');

		$this->setSession('AdvanceDirectivesSection', 'O');
		$this->setSession('EncountersSection', 'O');
		$this->setSession('FamilyHistorySection', 'O');
		$this->setSession('FunctionalStatusSection', 'O');
		$this->setSession('ImmunizationsSection', 'O');
		//$this->setSession('MedicalEquipmentSection', 'O');
		//$this->setSession('PayersSection', 'O');
		$this->setSession('ProceduresSection', 'O');
		$this->setSession('ResultsSection', 'O');
		$this->setSession('SocialHistorySection ', 'O');
		$this->setSession('VitalSignsSection', 'O');

		$this->build();

	}

	/**
	 * ReferralNoteV2
	 *
	 * @param $pid
	 * @param $eid
	 * @param $referral_id
	 */
	public function RN($pid, $eid, $referral_id){

		$code = [
			'code' => '34133-9',
			'codeSystem' => '2.16.840.1.113883.6.1',
			'codeSystemName' => 'LOINC',
			'displayName' => 'Summarization of Episode Note'
		];

		$templateIds = [
			['root' => '2.16.840.1.113883.10.20.22.1.1'],
			['root' => '2.16.840.1.113883.10.20.22.1.14']
		];

		$id = [
			'root' => 'MDTL',
			'extension' => '1912668293'
		];

		$this->setHeader($code, $templateIds, $id);
		$this->getPatientRecord($pid, $eid, $referral_id);

		$this->setSession('AssessmentAndPlanSection', 'R');
		$this->setSession('ReasonForReferralSection', 'R');
		$this->setSession('ProblemSection', 'R');
		$this->setSession('MedicationsSection', 'R');
		$this->setSession('AllergiesAndIntolerancesSection', 'R');

		$this->setSession('AdvanceDirectivesSection', 'O');
		$this->setSession('EncountersSection', 'O');
		$this->setSession('FamilyHistorySection', 'O');
		$this->setSession('FunctionalStatusSection', 'O');
		$this->setSession('ImmunizationsSectionEntriesOptional', 'O');
		//$this->setSession('MedicalEquipmentSection', 'O');
		//$this->setSession('PayersSection', 'O');
		$this->setSession('ProceduresSection', 'O');
		$this->setSession('ResultsSection', 'O');
		$this->setSession('SocialHistorySection ', 'O');
		$this->setSession('VitalSignsSection', 'O');

		$this->build();

	}

	/**
	 * CarePlanV2
	 *
	 * @param $pid
	 */
	public function CP($pid){

		$code = [
			'code' => '52521-2',
			'codeSystem' => '2.16.840.1.113883.6.1',
			'codeSystemName' => 'LOINC',
			'displayName' => 'Overall Plan of Care/Advance Care Directives'
		];

		$templateIds = [
			['root' => '2.16.840.1.113883.10.20.22.1.1'],
			['root' => '2.16.840.1.113883.10.20.22.1.15']
		];

		$id = [
			'root' => 'MDTL',
			'extension' => '1912668293'
		];

		$this->setHeader($code, $templateIds, $id);
		$this->getPatientRecord($pid);

		/**
		 * Required
		 */
		$this->setSession('HealthConcernsSection', 'R');
		$this->setSession('GoalsSection', 'R');

		/**
		 * Optional
		 */
		$this->setSession('InterventionsSection', 'O');
		$this->setSession('HealthStatusEvaluationsAndOutcomesSection', 'O');

		$this->build();
	}

	/**
	 * ContinuityofCareDocumentCCDV2
	 *
	 * @param $pid
	 */
	public function CCD($pid){

		$code = [
			'code' => '52521-2',
			'codeSystem' => '2.16.840.1.113883.6.1',
			'codeSystemName' => 'LOINC',
			'displayName' => 'Overall Plan of Care/Advance Care Directives'
		];

		$templateIds = [
			['root' => '2.16.840.1.113883.10.20.22.1.2']
		];

		$id = [
			'root' => 'MDTL',
			'extension' => '1912668293'
		];

		$this->setHeader($code, $templateIds, $id);
		$this->getPatientRecord($pid);

		/**
		 * Required
		 */
		$this->setSession('AllergiesAndIntolerancesSection', 'R');
		$this->setSession('MedicationsSection', 'R');
		$this->setSession('ProblemSection', 'R');
		$this->setSession('ProceduresSection', 'R');
		$this->setSession('ResultsSection', 'R');
		$this->setSession('VitalSignsSection', 'R');
		$this->setSession('SocialHistorySection', 'R');

		/**
		 * Optional
		 */
		$this->setSession('AdvanceDirectivesSection', 'O');
		$this->setSession('EncountersSection', 'O');
		$this->setSession('FamilyHistorySection', 'O');
		$this->setSession('FunctionalStatusSection', 'O');
		$this->setSession('ImmunizationsSection', 'O');
		//$this->setSession('MedicalEquipmentSection', 'O');
		//$this->setSession('PayersSection', 'O');
		$this->setSession('AssessmentAndPlanSection', 'O');
		$this->setSession('MentalStatusSection', 'O');
		$this->setSession('NutritionSection', 'O');

		$this->build();
	}

	/**
	 * @param $session
	 * @param $option
	 */
	protected function setSession($session, $option){
		$this->sessions[$session] = $option;
	}

	/**
	 * @param $session
	 */
	protected function addExclude($session){
		$this->excludes[] = $session;
	}

	/**
	 * @param $session
	 *
	 * @return bool
	 */
	protected function isExcluded($session){
		return array_search($session,$this->excludes) !== false;
	}

	/**
	 * @param $session
	 *
	 * @return bool
	 */
	protected function hasPatientRecordData($session){
		return isset($this->patientRecord[$session]) && !empty($this->patientRecord[$session]);
	}

	/**
	 * @param $session
	 */
	protected function addComponentSession($session){
		$this->data['component']['structuredBody']['component'][] = $session;
	}

	/**
	 * @param $session
	 * @param $option
	 *
	 * @return array|bool
	 */
	protected function getSessionAttributes($session, $option){

		switch($session){
			case 'AllergiesAndIntolerancesSection':
				return [
					'title' => 'Allergies and Intolerances',
					'text' => ' No Allergies and Intolerances',
					'root' => $option == 'R' ? '2.16.840.1.113883.10.20.22.2.6.1' : '2.16.840.1.113883.10.20.22.2.6',
					'extension' => '2014-06-09',
					'code' => '48765-2',
					'codeSystem' => '2.16.840.1.113883.6.1'
				];
			case 'MedicationsSection':
				return [
					'title' => 'Medications',
					'text' => ' No Medications',
					'root' => $option == 'R' ? '2.16.840.1.113883.10.20.22.2.1.1' : '2.16.840.1.113883.10.20.22.2.1',
					'extension' => '2014-06-09',
					'code' => '10160-0',
					'codeSystem' => '2.16.840.1.113883.6.1'
				];
			case 'ProceduresSection':
				return [
					'title' => 'Procedures',
					'text' => ' No Procedures',
					'root' => $option == 'R' ? '2.16.840.1.113883.10.20.22.2.7.1' : '2.16.840.1.113883.10.20.22.2.7',
					'extension' => '2014-06-09',
					'code' => '47519-4',
					'codeSystem' => '2.16.840.1.113883.6.1'
				];
			case 'ProblemSection':
				return [
					'title' => 'Plan of Treatment',
					'text' => ' No Plan of Treatment',
					'root' => $option == 'R' ? '2.16.840.1.113883.10.20.22.2.5.1' : '2.16.840.1.113883.10.20.22.2.5',
					'extension' => '2015-08-01',
					'code' => '18776-5',
					'codeSystem' => '2.16.840.1.113883.6.1'
				];
			case 'AssessmentAndPlanSection':
				return [
					'title' => 'Assessment and Plan',
					'text' => ' No Assessment and Plan',
					'root' => '2.16.840.1.113883.10.20.22.2.9',
					'extension' => '2014-06-09',
					'code' => '51847-2',
					'codeSystem' => '2.16.840.1.113883.6.1'
				];
			case 'ResultsSection':
				return [
					'title' => 'Results',
					'text' => ' No Results',
					'root' => $option == 'R' ? '2.16.840.1.113883.10.20.22.2.3.1' : '2.16.840.1.113883.10.20.22.2.3',
					'extension' => '2015-08-01',
					'code' => '30954-2',
					'codeSystem' => '2.16.840.1.113883.6.1'
				];
			case 'VitalSignsSection':
				return [
					'title' => 'Vital Signs',
					'text' => ' No Vital Signs',
					'root' => $option == 'R' ? '2.16.840.1.113883.10.20.22.2.4.1' : '2.16.840.1.113883.10.20.22.2.4',
					'extension' => '2015-08-01',
					'code' => '8716-3',
					'codeSystem' => '2.16.840.1.113883.6.1'
				];
			case 'SocialHistorySection':
				return [
					'title' => 'Social History',
					'text' => ' No Social History',
					'root' => '2.16.840.1.113883.10.20.22.2.17',
					'extension' => '2015-08-01',
					'code' => '29762-2',
					'codeSystem' => '2.16.840.1.113883.6.1'
				];
			case 'AdvanceDirectivesSection':
				return [
					'title' => 'Advance Directives',
					'text' => ' No Advance Directives',
					'root' => $option == 'R' ? '2.16.840.1.113883.10.20.22.2.21.1' : '2.16.840.1.113883.10.20.22.2.21',
					'extension' => '2015-08-01',
					'code' => '42348-3',
					'codeSystem' => '2.16.840.1.113883.6.1'
				];
			case 'EncountersSection':
				return [
					'title' => 'Encounters',
					'text' => ' No Encounters',
					'root' => $option == 'R' ? '2.16.840.1.113883.10.20.22.2.22.1' : '2.16.840.1.113883.10.20.22.2.22',
					'extension' => '2015-08-01',
					'code' => '46240-8',
					'codeSystem' => '2.16.840.1.113883.6.1'
				];
			case 'FamilyHistorySection':
				return [
					'title' => 'Family History',
					'text' => ' No Family History',
					'root' => '2.16.840.1.113883.10.20.22.2.15',
					'extension' => '2015-08-01',
					'code' => '10157-6',
					'codeSystem' => '2.16.840.1.113883.6.1'
				];
			case 'FunctionalStatusSection':
				return [
					'title' => 'Functional Status',
					'text' => ' No Functional Status',
					'root' => '2.16.840.1.113883.10.20.22.2.14',
					'extension' => '2014-06-09',
					'code' => '47420-5',
					'codeSystem' => '2.16.840.1.113883.6.1'
				];
			case 'ImmunizationsSection':
				return [
					'title' => 'Immunizations',
					'text' => 'No Immunizations',
					'root' => $option == 'R' ? '2.16.840.1.113883.10.20.22.2.2.1' : '2.16.840.1.113883.10.20.22.2.2',
					'extension' => '2015-08-01',
					'code' => '11369-6',
					'codeSystem' => '2.16.840.1.113883.6.1'
				];
			case 'MentalStatusSection':
				return [
					'title' => 'Mental Status',
					'text' => 'No Mental Status',
					'root' => '2.16.840.1.113883.10.20.22.2.56',
					'extension' => '2015-08-01',
					'code' => '10190-7',
					'codeSystem' => '2.16.840.1.113883.6.1'
				];
			case 'NutritionSection':
				return [
					'title' => 'Nutrition',
					'text' => 'No Nutrition',
					'root' => '2.16.840.1.113883.10.20.22.2.57',
					'extension' => null,
					'code' => '61144-2',
					'codeSystem' => '2.16.840.1.113883.6.1'
				];
			case 'MedicationsAdministeredSection':
				return [
					'title' => 'Medications Administered',
					'text' => 'No Medications Administered',
					'root' => '2.16.840.1.113883.10.20.22.2.38',
					'extension' => '2014-06-09',
					'code' => '29549-3',
					'codeSystem' => '2.16.840.1.113883.6.1'
				];
			case 'HealthConcernsSection':
				return [
					'title' => 'Health Concerns',
					'text' => 'No Health Concerns',
					'root' => '2.16.840.1.113883.10.20.22.2.58',
					'extension' => '2015-08-01',
					'code' => '75310-3',
					'codeSystem' => '2.16.840.1.113883.6.1'
				];
			case 'ReasonForReferralSection':
				return [
					'title' => 'Reason for Referral',
					'text' => 'No Reason for Referral',
					'root' => '1.3.6.1.4.1.19376.1.5.3.1.3.1',
					'extension' => '2014-06-09',
					'code' => '42349-1',
					'codeSystem' => '2.16.840.1.113883.6.1'
				];
			default:
				return false;
		}

	}

	/**
	 * @param $section
	 * @param $option
	 *
	 * @return array|bool
	 */
	protected function initSection($section, $option){

		$isExcluded = $this->isExcluded($section);
		if($isExcluded && $option == 'O'){
			return false;
		}

		$isNull = $option == 'R' && ($isExcluded || !$this->hasPatientRecordData($section));

		if($isNull){
			$buff = $this->nullFlavor('NI');
		}

		$attr = $this->getSessionAttributes($section, $option);
		$buff['templateId']['@attributes']['root'] = $attr['root'];
		if(isset($attr['extension'])){
			$buff['templateId']['@attributes']['extension'] = $attr['extension'];
		}
		$buff['code']['@attributes']['code'] = $attr['code'];
		$buff['code']['@attributes']['codeSystem'] = $attr['codeSystem'];
		$buff['title'] = $attr['title'];
		$buff['text'] = $attr['text'];

		if($isNull){
			$this->addComponentSession(['section' => $buff]);
			return false;
		}

		return $buff;
	}

	/**
	 * @param $section
	 *
	 * @return bool|mixed
	 */
	protected function getSectionData($section){

		if(isset($this->patientRecord[$section]) && !empty($this->patientRecord[$section])){
			return $this->patientRecord[$section];
		}else{
			return false;
		}
	}

	/**
	 *
	 */
	protected function build(){

		$this->data['recordTarget'] = [];
		$this->data['author'] = [];
		$this->data['dataEnterer'] = [];
		$this->data['informant'] = [];
		$this->data['custodian'] = [];
		$this->data['informationRecipient'] = [];
		$this->data['legalAuthenticator'] = [];
		$this->data['authenticator'] = [];
		$this->data['participant'] = [];
		$this->data['documentationOf'] = [];
		$this->data['relatedDocument'] = [];
		$this->data['componentOf'] = [];
		$this->data['component']['structuredBody'] = [];

		foreach($this->sessions as $session => $option){

			// check is method exist
			$method = 'set' . $session;
			if(!method_exists($this, $method)) continue;

			// run session method
			$this->{$method}($option);
		}
	}

	/**
	 * @param       $pid
	 * @param null  $eid
	 * @param null  $referral_id
	 * @param null  $start_date
	 * @param null  $end_date
	 * @param array $excludes
	 */
	protected function getPatientRecord($pid, $eid = null, $referral_id = null, $start_date = null, $end_date = null, $excludes = []){
		$this->patientRecord = $this->PatientRecord->getRecord($pid, $eid, $referral_id, $start_date, $end_date, $excludes);
	}

	/**
	 * @param        $code
	 * @param        $templateIds
	 * @param        $id
	 * @param        $version
	 *
	 * @internal param string $title
	 */
	protected function setHeader($code, $templateIds, $id, $version = 1){

		$this->data['@attributes'] = [
			'xmlns' => 'urn:hl7-org:v3',
			'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
			'xsi:schemaLocation' => 'urn:hl7-org:v3 CDA.xsd'
		];

		$this->data['realmCode']['@attributes']['code'] = 'US';

		$this->data['typeId']['@attributes']['root'] = '2.16.840.1.113883.1.3';
		$this->data['typeId']['@attributes']['extension'] = 'POCD_HD000040';

		foreach($templateIds as $templateId){
			$buff = [];
			$buff['@attributes']['root'] = $templateId['root'];
			if(isset($templateId['extension'])){
				$buff['@attributes']['extension'] = $templateId['extension'];
			}
			$this->data['templateId'][] = $buff;
		}

		$this->data['id']['@attributes']['root'] = $id['root'];
		if(isset($id['extension'])){
			$this->data['id']['@attributes']['extension'] = $id['extension'];
		}

		$this->data['code']['@attributes']['code'] = $code['code'];
		$this->data['code']['@attributes']['displayName'] = $code['displayName'];
		$this->data['code']['@attributes']['codeSystem'] = $code['codeSystem'];
		$this->data['code']['@attributes']['codeSystemName'] = $code['codeSystemName'];

		$this->data['title'] = $code['displayName'];

		$this->data['effectiveTime']['@attributes']['value'] = $this->nowTime;

		$this->data['confidentialityCode']['@attributes']['code'] = 'N';
		$this->data['confidentialityCode']['@attributes']['codeSystem'] = '2.16.840.1.113883.5.25';

		$this->data['languageCode']['@attributes']['code'] = 'US';
		$this->data['versionNumber']['@attributes']['value'] = $version;
	}

	/**
	 *
	 */
	protected function createXml(){
		Array2XML::init('1.0', 'UTF-8', true);
		$this->xml = Array2XML::createXML('ClinicalDocument', $this->data);
	}

	/**
	 * @return string
	 */
	public function get(){

		if(!isset($this->xml)){
			$this->createXml();
		}
		return $this->xml->saveXML();
	}

	abstract protected function setAssessmentAndPlanSection($option);
	abstract protected function setProblemSection($option);
	abstract protected function setProceduresSection($option);
	abstract protected function setMedicationsSection($option);
	abstract protected function setAllergiesAndIntolerancesSection($option);
	abstract protected function setResultsSection($option);
	abstract protected function setVitalSignsSection($option);
	abstract protected function setSocialHistorySection($option);
	abstract protected function setAdvanceDirectivesSection($option);
	abstract protected function setEncountersSection($option);
	abstract protected function setFamilyHistorySection($option);
	abstract protected function setFunctionalStatusSection($option);
	abstract protected function setImmunizationsSection($option);
	abstract protected function setMentalStatusSection($option);
	abstract protected function setNutritionSection($option);

}