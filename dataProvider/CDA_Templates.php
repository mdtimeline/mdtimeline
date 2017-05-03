<?php

include_once(ROOT . '/classes/UUID.php');
include_once(ROOT . '/dataProvider/CDA_Utilities.php');

/**
 * Created by IntelliJ IDEA.
 * User: ernesto
 * Date: 4/18/17
 * Time: 8:12 PM
 */
class CDA_Templates extends CDA_Utilities {

	// PROBLEMS
	public function ProblemConcernActCondition($problem, $reference = null){
		$tpl = [];
		$tpl['@attributes']['classCode'] = 'ACT';
		$tpl['@attributes']['moodCode'] = 'EVN';
		$tpl['templateId']['@attributes']['root'] = '2.16.840.1.113883.10.20.22.4.3';
		$tpl['templateId']['@attributes']['extension'] = '2015-08-01';
		$tpl['id']['@attributes']['root'] = UUID::v4();
		$tpl['code']['@attributes']['code'] = '	CONC';
		$tpl['code']['@attributes']['codeSystem'] = '2.16.840.1.113883.5.6';
		$tpl['code']['@attributes']['displayName'] = 'Concern';
		$tpl['statusCode']['@attributes']['code'] = 'active';
		$tpl['effectiveTime'] = $this->effectiveTime($problem['CreatedDate']);

		$tpl['entryRelationship']['@attributes']['typeCode'] = 'SUBJ';
		$tpl['entryRelationship']['observation'] = $this->ProblemObservation($problem, $reference);


		return $tpl;
	}

	public function ProblemObservation($problem, $reference = null){

		$obs = [];
		$obs['@attributes']['classCode'] = 'OBS';
		$obs['@attributes']['moodCode'] = 'EVN';
		$obs['templateId']['@attributes']['root'] = '2.16.840.1.113883.10.20.22.4.4';
		$obs['templateId']['@attributes']['extension'] = '2015-08-01';
		$obs['id']['@attributes']['root'] = UUID::v4();
		// SNOMED-CT CODE
		$obs['code']['@attributes']['code'] = '55607006';
		$obs['code']['@attributes']['displayName'] = 'Problem';
		$obs['code']['@attributes']['codeSystem'] = '2.16.840.1.113883.6.96';
		$obs['code']['@attributes']['codeSystemName'] = 'SNOMED-CT';

		if(isset($reference)){
			$obs['code']['originalText']['reference'] = $this->reference('#Problem' . $reference);
		}

		// LOINC CODE
		$obs['code']['translation']['@attributes']['code'] = '75326-9';
		$obs['code']['translation']['@attributes']['displayName'] = 'Problem';
		$obs['code']['translation']['@attributes']['codeSystem'] = '2.16.840.1.113883.6.1';
		$obs['code']['translation']['@attributes']['codeSystemName'] = 'LOINC';

		if(isset($reference)){
			$obs['text']['reference'] = $this->reference('#Condition' . $reference);
		}

		$obs['statusCode']['@attributes']['code'] = 'completed';
		$obs['effectiveTime'] = $this->effectiveTime($problem['Dates']);
		$obs['value'] = $this->value($problem['Code']);


		return $obs;
	}

	// PROCEDURES
	public function ProcedureActivityProcedure($procedure, $reference = null){

		$tpl = [];
		$tpl['@attributes']['classCode'] = 'PROC';
		$tpl['@attributes']['moodCode'] = 'EVN';
		$tpl['templateId']['@attributes']['root'] = '2.16.840.1.113883.10.20.22.4.14';
		$tpl['templateId']['@attributes']['extension'] = '2014-06-09';
		$tpl['id']['@attributes']['root'] = UUID::v4();

		$tpl['code']['@attributes']['code'] = '	CONC';
		$tpl['code']['@attributes']['codeSystem'] = '2.16.840.1.113883.5.6';
		$tpl['code']['@attributes']['displayName'] = 'Concern';

		if(isset($reference)){
			$obs['code']['originalText']['reference'] = $this->reference('#Procedure' . $reference);
		}

		$tpl['statusCode']['@attributes']['code'] = 'active';
		$tpl['effectiveTime'] = $this->effectiveTime($procedure['Date']['Low'], false);
		$tpl['methodCode'] = $this->nullFlavor();

		$tpl['performer'] = $this->performer($procedure['Performer']);

		return $tpl;
	}

