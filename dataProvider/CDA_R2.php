<?php

include_once(ROOT . '/dataProvider/CDA_Base.php');

/**
 * Created by IntelliJ IDEA.
 * User: ernesto
 * Date: 4/18/17
 * Time: 8:12 PM
 */
class CDA_R2 extends CDA_Base {

	public function __construct(){
		parent::__construct();
	}

	protected function setProblemSection($option){
		$section = $this->initSection('ProblemSection', $option);
		if($section === false) return;

		$data = $this->getSectionData('ProblemSection');
		if($data === false) return;

		if(!isset($data['Problem'])) return;

		$columns = [
			'Condition',
			'Effective Dates',
			'Condition Status'
		];
		$rows = [];

		foreach($data['Problem'] as $i =>  $problem){
			$reference = $i + 1;
			$row = [];

			$row['Condition' . $reference] = $this->codeToText($problem['Code']);
			$row[] = $this->dateToText($problem['Dates']);
			$row[] = $problem['Status']['DisplayName'];
			$rows['Problem' . $reference] = $row;

			$active = $this->isActiveByDate($problem['Dates']['High']);

			$entry = [];
			$entry['@attributes']['typeCode'] = $active ? 'DRIV' : 'COMP';
			$entry['act'] = $this->CDA_Templates->ProblemConcernActCondition($problem, $reference);
			$section['entry'][] = $entry;
		}

		$section['text'] = [];
		$section['text']['table'] = $this->createTable($columns, $rows);

//		$this->addComponentSession(['section' => $section]);
	}

	protected function setProceduresSection($option){
		$section = $this->initSection('ProceduresSection', $option);
		if($section === false) return;

		$data = $this->getSectionData('ProceduresSection');
		if($data === false) return;

		if(!isset($data['Procedure'])) return;

		$columns = [
			'Procedure',
			'Date'
		];
		$rows = [];

		foreach($data['Procedure'] as $i =>  $procedure){
			$reference = $i + 1;
			$row = [];

			$row['Procedure' . $reference] = $this->codeToText($procedure['Procedure']);
			$row[] = $this->dateToText($procedure['Date']['Low']);
			$rows[] = $row;

			$active = $this->isActiveByDate($procedure['Date']['High']);

			$entry = [];
			$entry['@attributes']['typeCode'] = $active ? 'DRIV' : 'COMP';
			$entry['procedure'] = $this->CDA_Templates->ProcedureActivityProcedure($procedure, $reference);
			$section['entry'][] = $entry;
		}

		$section['text'] = [];
		$section['text']['table'] = $this->createTable($columns, $rows);

//		$this->addComponentSession(['section' => $section]);
	}

	protected function setMedicationsSection($option){
		$section = $this->initSection('MedicationsSection', $option);
		if($section === false) return;

		$data = $this->getSectionData('MedicationsSection');
		if($data === false) return;

		if(!isset($data['Medication'])) return;

		$columns = [
			'Medication',
			'Directions',
			'Start Date'
		];
		$rows = [];

		foreach($data['Medication'] as $i =>  $medication){
			$reference = $i + 1;
			$row = [];

			$row['Medication' . $reference] = $medication['Medication'];
			$row[] = $medication['Instructions'];
			$row[] = $this->dateToText($medication['Dates']);
			$rows[] = $row;

			$active = $this->isActiveByDate($medication['Dates']['High']);

			$entry = [];
			$entry['@attributes']['typeCode'] = $active ? 'DRIV' : 'COMP';
			$entry['substanceAdministration'] = $this->CDA_Templates->MedicationActivity($medication, $reference);
			$section['entry'][] = $entry;
		}

		$section['text'] = [];
		$section['text']['table'] = $this->createTable($columns, $rows);

		//$this->addComponentSession(['section' => $section]);
	}

	protected function setAllergiesAndIntolerancesSection($option){
		$section = $this->initSection('AllergiesAndIntolerancesSection', $option);
		if($section === false) return;

		$data = $this->getSectionData('AllergiesAndIntolerancesSection');
		if($data === false) return;

		if(!isset($data['AllergyAndIntolerance'])) return;

		$columns = [
			'Substance',
			'Reaction',
			'Severity',
			'Status'
		];
		$rows = [];

		foreach($data['AllergyAndIntolerance'] as $i =>  $allergy){
			$reference = $i + 1;
			$row = [];
			$row['Product' . $reference] = $allergy['Substance']['DisplayName'];
			$row['Reaction' . $reference] = $allergy['Reaction']['DisplayName'];
			$row['Severity' . $reference] = $allergy['Severity']['DisplayName'];
			$row[] = $allergy['Status']['DisplayName'];
			$rows[] = $row;

			$active = $allergy['Status']['Code'] == '55561003';

			$entry = [];
			$entry['@attributes']['typeCode'] = $active ? 'DRIV' : 'COMP';
			$entry['act'] = $this->CDA_Templates->AllergyConcernAct($allergy, $reference);
			$section['entry'][] = $entry;
		}

		$section['text'] = [];
		$section['text']['table'] = $this->createTable($columns, $rows);

		//$this->addComponentSession(['section' => $section]);
	}

