<?php
/**
 * GaiaEHR (Electronic Health Records)
 * Copyright (C) 2013 Certun, LLC.
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
include_once(ROOT . '/classes/Array2XML.php');
include_once(ROOT . '/classes/XML2Array.php');
include_once(ROOT . '/dataProvider/SnomedCodes.php');
include_once(ROOT . '/dataProvider/Person.php');
include_once(ROOT . '/dataProvider/PatientContacts.php');
include_once(ROOT . '/dataProvider/Rxnorm.php');

class CDA_Parser
{

	private $document;

	private $index;

	public $styledXml;

	/**
	 * @var SnomedCodes
	 */
	private $SnomedCodes;

	/**
	 * @var Rxnorm
	 */
	private $Rxnorm;

	function __construct($xml = null)
	{

		$this->Rxnorm = new Rxnorm();

		if (isset($xml))
			$this->setDocument($xml);
	}

	function setDocument($xml)
	{
		$this->document = $this->XmlToArray($xml);
		unset($this->document['ClinicalDocument']['@attributes']);

		Array2XML::init('1.0', 'UTF-8', true, ['xml-stylesheet' => 'type="text/xsl" href="' . URL . '/lib/CCRCDA/schema/cda2.xsl"']);

		$data = [
			'@attributes' => [
				'xmlns' => 'urn:hl7-org:v3',
				'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
				'xsi:schemaLocation' => 'urn:hl7-org:v3 CDA.xsd'
			]
		];
		foreach ($this->document['ClinicalDocument'] as $i => $com) {
			$data[$i] = $com;
		}

		// Building the document
		$this->styledXml = Array2XML::createXML('ClinicalDocument', $data)->saveXML();
		unset($data);

		$this->index = [];
		foreach ($this->document['ClinicalDocument']['component']['structuredBody']['component'] as $index => $component) {
			$code = isset($component['section']['code']['@attributes']['code']) ? $component['section']['code']['@attributes']['code'] : '';

			//Advance Directives ???
			switch ($code) {
				case '48765-2':
					$this->index['allergies'] = $index;
					break;
				case '10160-0':
					$this->index['medications'] = $index;
					break;
				case '11450-4':
					$this->index['problems'] = $index;
					break;
				case '47519-4':
					$this->index['procedures'] = $index;
					break;
				case '30954-2':
					$this->index['results'] = $index;
					break;
				case '46240-8':
					$this->index['encounters'] = $index;
					break;
				case '51847-2':
					$this->index['assessments'] = $index;
					break;
				case '46239-0':
					$this->index['chiefcomplaint'] = $index;
					break;
				default:
					$tplId = isset($component['section']['templateId']['@attributes']['root']) ? $component['section']['templateId']['@attributes']['root'] : '';
					if ($tplId == '2.16.840.1.113883.10.20.22.2.21.1') $this->index['advancedirectives'] = $index;
					break;
			}
		}
	}

	function parseDocument($xml = null)
	{
		if (isset($xml)) {
			$this->setDocument($xml);
		}
		return $this->getDocument();
	}

	function getDocument()
	{
		$document = new stdClass();
		$document->title = $this->getTitle();
		$document->patient = $this->getPatient();
		$document->encounter = $this->getEncounter();
		$document->author = $this->getAuthor();
		$document->allergies = $this->getAllergies($document->author);
		$document->medications = $this->getMedications($document->author);
		$document->problems = $this->getProblems($document->author);
		$document->procedures = $this->getProcedures();
		$document->results = $this->getResults();
		$document->encounters = $this->getEncounters();
		$document->advancedirectives = $this->getAdvanceDirectives();
		return $document;
	}

	function getTitle()
	{
		return isset($this->document['ClinicalDocument']['title']) ? $this->document['ClinicalDocument']['title'] : '';
	}

	/**
	 * @return mixed
	 * @throws Exception
	 */
	function getPatient()
	{
		$dom = $this->document['ClinicalDocument']['recordTarget']['patientRole'];
		$patient = new stdClass();

		// IDs
		if ($this->isAssoc($dom['id'])) {
			$patient->pubpid = $dom['id']['@attributes']['extension'];
		} else {
			$foo = [];
			foreach ($dom['id'] as $id) {
				$foo[] = $id['@attributes']['extension'];
			}
			$patient->pubpid = implode('~', $foo);
			unset($foo);
		}

		// address
		// TODO: Here we need to create a new Patient Contact record. (Self)
		$a = isset($dom['addr']) ? $dom['addr'] : [];
		//$PatientContact = new PatientContacts();
		$patient->postal_address = isset($a['streetAddressLine']) ? $a['streetAddressLine'] : '';
		$patient->postal_city = isset($a['city']) ? $a['city'] : '';
		$patient->postal_state = isset($a['state']) ? $a['state'] : '';
		$patient->postal_zip = isset($a['postalCode']) ? $a['postalCode'] : '';
		$patient->postal_country = isset($a['country']) ? $a['country'] : '';
		unset($a);

        $patient->fulladdress = '';

        if($patient->postal_address !== ''){
            $patient->fulladdress .= ' ' . $patient->postal_address;
        }
        if($patient->postal_city !== ''){
            $patient->fulladdress .= ' ' . $patient->postal_city;
        }
        if($patient->postal_state !== ''){
            $patient->fulladdress .= ' ' . $patient->postal_state;
        }
        if($patient->postal_zip !== ''){
            $patient->fulladdress .= ' ' . $patient->postal_zip;
        }
        if($patient->postal_country !== ''){
            $patient->fulladdress .= ' ' . $patient->postal_country;
        }

        $patient->fulladdress = trim($patient->fulladdress);

		// phones
		if (isset($dom['telecom'])) {
			$telecoms = $this->telecomHandler($dom['telecom']);
			foreach ($telecoms as $type => $telecom) {
				if ($type == 'WP') {
					$patient->work_phone = $telecom;
				} else {
					$patient->home_phone = $telecom;
				}
			}
		}

        $patient->phones = [];
		if(isset($patient->home_phone)){
            $patient->phones[] = $patient->home_phone . ' (H)';
        }
		if(isset($patient->work_phone)){
            $patient->phones[] = $patient->home_phone . ' (W)';
        }

        $patient->phones = implode('<br>', $patient->phones);


		if (!isset($dom['patient'])) {
			throw new Exception('Error: ClinicalDocument->recordTarget->patientRole->Patient is required');
		}
		//names
		if (!isset($dom['patient']['name']['given'])) {
			throw new Exception('Error: Patient given name is required');
		}
		if (!isset($dom['patient']['name']['family'])) {
			throw new Exception('Error: Patient family name is required');
		}
		$names = $this->namesHandler($dom['patient']['name']);
		$patient->fname = $names['L']['fname'];
		$patient->mname = $names['L']['mname'];
		$patient->lname = $names['L']['lname'];
		$patient->name = Person::fullname($patient->fname, $patient->mname, $patient->lname);

		$patient->birth_fname = $names['BR']['fname'];
		$patient->birth_mname = $names['BR']['mname'];
		$patient->birth_lname = $names['BR']['lname'];
		//gender
		if (!isset($dom['patient']['administrativeGenderCode'])) {
			throw new Exception('Error: Patient gender is required');
		}
		$patient->sex = $dom['patient']['administrativeGenderCode']['@attributes']['code'];
		//DOB
		$patient->DOB = $this->dateParser($dom['patient']['birthTime']['@attributes']['value']);
		// fix for date with only the day...  add the time at the end
		if (strlen($patient->DOB) <= 10)
			$patient->DOB .= ' 00:00:00';

		//marital StatusCode
		$patient->marital_status = isset($dom['patient']['maritalStatusCode']['@attributes']['code']) ? $dom['patient']['maritalStatusCode']['@attributes']['code'] : '';
		$patient->marital_statu_name = isset($dom['patient']['maritalStatusCode']['@attributes']['displayName']) ? $dom['patient']['maritalStatusCode']['@attributes']['displayName'] : '';

		//race
		$patient->race = isset($dom['patient']['raceCode']['@attributes']['code']) ? $dom['patient']['raceCode']['@attributes']['code'] : '';
		//secondary race
        $patient->secondary_race = isset($dom['patient']['sdtc:raceCode']['@attributes']['code']) ? $dom['patient']['sdtc:raceCode']['@attributes']['code'] : '';
        $patient->secondary_race_name = isset($dom['patient']['sdtc:raceCode']['@attributes']['displayName']) ? $dom['patient']['sdtc:raceCode']['@attributes']['displayName'] : '';

		//ethnicGroupCode
		$patient->ethnicity = isset($dom['patient']['ethnicGroupCode']['@attributes']['code']) ? $dom['patient']['ethnicGroupCode']['@attributes']['code'] : '';
		$patient->ethnicity_name = isset($dom['patient']['ethnicGroupCode']['@attributes']['displayName']) ? $dom['patient']['ethnicGroupCode']['@attributes']['displayName'] : '';


		//birthplace
		if (isset($dom['patient']['birthplace']['place']['addr'])) {
			$addr = $dom['patient']['birthplace']['place']['addr'];
			$foo = '';

			if (isset($addr['city'])) {
				$foo .= is_string($addr['city']) ? $addr['city'] : '';
			}
			if (isset($addr['state'])) {
				$foo .= is_string($addr['state']) ? ' ' . $addr['state'] : '';
			}
			if (isset($addr['country'])) {
				$foo .= is_string($addr['country']) ? ' ' . $addr['country'] : '';
			}

			$patient->birth_place = trim($foo);
		} else {
			$patient->birth_place = '';
		}

		//languageCommunication
		$patient->language = isset($dom['patient']['languageCommunication']['languageCode']['@attributes']['code']) ? $dom['patient']['languageCommunication']['languageCode']['@attributes']['code'] : '';
		$patient->language_name = isset($dom['patient']['languageCommunication']['languageCode']['@attributes']['displayName']) ? $dom['patient']['languageCommunication']['languageCode']['@attributes']['displayName'] : '';

		switch (strtolower($patient->language)){
            case 'eng':
                $patient->language = 'en';
                break;
            case 'esp':
                $patient->language = 'es';
                break;
        }

		//religious  not implemented
		//$patient->religion = '';

		//guardian
		// TODO: Here we need to create a new Patient Contact record. (Guardian)
		if (isset($dom['patient']['guardian'])) {
			// do a bit more...
			// lets just save the name for now
			if ($dom['patient']['guardian']['guardianPerson']) {
				$name = isset($dom['patient']['guardian']['guardianPerson']['name']['given']) ? $dom['patient']['guardian']['guardianPerson']['name']['given'] : '';
				$name .= isset($dom['patient']['guardian']['guardianPerson']['name']['family']) ? ' ' . $dom['patient']['guardian']['guardianPerson']['name']['family'] : '';
				$patient->guardians_name = trim($name);
			}
		}
		unset($dom);
		return $patient;

	}

	/**
	 * @return stdClass
	 */
	function getAuthor()
	{
		$dom = $this->document['ClinicalDocument']['author'];
		$author = new stdClass();

		if (isset($dom['assignedAuthor'])) {
			$author->id = $dom['assignedAuthor']['id']['@attributes']['extension'];

			if (isset($dom['assignedAuthor']['assignedPerson']['name'])) {
				$names = $this->namesHandler($dom['assignedAuthor']['assignedPerson']['name']);
				$author->fname = $names['L']['fname'];
				$author->mname = $names['L']['mname'];
				$author->lname = $names['L']['lname'];
			}

			if(!isset($author->lname) && isset($dom['assignedAuthor']['representedOrganization']['name'])){
				$author->lname = $dom['assignedAuthor']['representedOrganization']['name'];
			}

			if (isset($dom['assignedAuthor']['addr'])) {
				$author->address = isset($dom['assignedAuthor']['addr']['streetAddressLine']) ? $dom['assignedAuthor']['addr']['streetAddressLine'] : '';
				$author->city = isset($dom['assignedAuthor']['addr']['city']) ? $dom['assignedAuthor']['addr']['city'] : '';
				$author->state = isset($dom['assignedAuthor']['addr']['state']) ? $dom['assignedAuthor']['addr']['state'] : '';
				$author->zipcode = isset($dom['assignedAuthor']['addr']['postalCode']) ? $dom['assignedAuthor']['addr']['postalCode'] : '';
				$author->country = isset($dom['assignedAuthor']['addr']['country']) ? $dom['assignedAuthor']['addr']['country'] : '';
			}
			if (isset($dom['assignedAuthor']['telecom']) && $dom['assignedAuthor']['telecom'] !== '') {
				$telecoms = $this->telecomHandler($dom['assignedAuthor']['telecom']);
				foreach ($telecoms as $type => $telecom) {
					if ($type == 'WP') {
						$author->work_phone = $telecom;
					} else {
						$author->home_phone = $telecom;
					}
				}
			}
		}

		return $author;
	}

	function getEncounter()
	{
		$encounter = new stdClass();

		if (!isset($this->document['ClinicalDocument']['componentOf'])) {
			return $encounter;
		}

		$dom = $this->document['ClinicalDocument']['componentOf'];
		if (isset($dom['encompassingEncounter'])) {
			$encounter->rid = (isset($dom['encompassingEncounter']['id']['@attributes']['extension']) ? $dom['encompassingEncounter']['id']['@attributes']['extension'] : null);
			$times = $this->datesHandler($dom['encompassingEncounter']['effectiveTime']);
			$encounter->service_date = $times['low'];
			unset($times);
		}

		if (isset($this->index['chiefcomplaint'])) {
			$cc = $this->document['ClinicalDocument']['component']['structuredBody']['component'][$this->index['chiefcomplaint']]['section'];
			$encounter->brief_description = $cc['text']['paragraph']['@value'];
		}

		if (isset($this->index['assessments'])) {
			$assessments = $this->document['ClinicalDocument']['component']['structuredBody']['component'][$this->index['assessments']]['section'];
			if ($this->isAssoc($assessments['entry']))
				$section['entry'] = [$assessments['entry']];

			$encounter->assessments = [];

			foreach ($assessments['entry'] as $i => $entry) {
				if (isset($entry['act'])) {
					$assessment = new stdClass();
					$assessment->text = $assessments['text']['paragraph'][$i]['@value'];
					$code = $this->codeHandler($entry['act']['code']);
					$assessment->code = $code['code'];
					$assessment->code_text = $code['code_text'];
					$assessment->code_type = $code['code_type'];
					$encounter->assessments[] = $assessment;
				}
			}
		}

		return $encounter;
	}

	/**
	 * @return array
	 */
	function getAllergies($author)
	{
		$allergies = [];

		if (!isset($this->index['allergies'])) {
			return $allergies;
		}

		$section = $this->document['ClinicalDocument']
		['component']
		['structuredBody']
		['component']
		[$this->index['allergies']]
		['section'];

		if (!isset($section['entry'])) {
			return $allergies;
		}

		if ($this->isAssoc($section['entry']))
			$section['entry'] = [$section['entry']];
		foreach ($section['entry'] as $entry) {

			$allergy = new stdClass();

			// allergy type
			$code = $this->codeHandler($entry['act']['entryRelationship']['observation']['value']['@attributes']);
			$allergy->allergy_type = $code['code_text'];
			$allergy->allergy_type_code = $code['code'];
			$allergy->allergy_type_code_type = $code['code_type'];
			unset($code);

			// allergy
			$code = $this->codeHandler($entry['act']['entryRelationship']['observation']['participant']['participantRole']['playingEntity']['code']['@attributes']);
			$allergy->allergy = $code['code_text'];
			$allergy->allergy_code = $code['code'];
			$allergy->allergy_code_type = $code['code_type'];
			unset($code);

			//dates
			if(
				isset($entry['act']['entryRelationship']['observation']['author']['time']['@attributes']['value'])
			){
				$allergy->begin_date = $this->dateHandler($entry['act']['entryRelationship']['observation']['author']['time']['@attributes']['value']);
			}elseif (isset($entry['act']['effectiveTime'])) {
				$dates = $this->datesHandler($entry['act']['effectiveTime'], true);
				$allergy->begin_date = $dates['low'];
				$allergy->end_date = $dates['high'];
			}

			// reaction, severity, status
			foreach ($entry['act']['entryRelationship']['observation']['entryRelationship'] as $obs) {
				$key = null;
				$root = null;

				if(isset($obs['observation'])){
                    $obs = $obs['observation'];
                }

				if(isset($obs['templateId']['@attributes']['root'])){
					$root = $obs['templateId']['@attributes']['root'];
				}elseif (isset($obs['templateId'][0]['@attributes']['root'])){
					$root = $obs['templateId'][0]['@attributes']['root'];
				}

				switch ($root) {
					case '2.16.840.1.113883.10.20.22.4.28':
						$key = 'status';
						break;
					case '2.16.840.1.113883.10.20.22.4.9':
						$key = 'reaction';
						break;
					case '2.16.840.1.113883.10.20.22.4.8':
						$key = 'severity';
						break;
				}

				if (isset($key)) {
					$code = $this->codeHandler($obs['value']['@attributes']);
					$allergy->{$key} = $code['code_text'];
					$allergy->{$key . '_code'} = $code['code'];
					$allergy->{$key . '_code_type'} = $code['code_type'];
					unset($code);
				};
			}

			if(!isset($allergy->end_date) && !isset($allergy->status)){
				$allergy->status = 'Active';
				$allergy->status_code = '55561003';
				$allergy->status_code_type = 'SNOMEDCT';
			}

			$allergy->create_date = date('Y-m-d H:i:s');
			$allergy->update_date = date('Y-m-d H:i:s');
			$allergy->source = $author->lname  . (isset($author->fname) ? (', ' . $author->fname) : '');

			$allergies[] = $allergy;
		}

		return $allergies;

	}

	/**
	 * @return array
	 */
	function getMedications($author)
	{
		$medications = [];

		if (!isset($this->index['medications'])) {
			return $medications;
		}

		$section = $this->document['ClinicalDocument']['component']['structuredBody']['component'][$this->index['medications']]['section'];

		if (!isset($section['entry'])) return $medications;

		if ($this->isAssoc($section['entry']))
			$section['entry'] = [$section['entry']];
		foreach ($section['entry'] as $entry) {

			$medication = new stdClass();

			if (!$this->isAssoc($entry['substanceAdministration']['effectiveTime'])) {
				foreach ($entry['substanceAdministration']['effectiveTime'] as $date) {
					if (!isset($date['low']))
						continue;
					$dates = $this->datesHandler($date, true);
				}
			} else {
				$dates = $this->datesHandler($entry['substanceAdministration']['effectiveTime'], true);
			}

			// dates
			if (isset($dates)) {
				$medication->begin_date = $dates['low'];
				$medication->end_date = $dates['high'];
			}

			// rxnorm
			if ($entry['substanceAdministration']['consumable']['manufacturedProduct']['manufacturedMaterial']) {
				$code = $this->codeHandler($entry['substanceAdministration']['consumable']['manufacturedProduct']['manufacturedMaterial']['code']['@attributes']);
				$medication->RXCUI = $code['code'];
				$medication->CODE = $code['code'];
				$medication->STR = $code['code_text'];
				unset($code);
			}

			if(isset($medication->RXCUI) && $medication->RXCUI != ''){

				// ndc
				$ndc = $this->Rxnorm->getNDCByRxCUI($medication->RXCUI);
				if($ndc !== false) $medication->NDC = $ndc;

				// gs_code
				$gs_code = $this->Rxnorm->getGsCodeByRxCUI($medication->RXCUI);
				if($gs_code !== false) $medication->GS_CODE = $gs_code;
			}

			// route
            if (isset($entry['substanceAdministration']['routeCode']['@attributes']['code'])) {
                $code = $this->codeHandler($entry['substanceAdministration']['routeCode']['@attributes']);
                $medication->route = $code['code'];
                unset($code);
            }
            // dose
            if (isset($entry['substanceAdministration']['doseQuantity']['@attributes']['value'])) {
                $medication->dose = $entry['substanceAdministration']['doseQuantity']['@attributes']['value'];
            }

			// instructions...
            if(isset($entry['substanceAdministration']['entryRelationship'])) {
                $administrationRels = $entry['substanceAdministration']['entryRelationship'];
                $administrationRels = isset($administrationRels[0]) ? $administrationRels : [$administrationRels];
                foreach ($administrationRels as $administrationRel) {
                    if (
                        isset($medication->directions) ||
                        !isset($administrationRel['supply']['entryRelationship']['act']['code']['@attributes']['code']) ||
                        !isset($administrationRel['supply']['entryRelationship']['act']['text']) ||
                        $administrationRel['supply']['entryRelationship']['act']['code']['@attributes']['code'] != '409073007'
                    ) continue;

                    $medication->directions = $administrationRel['supply']['entryRelationship']['act']['text'];
                }
            }

			if($medication->end_date && $medication->end_date != '0000-00-00'){
				$medication->created_date = $medication->end_date . ' 00:00:00';
				$medication->update_date = $medication->end_date . ' 00:00:00';;
			}else{
				$medication->created_date = $medication->begin_date . ' 00:00:00';;
				$medication->update_date = $medication->begin_date . ' 00:00:00';;
			}

			$medication->source = $author->lname  . (isset($author->fname) ? (', ' . $author->fname) : '');

			$medications[] = $medication;
		}

		return $medications;
	}

	/**
	 * @return array
	 */
	function getProblems($author)
	{
		$problems = [];

		if (!isset($this->index['problems'])) {
			return $problems;
		}

		$section = $this->document['ClinicalDocument']['component']['structuredBody']['component'][$this->index['problems']]['section'];

		if (!isset($section['entry'])) {
			return $problems;
		}

		if ($this->isAssoc($section['entry']))
			$section['entry'] = [$section['entry']];
		foreach ($section['entry'] as $entry) {
			$problem = new stdClass();

			if (!isset($entry['act']['entryRelationship']['observation']['value'])) continue;

			$code = $this->codeHandler($entry['act']['entryRelationship']['observation']['value']);
			$problem->code = $code['code'];
			$problem->code_text = $code['code_text'];
			$problem->code_type = $code['code_type'];
			unset($code);


			if(
				isset($entry['act']['entryRelationship']['observation']['author']['time']['@attributes']['value'])
			) {
				$problem->create_date = date('Y-m-d H:i:s');
				$problem->update_date = date('Y-m-d H:i:s');
				$problem->begin_date = $this->dateHandler($entry['act']['entryRelationship']['observation']['author']['time']['@attributes']['value']);
			}elseif (isset($entry['act']['effectiveTime'])) {
				$dates = $this->datesHandler($entry['act']['effectiveTime'], true);
				$problem->create_date = date('Y-m-d H:i:s');
				$problem->update_date = date('Y-m-d H:i:s');
				$problem->begin_date = $dates['low'];
				$problem->end_date = $dates['high'];
				unset($dates);
			}

			if (isset($entry['act']['entryRelationship']['observation']['entryRelationship'])) {

				$entryRelationships = $entry['act']['entryRelationship']['observation']['entryRelationship'];
				$entryRelationships = isset($entryRelationships[0]) ? $entryRelationships : [$entryRelationships];

				foreach ($entryRelationships as $rel) {

					if (
						!isset($rel['observation']['code']['@attributes']['code']) ||
						$rel['observation']['code']['@attributes']['code'] != '33999-4'
					) {
						continue;
					}

					$status = $rel['observation']['value']['@attributes'];
					$problem->status = $status['displayName'];
					$problem->status_code = $status['code'];
					$problem->status_code_type = $this->getCodeSystemName($status['codeSystem']);
				}
				unset($rel);
			}

			if(!isset($problem->status) &&
				(
					!isset($problem->end_date) ||
					$problem->end_date == '0000-00-00' ||
					$problem->end_date == '0000-00-00 00:00:00'
				)
			){
				$problem->status = 'Active';
				$problem->status_code = '55561003';
				$problem->status_code_type = 'SNOMEDCT';
			}else{
				$problem->status = 'Inactive';
				$problem->status_code = '73425007';
				$problem->status_code_type = 'SNOMEDCT';
			}

			$problem->occurrence = 'Unknown or N/A';
			$problem->source = $author->lname  . (isset($author->fname) ? (', ' . $author->fname) : '');


			$problems[] = $problem;
		}

		return $problems;
	}

	/**
	 * @return array
	 */
	function getProcedures()
	{
		$procedures = [];

		if (!isset($this->index['procedures'])) {
			return $procedures;
		}

		$section = $this->document['ClinicalDocument']['component']['structuredBody']['component'][$this->index['procedures']]['section'];

		if (!isset($section['entry'])) {
			return $procedures;
		}

		if ($this->isAssoc($section['entry']))
			$section['entry'] = [$section['entry']];

		foreach ($section['entry'] as $entry) {
			$procedure = new stdClass();

			if (isset($entry['procedure']['code'])) {
				// procedure
				$code = $this->codeHandler($entry['procedure']['code']);
				if ($code['code'] == '')
					continue;

				$procedure->code = $code['code'];
				$procedure->code_text = $code['code_text'];
				$procedure->code_type = $code['code_type'];

				//dates
				$dates = $this->datesHandler($entry['procedure']['effectiveTime']);
				$procedure->procedure_date = $dates['low'];
				$procedure->create_date = date('Y-m-d H:i:s');
				$procedure->update_date = date('Y-m-d H:i:s');

				$procedures[] = $procedure;
			}

		}
		return $procedures;
	}

	/**
	 * @return array
	 */
	function getResults()
	{
		$results = [];

		if (!isset($this->index['results'])) {
			return $results;
		}

		$section = $this->document['ClinicalDocument']['component']['structuredBody']['component'][$this->index['results']]['section'];

		if (!isset($section['entry'])) {
			return $results;
		}

		if ($this->isAssoc($section['entry']))
			$section['entry'] = [$section['entry']];

		foreach ($section['entry'] as $entry) {
			$result = new stdClass();

			$code = $this->codeHandler($entry['organizer']['code']);
			$result->code = $code['code'];
			$result->code_text = $code['code_text'];
			$result->code_type = $code['code_type'];
			unset($code);

			$result_date = '0000-00-00';

			$result->observations = [];

			if ($this->isAssoc($entry['organizer']['component']))
				$entry['organizer']['component'] = [$entry['organizer']['component']];

			foreach ($entry['organizer']['component'] as $obs) {

				if (isset($obs['observation'])) {
					$obs = $obs['observation'];

					$observation = new stdClass();
					$code = $this->codeHandler($obs['code']);
					$observation->code = $code['code'];
					$observation->code_text = $code['code_text'];
					$observation->code_type = $code['code_type'];
					unset($code);

					$observation->value = isset($obs['value']['@attributes']['value']) ? $obs['value']['@attributes']['value'] : '';
					$observation->units = isset($obs['value']['@attributes']['unit']) ? $obs['value']['@attributes']['unit'] : '';

					if (isset($obs['referenceRange'])) {
						$observation->reference_rage = "";
						if (isset($obs['referenceRange']['observationRange']['text'])) {
							$observation->reference_rage = $obs['referenceRange']['observationRange']['text'];
						} else {
							if (isset($obs['referenceRange']['observationRange']['value']['low'])) {
								$observation->reference_rage = $obs['referenceRange']['observationRange']['value']['low']['@attributes']['value'];
							}
							if (isset($obs['referenceRange']['observationRange']['value']['high'])) {
								$observation->reference_rage .= ' - ' . $obs['referenceRange']
									['observationRange']
									['value']
									['high']
									['@attributes']
									['value'];
							}
							$observation->reference_rage .= ' ' . $observation->units;
						}
					}

					$dates = $this->datesHandler($obs['effectiveTime']);
					$observation->date_analysis = $dates['low'];

					if (
						isset($obs['interpretationCode']) &&
						isset($obs['interpretationCode']['@attributes']) &&
						isset($obs['interpretationCode']['@attributes']['code'])
					) {
						$observation->abnormal_flag = $obs['interpretationCode']['@attributes']['code'];
					} else {
						$observation->abnormal_flag = '';
					}

					if (
						isset($obs['statusCode']) &&
						isset($obs['statusCode']['@attributes']) &&
						isset($obs['statusCode']['@attributes']['code'])
					) {
						$observation->observation_result_status = $obs['statusCode']['@attributes']['code'];
					} else {
						$observation->observation_result_status = '';
					}

					$dates = $this->datesHandler($obs['effectiveTime']);
					$observation->date_observation = $result_date = $dates['low'];

					$result->observations[] = $observation;

				} elseif (isset($obs['procedure'])) {
					//TODO Finish me!.
				}
			}
			$result->result_date = $result_date;
			$results[] = $result;
		}

		return $results;
	}

	/**
	 * @return array
	 */
	function getEncounters()
	{
		$encounters = [];

		if (!isset($this->index['encounters'])) {
			return $encounters;
		}

		$section = $this->document['ClinicalDocument']['component']['structuredBody']['component'][$this->index['encounters']]['section'];

		if (!isset($section['entry'])) {
			return $encounters;
		}

		if ($this->isAssoc($section['entry']))
			$section['entry'] = [$section['entry']];

		foreach ($section['entry'] as $entry) {

			if (!isset($entry['encounter']['entryRelationship']))
				continue;

			$encounter = new stdClass();

			$dates = $this->datesHandler($entry['encounter']['effectiveTime']);
			$encounter->service_date = $dates['low'];
			unset($dates);

			$code = $this->codeHandler($entry['encounter']['code']);
			$encounter->service_code = $code['code'];
			$encounter->service_code_text = $code['code_text'];
			$encounter->service_code_type = $code['code_type'];
			unset($code);

			$encounter->observations = [];

			if ($this->isAssoc($entry['encounter']['entryRelationship'])) {
				$entry['encounter']['entryRelationship'] = [$entry['encounter']['entryRelationship']];
			};
			// for each observations
			foreach ($entry['encounter']['entryRelationship'] as $obs) {

				if (isset($obs['observation'])) {
					$obs = $obs['observation'];
				} elseif (isset($obs['act'])) {
					$obs = $obs['act']['entryRelationship']['observation'];
				}

				$observation = new stdClass();

				$code = $this->codeHandler($obs['code']);
				$observation->code = $code['code'];
				$observation->code_text = $code['code_text'];
				$observation->code_type = $code['code_type'];
				unset($code);

				$code = $this->codeHandler($obs['value']);
				$observation->value_code = $code['code'];
				$observation->value_code_text = $code['code_text'];
				$observation->value_code_type = $code['code_type'];
				unset($code);

				$encounter->observations[] = $observation;

			}

			$encounters[] = $encounter;
		}

		return $encounters;
	}

	/**
	 * @return array
	 */
	function getAdvanceDirectives()
	{
		$directives = [];

		if (!isset($this->index['advancedirectives'])) {
			return $directives;
		}

		$section = $this->document['ClinicalDocument']['component']['structuredBody']['component'][$this->index['advancedirectives']]['section'];

		if (!isset($section['entry'])) {
			return $directives;
		}

		if ($this->isAssoc($section['entry']))
			$section['entry'] = [$section['entry']];

		foreach ($section['entry'] as $entry) {
			$directive = new stdClass();

			$code = $this->codeHandler($entry['observation']['code']);
			$directive->code = $code['code'];
			$directive->code_text = $code['code_text'];
			$directive->code_type = $code['code_type'];
			unset($code);

			$code = $this->codeHandler($entry['observation']['value']);
			$directive->value_code = $code['code'];
			$directive->value_code_text = $code['code_text'];
			$directive->value_code_type = $code['code_type'];
			unset($code);

			$dates = $this->datesHandler($entry['observation']['effectiveTime'], true);
			$directive->begin_date = $dates['low'];
			$directive->end_date = $dates['high'];

			if (isset($entry['observation']['participant'])) {
				if ($this->isAssoc($entry['observation']['participant'])) {
					$entry['observation']['participant'] = [$entry['observation']['participant']];
				}

				$directive->contact = '';

				foreach ($entry['observation']['participant'] as $participant) {
					$participant = $participant['participantRole'];

					if (isset($participant['playingEntity']) && (isset($participant['addr']) || isset($participant['telecom']))) {

						if (isset($participant['addr'])) {
							$address = isset($participant['addr']['streetAddressLine']) ? $participant['addr']['streetAddressLine'] : '';
							$address .= isset($participant['addr']['city']) ? ' ' . $participant['addr']['city'] : '';
							$address .= isset($participant['addr']['state']) ? ', ' . $participant['addr']['state'] : '';
							$address .= isset($participant['addr']['postalCode']) ? ' ' . $participant['addr']['postalCode'] : '';
							$address .= isset($participant['addr']['country']) ? ' ' . $participant['addr']['country'] : '';
						}

						$tel = isset($participant['telecom']) ? $participant['telecom']['@attributes']['value'] : '';

						$name = $this->namesHandler($participant['playingEntity']['name']);
						$directive->contact = $name['L']['prefix'] . ' ' . $name['L']['lname'] . ' ' . $name['L']['fname'] . $name['L']['mname'] . ' ~ ' . $tel . $address;

					}

				}
			}

			$directives[] = $directive;
		}

		return $directives;
	}

	/**
	 * @param $array
	 * @return string
	 */
	function ArrayToJson($array)
	{
		return json_encode($array);
	}

	/**
	 * @param $xml
	 * @return string
	 */
	function XmlToJson($xml)
	{
		return $this->ArrayToJson($this->XmlToArray($xml));
	}

	/**
	 * @param $xml
	 * @return DOMDocument
	 */
	function XmlToArray($xml)
	{
		return XML2Array::createArray($xml);
	}

	/**
	 * @param $telecoms
	 * @return array
	 */
	function telecomHandler($telecoms)
	{
		$telecoms = !$this->isAssoc($telecoms) ? $telecoms : [$telecoms];
		$results = [];
		foreach ($telecoms as $telecom) {
			$use = isset($telecom['@attributes']['use']) && $telecom['@attributes']['use'] != '' ? $telecom['@attributes']['use'] : 'HP';
			$results[$use] = isset($telecom['@attributes']['value']) ? $this->parsePhone($telecom['@attributes']['value']) : '';
		}
		return $results;
	}

	/**
	 * @param $names
	 * @return array
	 */
	function namesHandler($names)
	{
	    $parsed_name = [
            'L' => [
                'prefix' => '',
                'fname' => '',
                'mname' => '',
                'lname' => '',
            ],
            'BR' => [
                'prefix' => '',
                'fname' => '',
                'mname' => '',
                'lname' => '',
            ]
        ];

        if(!isset($names)){
            return $parsed_name;
        }

        if(isset($names[0])){
            foreach ($names as $name){
                $this->nameHandler($name, $parsed_name, true);
            }
        }else{
            $this->nameHandler($names, $parsed_name, false);
        }

		return $parsed_name;
	}

	function nameHandler($name, &$parsed_name, $has_multiple)
    {

        $use = isset($name['@attributes']['use']) ? $name['@attributes']['use'] : '';
        $is_primary = !$has_multiple || $use == 'L';

        $foo = [];

        $parsed_name['L']['prefix'] = isset($name['prefix']) && is_string($name['prefix']) ? $name['prefix'] : '';

        if (is_array($name['given'])) {
            foreach ($name['given'] as $given) {

                $foo['value'] = isset($given['@value']) ? $given['@value'] : $given;
                $foo['qualifier'] = isset($given['@attributes']['qualifier']) ? $given['@attributes']['qualifier'] : '';

                if($is_primary){
                    if ($parsed_name['L']['fname'] === '') {
                        $parsed_name['L']['fname'] = $foo['value'];
                    } else if($parsed_name['L']['mname'] == '') {
                        $parsed_name['L']['mname'] = $foo['value'];
                    }
                }else if($foo['qualifier'] == 'BR'){
                    if ($parsed_name['BR']['fname'] === '') {
                        $parsed_name['BR']['fname'] = $foo['value'];
                    } else if($parsed_name['L']['mname'] == '') {
                        $parsed_name['BR']['mname'] = $foo['value'];
                    }
                }
            }
        } else {
            $given = $name['given'];
            $foo['value'] = isset($given['@value']) ? $given['@value'] : $given;
            $foo['qualifier'] = isset($given['@attributes']['qualifier']) ? $given['@attributes']['qualifier'] : '';

            if($is_primary){
                if ($parsed_name['L']['fname'] === '') {
                    $parsed_name['L']['fname'] = $foo['value'];
                } else if($parsed_name['L']['mname'] == '') {
                    $parsed_name['L']['mname'] = $foo['value'];
                }
            }else if($foo['qualifier'] == 'BR'){
                if ($parsed_name['BR']['fname'] === '') {
                    $parsed_name['BR']['fname'] = $foo['value'];
                } else if($parsed_name['L']['mname'] == '') {
                    $parsed_name['BR']['mname'] = $foo['value'];
                }
            }
        }

        if (is_array($name['family'])) {
            foreach ($name['family'] as $family) {
                $foo['value'] = isset($family['@value']) ? $family['@value'] : $family;
                $foo['qualifier'] = isset($family['@attributes']['qualifier']) ? $family['@attributes']['qualifier'] : '';

                if($is_primary){
                    $parsed_name['L']['lname'] = $foo['value'];
                }else if($foo['qualifier'] == 'BR'){
                    $parsed_name['BR']['lname'] = $foo['value'];
                }
            }
        } else {
            $family = $name['family'];
            $foo['value'] = isset($family['@value']) ? $family['@value'] : $family;
            $foo['qualifier'] = isset($family['@attributes']['qualifier']) ? $family['@attributes']['qualifier'] : '';

            if($is_primary){
                $parsed_name['L']['lname'] = $foo['value'];
            }else if($foo['qualifier'] == 'BR'){
                $parsed_name['BR']['lname'] = $foo['value'];
            }
        }

//        if($parsed_name['BR']['fname'] === ''){
//            $parsed_name['BR']['fname'] = $parsed_name['L']['fname'];
//        }
//        if($parsed_name['BR']['mname'] === ''){
//            $parsed_name['BR']['mname'] = $parsed_name['L']['mname'];
//        }
//        if($parsed_name['BR']['lname'] === ''){
//            $parsed_name['BR']['lname'] = $parsed_name['L']['lname'];
//        }

        return;

    }

	/**
	 * @param $dates
	 * @param $justDate
	 * @return array
	 */
	function datesHandler($dates, $justDate = false)
	{
		$result = [
			'low' => '0000-00-00',
			'high' => '0000-00-00'
		];

		if (is_string($dates)) {
			$result['low'] = $this->dateParser($dates);
		} else {
			if (isset($dates['@value'])) {
				$result['low'] = $this->dateHandler($dates);
			} else {
				if (isset($dates['low'])) {
					$result['low'] = $this->dateHandler($dates['low']);
				}
				if (isset($dates['high'])) {
					$result['high'] = $this->dateHandler($dates['high']);
				}
			}
		}

		if ($justDate) {
			$result['low'] = substr($result['low'], 0, 10);
			$result['high'] = substr($result['high'], 0, 10);
		}

		return $result;
	}

	/**
	 * @param $date
	 * @return mixed|string
	 */
	function dateHandler($date)
	{
		$result = '0000-00-00';
		if (is_string($date)) {
			$result = $this->dateParser($date);
		} elseif (isset($date['@attributes']['value'])) {
			$result = $this->dateParser($date['@attributes']['value']);
		}
		return $result;
	}

	/**
	 * @param $code
	 * @return array
	 */
	function codeHandler($code)
	{
		if (isset($code['@attributes'])) {
			return $this->codeHandler($code['@attributes']);
		}
		$result = [];
		$result['code'] = isset($code['code']) ? $code['code'] : '';
		$result['code_type'] = isset($code['codeSystem']) ? $this->getCodeSystemName($code['codeSystem']) : '';
		$result['code_text'] = isset($code['displayName']) ? $code['displayName'] : '';

		if ($result['code_text'] == '') {

			if ($result['code_type'] == 'SNOMEDCT') {

				if (!isset($this->SnomedCodes)) {
					$this->SnomedCodes = new SnomedCodes();
				}
				$text = $this->SnomedCodes->getSnomedTextByConceptId($result['code']);
				$result['code_text'] = $text;

			} elseif ($result['code_type'] == 'LOINC') {

				//TODO

			}

		}

		return $result;
	}

	/**
	 * @param $date
	 * @return mixed|string
	 */
	function dateParser($date)
	{
		$result = '0000-00-00';
		switch (strlen($date)) {
			case 4:
				$result = $date . '-00-00';
				break;
			case 6:
				$result = preg_replace('/^(\d{4})(\d{2})/', '$1-$2-00', $date);
				break;
			case 8:
				$result = preg_replace('/^(\d{4})(\d{2})(\d{2})$/', '$1-$2-$3', $date);
				break;
			case 10:
				$result = preg_replace('/^(\d{4})(\d{2})(\d{2})(\d{2})$/', '$1-$2-$3 $4:00:00', $date);
				break;
			case 12:
				$result = preg_replace('/^(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})/', '$1-$2-$3 $4:$5:00', $date);
				break;
			case 14:
				$result = preg_replace('/^(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/', '$1-$2-$3 $4:$5:$6', $date);
				break;
		}

		return $result;
	}

	/**
	 * @param $phone
	 * @return mixed
	 */
	function parsePhone($phone)
	{
		return preg_replace('/tel:/', '', $phone);
	}

	/**
	 * @param $arr
	 * @return bool
	 */
	function isAssoc($arr)
	{
		return array_keys($arr) !== range(0, count($arr) - 1);
	}

	/**
	 * @param $code
	 * @return string
	 */
	function getCodeSystemName($code)
	{
		$code = str_replace('.', '', $code);
		$codes = [
			'2168401113883612' => 'CPT4',
			'2168401113883642' => 'ICD9',
			'21684011138836103' => 'ICD9CM',
			'216840111388363' => 'ICD10',
			'216840111388361' => 'LOINC',
			'216840111388366' => 'NDC',
			'2168401113883688' => 'RXNORM',
			'2168401113883696' => 'SNOMEDCT',
			'216840111388346' => 'NPI',
			'216840111388349' => 'UNII',
			'216840111388332611' => 'NCI'
		];

		return isset($codes[$code]) ? $codes[$code] : 'UNK';
	}

	function getTestCCD($file)
	{
		$ccd = file_get_contents(ROOT . '/dataProvider/CCDs/' . $file);
		$this->setDocument($ccd);
		return ['ccd' => $this->getDocument()];
	}
}