	// MEDICATION
	public function MedicationActivity($medication, $reference = null){

		$tpl = [];
		$tpl['@attributes']['classCode'] = 'SBADM';
		$tpl['@attributes']['moodCode'] = 'EVN';
		$tpl['templateId']['@attributes']['root'] = '2.16.840.1.113883.10.20.22.4.16';
		$tpl['templateId']['@attributes']['extension'] = '2014-06-09';
		$tpl['id']['@attributes']['root'] = UUID::v4();
		$tpl['statusCode']['@attributes']['code'] = 'active';
		$tpl['effectiveTime'] = $this->effectiveTime($medication['Dates']);

		if(!isset($medication['Route']) || $medication['Route'] == ''){
			$tpl['routeCode'] = $this->nullFlavor();
		}else{
			$tpl['routeCode'] = $medication['Route'];
		}
		if(!isset($medication['Quantity']) || $medication['Quantity'] == ''){
			$tpl['doseQuantity'] = $this->nullFlavor();
		}else{
			$tpl['doseQuantity'] = $medication['Quantity'];
		}

		$product['@attributes']['classCode'] = 'MANU';
		$product['templateId']['@attributes']['root'] = '2.16.840.1.113883.10.20.22.4.23';
		$product['templateId']['@attributes']['extension'] = '2014-06-09';
		$product['id']['@attributes']['root'] = UUID::v4();
		$product['manufacturedMaterial']['@attributes']['root'] = UUID::v4();
		$product['manufacturedMaterial']['code']['@attributes']['code'] = $medication['RXCUI'];
		$product['manufacturedMaterial']['code']['@attributes']['displayName'] = $medication['Medication'];
		$product['manufacturedMaterial']['code']['@attributes']['codeSystem'] = '2.16.840.1.113883.6.88';
		$product['manufacturedMaterial']['code']['@attributes']['codeSystemName'] = 'RXNORM';
		$tpl['consumable']['manufacturedProduct'] = $product;

		$tpl['performer'] = $this->performer($medication['Performer']);

		return $tpl;
	}