	protected function setResultsSection($option){
		$section = $this->initSection('ResultsSection', $option);
		if($section === false) return;

		$section = $this->initSection('ResultsSection', $option);
		if($section === false) return;

		$data = $this->getSectionData('ResultsSection');
		if($data === false) return;

		if(!isset($data['Results'])) return;

		$columns = [
			'Result Type',
			'Result Value',
			'Relevant Reference Range',
			'Interpretation',
			'Date'
		];
		$tables = [];

		foreach($data['Results'] as $i =>  $result){

			$i_reference = $i + 1;
			$rows = [];

			foreach($result['Observations'] as $j => $observation){
				$reference = $i_reference . ($j + 1);

				$row = [];
				$row['Result' . $reference] = $this->codeToText($observation['Observation']);
				$row['ResultValue' . $reference] = isset($observation['Value']) ? $observation['Value'] : '' ;
				$row['ReferenceRange' . $reference] =  $this->referenceRangeToText($observation['ReferenceRange']);

				if($observation['InterpretationCode'] != 'UNK'){
					$row[] = $observation['InterpretationCode']['Code'];
				}else{
					$row[] = 'UNK';
				}

				$row[] = $this->dateToText($observation['Dates']);
				$rows[] = $row;
			}

			$title = 'Order: ' . $this->codeToText($result['Order']);
			$tables[] = $this->createTable($columns, $rows, $title);


			$entry = [];
			$entry['@attributes']['typeCode'] = 'DRIV';
			$entry['organizer'] = $this->CDA_Templates->ResultOrganizer($result, $i_reference);
			$section['entry'][] = $entry;
		}

		$section['text'] = [];
		$section['text']['table'] = $tables;

//		$this->addComponentSession(['section' => $section]);
	}

	protected function setVitalSignsSection($option){
		$section = $this->initSection('VitalSignsSection', $option);
		if($section === false) return;

		$section = $this->initSection('VitalSignsSection', $option);
		if($section === false) return;

		$data = $this->getSectionData('VitalSignsSection');
		if($data === false) return;

		if(!isset($data['VitalSigns'])) return;

		$columns = [
			'DT' => 'Date / Time: ',
			'HT' => 'Height',
			'WT' => 'Weight',
			'BP' => 'Blood Pressure',
			'HR' => 'Heart Rate',
			'O2' => 'O2 Percentage BldC Oximetry',
			'BT' => 'Body Temperature',
			'RR' => 'Respiratory Rate',
			'BM' => 'BMI',
		];

		$rows = [];

		foreach($data['VitalSigns'] as $i =>  $vitals){
			$i_reference = $i + 1;

			$row['DT'] = $this->dateToText($vitals['Dates']['Low'], 'F j, Y G:ia');

			$BP = '/';
			$BP_UNIT = 'mm[Hg]';

			foreach($vitals['Observations'] as $j => $observation){
				$reference = $i_reference . ($j + 1);

				if($observation['Alias'] == 'BPS'){
					$BP = $observation['Value']['Value'] . $BP;
				}else if($observation['Alias'] == 'BPD'){
					$BP = $BP . $observation['Value']['Value'];
				}else{
					$row[$observation['Alias']] = $observation['Value']['Value'];
					if($observation['Value']['Value'] != ''){
						$row[$observation['Alias']] .= ' ' . $observation['Value']['Unit'];
					}
				}
			}

			$row['BP'] = $BP;
			if($row['BP'] != '/'){
				$row['BP'] .= ' ' . $BP_UNIT;
			}

			$rows[] = $row;

			$entry = [];
			$entry['@attributes']['typeCode'] = 'DRIV';
			$entry['act'] = $this->CDA_Templates->VitalSignsOrganizer($vitals, $i_reference);
			$section['entry'][] = $entry;
		}
		$section['text'] = [];
		$section['text']['table'] = $this->createTable($columns, $rows, null, true);

		//$this->addComponentSession(['section' => $section]);
	}