	// ALLERGY
	public function AllergyConcernAct($allergy, $reference = null){

		$tpl = [];
		$tpl['@attributes']['classCode'] = 'ACT';
		$tpl['@attributes']['moodCode'] = 'EVN';
		$tpl['templateId']['@attributes']['root'] = '2.16.840.1.113883.10.20.22.4.30';
		$tpl['templateId']['@attributes']['extension'] = '2015-08-01';
		$tpl['id']['@attributes']['root'] = UUID::v4();


		$tpl['code']['@attributes']['code'] = '	CONC';
		$tpl['code']['@attributes']['codeSystem'] = '2.16.840.1.113883.5.6';
		$tpl['code']['@attributes']['displayName'] = 'Concern';

		$tpl['statusCode']['@attributes']['code'] = 'active';
		$tpl['effectiveTime'] = $this->effectiveTime($allergy['Dates']);

		$product = [];
		$product['@attributes']['typeCode'] = 'CSM';
		$product['participantRole']['@attributes']['classCode'] = 'MANU';
		$product['participantRole']['playingEntity']['@attributes']['classCode'] = 'MMAT';
		$product['participantRole']['playingEntity']['code']['@attributes']['code'] = $allergy['Substance']['Code'];
		$product['participantRole']['playingEntity']['code']['@attributes']['displayName'] = $allergy['Substance']['DisplayName'];
		$product['participantRole']['playingEntity']['code']['@attributes']['codeSystem'] = $allergy['Substance']['CodeSystem'];
		$product['participantRole']['playingEntity']['code']['@attributes']['codeSystemName'] = $allergy['Substance']['CodeSystemName'];
		$product['participantRole']['playingEntity']['originalText']['reference'] = $this->reference('#Product' . $reference);

		$severity = [];
		$severity['@attributes']['typeCode'] = 'SUBJ';
		$severity['@attributes']['inversionInd'] = 'true';
		$severity['observation']['@attributes']['classCode'] = 'OBS';
		$severity['observation']['@attributes']['moodCode'] = 'EVN';
		$severity['observation']['templateId']['@attributes']['root'] = '2.16.840.1.113883.10.20.22.4.8';
		$severity['observation']['templateId']['@attributes']['extension'] = '2014-06-09';
		$severity['observation']['code']['@attributes']['code'] = 'SEV';
		$severity['observation']['code']['@attributes']['codeSystem'] = '2.16.840.1.113883.5.4';
		$severity['observation']['code']['@attributes']['codeSystemName'] = 'ActCode';
		$severity['observation']['code']['@attributes']['displayName'] = 'Severity Observation';
		$severity['observation']['text']['reference'] = $this->reference('#Severity'. $reference);
		$severity['observation']['statusCode']['@attributes']['code'] = 'completed';
		$severity['observation']['value']['@attributes']['xsi:type'] = 'CD';
		$severity['observation']['value']['@attributes']['code'] = $allergy['Severity']['Code'];
		$severity['observation']['value']['@attributes']['codeSystem'] = $allergy['Severity']['CodeSystem'];
		$severity['observation']['value']['@attributes']['codeSystemName'] = $allergy['Severity']['CodeSystemName'];
		$severity['observation']['value']['@attributes']['displayName'] = $allergy['Severity']['DisplayName'];

		$reaction = [];
		$reaction['@attributes']['typeCode'] = 'MFST';
		$reaction['@attributes']['inversionInd'] = 'true';
		$reaction['observation']['@attributes']['classCode'] = 'OBS';
		$reaction['observation']['@attributes']['moodCode'] = 'EVN';
		$reaction['observation']['templateId']['@attributes']['root'] = '2.16.840.1.113883.10.20.22.4.9';
		$reaction['observation']['templateId']['@attributes']['extension'] = '2014-06-09';
		$reaction['observation']['id']['@attributes']['root'] = UUID::v4();
		$reaction['observation']['code']['@attributes']['code'] = 'ASSERTION';
		$reaction['observation']['code']['@attributes']['codeSystem'] = '2.16.840.1.113883.5.4';
		$reaction['observation']['text']['reference'] = $this->reference('#Reaction'. $reference);
		$reaction['observation']['statusCode']['@attributes']['code'] = 'completed';
		$reaction['observation']['value']['@attributes']['xsi:type'] = 'CD';
		$reaction['observation']['value']['@attributes']['code'] = $allergy['Reaction']['Code'];
		$reaction['observation']['value']['@attributes']['codeSystem'] = $allergy['Reaction']['CodeSystem'];
		$reaction['observation']['value']['@attributes']['codeSystemName'] = $allergy['Reaction']['CodeSystemName'];
		$reaction['observation']['value']['@attributes']['displayName'] = $allergy['Reaction']['DisplayName'];

		$tpl['entryRelationship']['@attributes']['typeCode'] = 'SUBJ';
		$tpl['observation']['entryRelationship']['@attributes']['typeCode'] = 'OBS';
		$tpl['observation']['entryRelationship']['@attributes']['moodCode'] = 'EVN';
		$tpl['observation']['entryRelationship']['templateId']['@attributes']['root'] = '2.16.840.1.113883.10.20.22.4.7';
		$tpl['observation']['entryRelationship']['templateId']['@attributes']['extension'] = '2014-06-09';
		$tpl['observation']['entryRelationship']['id']['@attributes']['root'] = UUID::v4();
		$tpl['observation']['entryRelationship']['participant'] = $product;
		$tpl['observation']['entryRelationship']['entryRelationship'][] = $severity;
		$tpl['observation']['entryRelationship']['entryRelationship'][] = $reaction;

		return $tpl;

	}

	// RESULTS
	public function ResultOrganizer($result, $i_reference = null){

		$tpl = [];
		$tpl['@attributes']['classCode'] = 'BATTERY';
		$tpl['@attributes']['moodCode'] = 'EVN';
		$tpl['templateId']['@attributes']['root'] = '2.16.840.1.113883.10.20.22.4.1';
		$tpl['templateId']['@attributes']['extension'] = '2015-08-01';
		$tpl['id']['@attributes']['root'] = UUID::v4();
		$tpl['code'] = $this->code($result['Order']);
		$tpl['statusCode']['@attributes']['code'] = 'completed';
		$tpl['effectiveTime'] = $this->effectiveTime($result['Dates'], false);

		foreach($result['Observations'] as $j => $observation){
			$reference = $i_reference . ($j + 1);

			$obs = [];
			$obs['@attributes']['classCode'] = 'OBS';
			$obs['@attributes']['moodCode'] = 'EVN';
			$obs['templateId']['@attributes']['root'] = '2.16.840.1.113883.10.20.22.4.2';
			$obs['templateId']['@attributes']['extension'] = '2015-08-01';
			$obs['id']['@attributes']['root'] = UUID::v4();
			$obs['code'] = $this->code($observation['Observation']);
			if(isset($reference)){
				$obs['code']['originalText']['reference'] = $this->reference('#Result' . $reference);
			}
			$obs['statusCode']['@attributes']['code'] = 'completed';
			$obs['effectiveTime'] = $this->effectiveTime($observation['Dates'], false);
			$obs['value'] = $this->value($observation['Value'], 'ST');

			$obs['interpretationCode'] = $this->code($observation['InterpretationCode']);
			$obs['referenceRange']['observationRange'] = $this->referenceRange($observation['ReferenceRange']);

			$tpl['observation'][] = $obs;

		}

		return $tpl;
	}

	// VITAL SIGNS
	public function VitalSignsOrganizer($vitals, $i_reference = null){

		$tpl = [];
		$tpl['@attributes']['classCode'] = 'CLUSTER';
		$tpl['@attributes']['moodCode'] = 'EVN';
		$tpl['templateId']['@attributes']['root'] = '2.16.840.1.113883.10.20.22.4.26';
		$tpl['templateId']['@attributes']['extension'] = '2015-08-01';
		$tpl['id']['@attributes']['root'] = UUID::v4();
		$tpl['code']['@attributes']['code'] = '46680005';
		$tpl['code']['@attributes']['codeSystem'] = '2.16.840.1.113883.6.96';
		$tpl['code']['@attributes']['codeSystemName'] = 'SNOMED-CT';
		$tpl['code']['@attributes']['displayName'] = 'Vital signs';
		$tpl['statusCode']['@attributes']['code'] = 'completed';
		$tpl['effectiveTime'] = $this->effectiveTime($vitals['Dates']);

		foreach($vitals['Observations'] as $j => $observation){
			$reference = $i_reference . ($j + 1);

			if($observation['Value']['Value'] == '') continue;

			$tpl['component'][] = $this->VitalSignsObservation($observation, $vitals['Dates'], $i_reference);
		}

		return $tpl;
	}

	public function VitalSignsObservation($observation, $dates, $i_reference = null){

		$tpl = [];
		$tpl['observation']['@attributes']['classCode'] = 'OBS';
		$tpl['observation']['@attributes']['moodCode'] = 'EVN';
		$tpl['observation']['templateId']['@attributes']['root'] = '2.16.840.1.113883.10.20.22.4.27';
		$tpl['observation']['templateId']['@attributes']['extension'] = '2014-06-09';
		$tpl['observation']['id']['@attributes']['root'] = UUID::v4();
		$tpl['observation']['code'] = $this->code($observation['Observation']);
		$tpl['observation']['statusCode']['@attributes']['code'] = 'completed';
		$tpl['observation']['effectiveTime'] = $this->effectiveTime($dates, false);

		$tpl['observation']['value']['@attributes']['xsi:type'] = 'PQ';
		$tpl['observation']['value']['@attributes']['value'] = $observation['Value']['Value'];
		$tpl['observation']['value']['@attributes']['unit'] = $observation['Value']['Unit'];

		$tpl['observation']['interpretationCode']['@attributes']['code'] = 'N';
		$tpl['observation']['interpretationCode']['@attributes']['codeSystem'] = '2.16.840.1.113883.5.83';

		return $tpl;
	}