	protected function setImmunizationsSection($option){
		$section = $this->initSection('ImmunizationsSection', $option);
		if($section === false) return;

		$data = $this->getSectionData('ImmunizationsSection');
		if($data === false) return;

		if(!isset($data['Immunization'])) return;

		$columns = [
			'Vaccine',
			'Date',
			'Status'
		];
		$rows = [];

		foreach($data['Immunization'] as $i =>  $immunization){
			$reference = $i + 1;
			$row = [];
			$row['Immunization' . $reference] = $this->codeToText($immunization['Vaccine']);
			$row['Date' . $reference] = $this->dateToText($immunization['Administration']['Dates']['Low']);
			$row['Status' . $reference] = $immunization['Status'];
			$rows[] = $row;

			$active = $immunization['RefusalReason'] != 'NI' || $immunization['Status'] == 'Cancelled';

			$entry = [];
			$entry['@attributes']['typeCode'] = $active ? 'DRIV' : 'COMP';
			$entry['substanceAdministration'] = $this->CDA_Templates->ImmunizationActivity($immunization, $reference);
			$section['entry'][] = $entry;
		}

		$section['text'] = [];
		$section['text']['table'] = $this->createTable($columns, $rows);

		//$this->addComponentSession(['section' => $section]);
	}

	protected function setSocialHistorySection($option){
		$section = $this->initSection('SocialHistorySection', $option);
		if($section === false) return;

		$data = $this->getSectionData('SocialHistorySection');
		if($data === false) return;

		if(!isset($data['SocialHistory'])) return;

		$columns = [
			'Social History Observation',
			'Description',
			'Dates Observed'
		];
		$rows = [];

		foreach($data['SocialHistory'] as $i =>  $history){
			$reference = $i + 1;
			$row = [];

			$row['Observation' . $reference] = $this->codeToText($history['Category']);
			if(is_array($history['Observation'])){
				$row['Description' . $reference] = $this->codeToText($history['Observation']);
			}else{
				$row['Description' . $reference] = $history['Observation'];
			}
			$row['DatesObserved' . $reference] = $this->dateToText($history['Dates']);
			$rows[] = $row;

			$entry = [];
			$entry['@attributes']['typeCode'] = 'DRIV';
			$entry['observation'] = $this->CDA_Templates->SocialHistoryObservation($history, $reference);
			$section['entry'][] = $entry;
		}

		$section['text'] = [];
		$section['text']['table'] = $this->createTable($columns, $rows);

		//$this->addComponentSession(['section' => $section]);
	}

	// TODO
	protected function setAdvanceDirectivesSection($option){
		$section = $this->initSection('AdvanceDirectivesSection', $option);
		if($section === false) return;

		$data = $this->getSectionData('AdvanceDirectivesSection');
		if($data === false) return;

		if(!isset($data['AdvanceDirective'])) return;

		$columns = [
			'Social History Observation',
			'Description',
			'Dates Observed'
		];
		$rows = [];

		foreach($data['AdvanceDirective'] as $i =>  $history){
			$reference = $i + 1;
			$row = [];

			// TODO

			$rows[] = $row;

			$entry = [];
			$entry['@attributes']['typeCode'] = 'DRIV';
			$entry['organizer'] = $this->CDA_Templates->AdvanceDirectiveOrganizer($history, $reference);
			$section['entry'][] = $entry;
		}

		$section['text'] = [];
		$section['text']['table'] = $this->createTable($columns, $rows);

		//$this->addComponentSession(['section' => $section]);
	}

	protected function setEncountersSection($option){
		$section = $this->initSection('EncountersSection', $option);
		if($section === false) return;

		$data = $this->getSectionData('EncountersSection');
		if($data === false) return;

		if(!isset($data['Encounter'])) return;

		$columns = [
			'Encounter Diagnosis / Instructions',
			'Location',
			'Date'
		];
		$rows = [];

		foreach($data['Encounter'] as $i => $encounter){
			$reference = $i + 1;
			$row = [];

			$row['Encounter' . $reference] = 'Visit: '. $encounter['ChiefComplaint'] . ' -- Diagnosis: ' . $this->diagnosisToText($encounter['Diagnosis']);
			$row['EncounterLocation' . $reference] = $this->organizationToText($encounter['Organization']);
			$row['EncounterDate' . $reference] = $this->dateToText($encounter['ServiceDates']['Low']);

			$rows[] = $row;

			$entry = [];
			$entry['@attributes']['typeCode'] = 'DRIV';
			$entry['encounter'] = $this->CDA_Templates->EncounterActivity($encounter, $reference);
			$section['entry'][] = $entry;
		}

		$section['text'] = [];
		$section['text']['table'] = $this->createTable($columns, $rows);

		//$this->addComponentSession(['section' => $section]);
	}