	// IMMUNIZATION
	public function ImmunizationActivity($immunization, $reference = null){

		$tpl = [];
		$tpl['@attributes']['classCode'] = 'SBADM';
		$tpl['@attributes']['moodCode'] = 'EVN';
		$tpl['@attributes']['negationInd'] = 'false';
		$tpl['templateId']['@attributes']['root'] = '2.16.840.1.113883.10.20.22.4.52';
		$tpl['templateId']['@attributes']['extension'] = '2015-08-01';
		$tpl['observation']['id']['@attributes']['root'] = UUID::v4();
		if(isset($reference)){
			$obs['text']['reference'] = $this->reference('#Immunization' . $reference);
		}
		$tpl['statusCode']['@attributes']['code'] = 'completed';
		$tpl['effectiveTime'] = $this->effectiveTime($immunization['Administration']['Dates'], false);
		$tpl['routeCode'] = $immunization['Route'];

		$prod['@attributes']['classCode'] = 'MANU';
		$prod['templateId']['@attributes']['root'] = '2.16.840.1.113883.10.20.22.4.54';
		$prod['templateId']['@attributes']['extension'] = '2014-06-09';
		$prod['manufacturedMaterial']['code'] = $this->code($immunization['Vaccine']);

		if(isset($immunization['LotNumber']) && $immunization['LotNumber'] != ''){
			$prod['manufacturedMaterial']['lotNumberText'] = $immunization['LotNumber'];
		}else{
			$prod['manufacturedMaterial']['lotNumberText'] = $this->nullFlavor();
		}

		if(isset($immunization['Manufacturer']) && $immunization['Manufacturer'] != ''){
			$prod['manufacturerOrganization']['name'] = $immunization['Manufacturer'];
		}else{
			$prod['manufacturerOrganization']['name'] = $this->nullFlavor();
		}

		$tpl['consumable']['manufacturedProduct'] = $prod;


		if($immunization['RefusalReason'] == 'NI'){
			return $tpl;
		}

		// WAS REJECTED...

		$obs['@attributes']['classCode'] = 'MANU';
		$obs['@attributes']['moodCode'] = 'EVN';
		$obs['templateId']['@attributes']['root'] = '2.16.840.1.113883.10.20.22.4.53';
		$obs['id']['@attributes']['root'] = UUID::v4();
		$obs['code'] = $this->code($immunization['RefusalReason']);

		$tpl['entryRelationship']['@attributes']['typeCode'] = 'RSON';
		$tpl['entryRelationship']['observation'] = $obs;

		return $tpl;
	}

	// SOCIAL HISTORY
	public function SocialHistoryObservation($history, $reference = null){
		$tpl = [];
		$tpl['@attributes']['classCode'] = 'OBS';
		$tpl['@attributes']['moodCode'] = 'EVN';

		// Other templates ID that we could use
		//
		// Pregnancy Observation                2.16.840.1.113883.10.20.15.3.8
		// Caregiver Characteristics            2.16.840.1.113883.10.20.22.4.72
		// Cultural and Religious Observation   2.16.840.1.113883.10.20.22.4.111
		// Characteristics of Home              2.16.840.1.113883.10.20.22.4.109

		switch($history['Category']['Code']){
			case '229819007': // Tobacco Use 2.16.840.1.113883.10.20.22.4.85
				$tpl['templateId']['@attributes']['root'] = '2.16.840.1.113883.10.20.22.4.85';
				$tpl['templateId']['@attributes']['extension'] = '2014-06-09';
				break;
			case '72166-2': // Smoking Status 2.16.840.1.113883.10.20.22.4.78
				$tpl['templateId']['@attributes']['root'] = '2.16.840.1.113883.10.20.22.4.78';
				$tpl['templateId']['@attributes']['extension'] = '2014-06-09';
				break;
			default: // Social History Observation 2.16.840.1.113883.10.20.22.4.38
				$tpl['templateId']['@attributes']['root'] = '2.16.840.1.113883.10.20.22.4.38';
				$tpl['templateId']['@attributes']['extension'] = '2015-08-01';
		}

		$tpl['id']['@attributes']['root'] = UUID::v4();
		$tpl['code'] = $this->code($history['Category']);

		if(isset($reference)){
			$tpl['code']['originalText']['reference'] = $this->reference('#Observation' . $reference);
		}

		$tpl['statusCode']['@attributes']['code'] = 'completed';
		$tpl['effectiveTime'] = $this->effectiveTime($history['Dates'], false);

		$type = is_string($history['Observation']) ? 'ST' : 'CD';
		$tpl['value'] = $this->value($history['Observation'], $type);

		return $tpl;
	}

	// ADVANCE DIRECTIVE TODO
	public function AdvanceDirectiveOrganizer($directive, $reference = null){

		$tpl = [];
		$tpl['@attributes']['classCode'] = 'CLUSTER';
		$tpl['@attributes']['moodCode'] = 'EVN';
		$tpl['templateId']['@attributes']['root'] = '2.16.840.1.113883.10.20.22.4.108';
		$tpl['templateId']['@attributes']['extension'] = '2015-08-01';
		$tpl['id']['@attributes']['root'] = UUID::v4();
		$tpl['code']['@attributes']['code'] = '45473-6';
		$tpl['code']['@attributes']['codeSystem'] = '2.16.840.1.113883.6.1';
		$tpl['statusCode']['@attributes']['code'] = 'completed';
		$tpl['effectiveTime'] = $this->effectiveTime($directive['Dates']);

		return $tpl;
	}

	// ENCOUNTERS
	public function EncounterActivity($encounter, $reference = null){

		$tpl = [];
		$tpl['@attributes']['classCode'] = 'ENC';
		$tpl['@attributes']['moodCode'] = 'EVN';
		$tpl['templateId']['@attributes']['root'] = '2.16.840.1.113883.10.20.22.4.49';
		$tpl['templateId']['@attributes']['extension'] = '2015-08-01';
		$tpl['id']['@attributes']['root'] = UUID::v4();
		$tpl['effectiveTime'] = '??? CPT';
		$tpl['code'] = $this->code($encounter['VisitCode']);
		$tpl['performer'] = $this->performer($encounter['Provider']);

		$participant = [];
		$participant['@attributes']['classCode'] = 'SDLOC';
		$participant['templateId']['@attributes']['root'] = '2.16.840.1.113883.10.20.22.4.32';
		$participant['code'] = '';
		$participant['addr'] = $this->addr($encounter['Organization']['Address']);
		$participant['telecom'] = $this->telecom($encounter['Organization']['Telecom']);
		$participant['playingEntity']['@attributes']['classCode'] = 'PLC';
		$participant['playingEntity']['name'] = $encounter['Organization']['Name'];

		$tpl['participant']['@attributes']['typeCode'] = 'LOC';
		$tpl['participant']['participant'] = $participant;


		if(empty($encounter['Diagnosis'])) return $tpl;

		$tpl['entryRelationship']['@attributes']['typeCode'] = 'SUBJ';
		$tpl['entryRelationship']['act']['@attributes']['classCode'] = 'ACT';
		$tpl['entryRelationship']['act']['@attributes']['moodCode'] = 'EVN';
		$tpl['entryRelationship']['templateId']['@attributes']['root'] = '2.16.840.1.113883.10.20.22.4.80';
		$tpl['entryRelationship']['templateId']['@attributes']['extension'] = '2015-08-01';
		$tpl['entryRelationship']['id']['@attributes']['root'] = UUID::v4();
		$tpl['entryRelationship']['code']['@attributes']['xsi:type'] = 'CE';
		$tpl['entryRelationship']['code']['@attributes']['code'] = '29308-4';
		$tpl['entryRelationship']['code']['@attributes']['codeSystem'] = '2.16.840.1.113883.6.1';
		$tpl['entryRelationship']['code']['@attributes']['codeSystemName'] = 'LOINC';
		$tpl['entryRelationship']['code']['@attributes']['displayName'] = 'ENCOUNTER DIAGNOSIS';
		$tpl['entryRelationship']['effectiveTime'] = $this->effectiveTime($encounter['ServiceDates']);

		foreach($encounter['Diagnosis'] as $diagnosis){
			$tpl['entryRelationship']['entryRelationship'][] = $this->EncounterDiagnosis($diagnosis, $encounter['ServiceDates']);
		}

		return $tpl;
	}

	public function EncounterDiagnosis($diagnosis, $dates, $reference = null){

		$tpl = [];
		$tpl['@attributes']['classCode'] = 'SUBJ';
		$tpl['@attributes']['inversionInd'] = 'false';
		$tpl['observation']['@attributes']['classCode'] = 'OBS';
		$tpl['observation']['@attributes']['moodCode'] = 'EVN';
		$tpl['observation']['templateId']['@attributes']['root'] = '2.16.840.1.113883.10.20.22.4.4';
		$tpl['observation']['templateId']['@attributes']['extension'] = '2015-08-01';
		$tpl['observation']['id']['@attributes']['root'] = UUID::v4();
		$tpl['observation']['code'] = '';
		$tpl['observation']['statusCode']['@attributes']['code'] = 'completed';
		$tpl['observation']['effectiveTime'] = $this->effectiveTime($dates);
		$tpl['observation']['value'] = $this->value($diagnosis['Code']);

		return $tpl;

	}