	protected function setFamilyHistorySection($option){
		$section = $this->initSection('FamilyHistorySection', $option);
		if($section === false) return;


		$data = $this->getSectionData('FamilyHistorySection');
		if($data === false) return;

		if(!isset($data['FamilyHistory'])) return;

		$columns = [
			'Condition',
			'Relation',
			'Date'
		];
		$rows = [];

		foreach($data['FamilyHistory'] as $i => $history){
			$reference = $i + 1;
			$row = [];

			$row['FamilyHistoryCondition' . $reference] = $this->codeToText($history['Condition']);
			$row['FamilyHistoryRelation' . $reference] = $this->codeToText($history['Relation']);
			$row['FamilyHistoryObservationDate' . $reference] = $this->dateToText($history['Dates']['Low']);

			$rows[] = $row;

			$entry = [];
			$entry['@attributes']['typeCode'] = 'DRIV';
			$entry['organizer'] = $this->CDA_Templates->FamilyHistoryOrganizer($history, $reference);
			$section['entry'][] = $entry;
		}

		$section['text'] = [];
		$section['text']['table'] = $this->createTable($columns, $rows);

//		$this->addComponentSession(['section' => $section]);
	}

	protected function setFunctionalStatusSection($option){
		$section = $this->initSection('FunctionalStatusSection', $option);
		if($section === false) return;

		$data = $this->getSectionData('FunctionalStatusSection');
		if($data === false) return;

		if(!isset($data['FunctionalStatus'])) return;

		$columns = [
			'Functional or Cognitive Finding',
			'Observation',
			'Observation Date',
			'Condition Status',
		];
		$rows = [];

		foreach($data['FunctionalStatus'] as $i =>  $status){
			$reference = $i + 1;
			$row = [];
			$row['FunctionalStatusCategory' . $reference] = $this->codeToText($status['Category']);

			if(is_string($status['Observation'])){
				$row['FunctionalStatusObservation' . $reference] = $status['Observation'];
			}else{
				$row['FunctionalStatusObservation' . $reference] = $this->codeToText($status['Observation']);
			}
			$row['FunctionalStatusDates' . $reference] = $this->dateToText($status['Dates']);
			$row['FunctionalStatusStatus' . $reference] = $status['Status']['DisplayName'];
			$rows[] = $row;

			$entry = [];
			$entry['@attributes']['typeCode'] = 'DRIV';
			$entry['organizer'] = $this->CDA_Templates->FunctionalStatusOrganizer($status, $reference);
			$section['entry'][] = $entry;
		}

		$section['text'] = [];
		$section['text']['table'] = $this->createTable($columns, $rows);

		$this->addComponentSession(['section' => $section]);
	}

	// TODO
	protected function setMentalStatusSection($option){
		$section = $this->initSection('MentalStatusSection', $option);
		if($section === false) return;

		$data = $this->getSectionData('MentalStatusSection');
		if($data === false) return;

		if(!isset($data['MentalStatus'])) return;

		$columns = [
			'Observation',
			'Effective Dates',
			'Status'
		];
		$rows = [];

		foreach($data['MentalStatus'] as $i =>  $status){
			$reference = $i + 1;
			$row = [];

			if(is_string($status['Observation'])){
				$row['MentalStatusObservation' . $reference] = $status['Observation'];
			}else{
				$row['MentalStatusObservation' . $reference] = $this->codeToText($status['Observation']);
			}
			$row['MentalStatusDates' . $reference] = $this->dateToText($status['Dates']);
			$row['MentalStatusStatus' . $reference] =  $status['Status']['DisplayName'];

			$rows[] = $row;

			$entry = [];
			$entry['@attributes']['typeCode'] = 'DRIV';
			$entry['act'] = $this->CDA_Templates->MentalStatusObservation($status, $reference);
			$section['entry'][] = $entry;
		}

		$section['text'] = [];
		$section['text']['table'] = $this->createTable($columns, $rows);

		$this->addComponentSession(['section' => $section]);
	}

	// TODO
	protected function setNutritionSection($option){
		$section = $this->initSection('NutritionSection', $option);
		if($section === false) return;


		//$this->addComponentSession(['section' => $section]);
	}

	// TODO
	protected function setAssessmentAndPlanSection($option){
		$section = $this->initSection('AssessmentAndPlanSection', $option);
		if($section === false) return;

		// TODO...

		//$this->addComponentSession(['section' => $section]);
	}


}