	// FamilyHistory
	public function FamilyHistoryOrganizer($history, $reference = null){

		$tpl = [];
		$tpl['@attributes']['classCode'] = 'CLUSTER';
		$tpl['@attributes']['moodCode'] = 'EVN';
		$tpl['templateId']['@attributes']['root'] = '2.16.840.1.113883.10.20.22.4.45';
		$tpl['templateId']['@attributes']['extension'] = '2015-08-01';
		$tpl['id']['@attributes']['root'] = UUID::v4();
		$tpl['statusCode']['@attributes']['code'] = 'completed';
		$tpl['subject']['relatedSubject']['@attributes']['classCode'] = 'PRS';
		$tpl['subject']['relatedSubject']['code'] = $this->code($history['Relation']);

		$tpl['component']['observation']['@attributes']['classCode'] = 'OBS';
		$tpl['component']['observation']['@attributes']['moodCode'] = 'EVN';
		$tpl['component']['observation']['templateId']['@attributes']['root'] = '2.16.840.1.113883.10.20.22.4.46';
		$tpl['component']['observation']['templateId']['@attributes']['extension'] = '2015-08-01';
		$tpl['component']['observation']['id']['@attributes']['root'] = UUID::v4();

		$tpl['component']['observation']['code']['@attributes']['code'] = '64572001';
		$tpl['component']['observation']['code']['@attributes']['codeSystem'] = '2.16.840.1.113883.6.96';
		$tpl['component']['observation']['code']['@attributes']['displayName'] = 'Condition';
		$tpl['component']['observation']['statusCode']['@attributes']['code'] = 'completed';
		$tpl['component']['observation']['effectiveTime'] = $this->nullFlavor();

		$tpl['component']['observation']['value'] = $this->value($history['Condition']);
		if(isset($reference)){
			$obs['component']['observation']['code']['originalText']['reference'] = $this->reference('#FamilyHistoryCondition' . $reference);
		}

		return $tpl;
	}

	public function FunctionalStatusOrganizer($status, $reference = null){
		//2.16.840.1.113883.10.20.22.4.66

		$tpl = [];
		$tpl['@attributes']['classCode'] = 'CLUSTER';
		$tpl['@attributes']['moodCode'] = 'EVN';
		$tpl['templateId']['@attributes']['root'] = '2.16.840.1.113883.10.20.22.4.66';
		$tpl['templateId']['@attributes']['extension'] = '2014-06-09';
		$tpl['id']['@attributes']['root'] = UUID::v4();
		$tpl['statusCode']['@attributes']['code'] = 'completed';
	}

	public function FunctionalStatusObservation(){

	}

	public function SelfCareActivitiesADLandIADL($data){
		// Instrumental Activities of Daily Living (IADL) e.g., cooking, managing medications, driving, shopping
		// Activities of Daily Living (ADL) e.g., dressing, bathing, eating

		$tpl = [];
		$tpl['@attributes']['classCode'] = 'OBS';
		$tpl['@attributes']['moodCode'] = 'EVN';
		$tpl['templateId']['@attributes']['root'] = '2.16.840.1.113883.10.20.22.4.128';

		/**
		 * 46008-9 Bathing
		 * 28409-1 Dressing
		 * 28408-3 Toileting
		 * 46484-2 Feeding or Eating
		 * 46482-6 Transferring
		 * 28413-3 Ambulation
		 * 45618-6 Bowel continence
		 * 45619-4 Bladder continence
		 */

		$tpl['code']['@attributes']['root'] = '';
		$tpl['statusCode']['@attributes']['code'] = 'completed';
		$tpl['effectiveTime'] = $this->effectiveTime($data['Dates']);

		/**
		 * 371150009 able
		 * 371153006 independent
		 * 371155004 able to and does
		 * 371152001 assisted
		 * 371154000 dependent
		 * 371151008 unable
		 * 371156003 does not
		 * 371157007 difficulty
		 * 385640009 does
		 */

		$tpl['value'] = '';

	}

	public function MentalStatusObservation($status, $reference = null){
		//2.16.840.1.113883.10.20.22.4.74
	}




}