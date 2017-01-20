<?php
/**
* mdTimeLine EHR (Electronic Health Records)
* mdTimeLine (C) 2017
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

class CCRDocumentParse {

private $document;

private $index;

public $styledXml;

/**
 * @var SnomedCodes
 */
private $SnomedCodes;

function __construct($xml = null) {
    if(isset($xml)) $this->setDocument($xml);
}

function setDocument($xml) {
    $this->document = $this->XmlToArray($xml);

    Array2XML::init('1.0',
                    'UTF-8',
                    true,
                    ['xml-stylesheet' => 'type="text/xsl" href="' . URL . '/lib/CCRCDA/schema/cda2.xsl"']
    );

    $data = [
        '@attributes' => [
            'xmlns' => 'urn:hl7-org:v3',
            'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
            'xsi:schemaLocation' => 'urn:hl7-org:v3 CDA.xsd'
        ]
    ];
    foreach($this->document['ccr:ContinuityOfCareRecord']['ccr:Body'] as $index => $components){
        $data[$index] = $components;
    }

    // Building the document
    $this->styledXml = Array2XML::createXML('ClinicalDocument', $data)->saveXML();
    unset($data);

    $this->index = [];
    foreach($this->document['ccr:ContinuityOfCareRecord']['ccr:Body'] as $index => $component){
        $code = isset($component['section']['code']['@attributes']['code']) ? $component['section']['code']['@attributes']['code'] : '';

        //Advance Directives ???
        switch($code){
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
                if($tplId == '2.16.840.1.113883.10.20.22.2.21.1') $this->index['advancedirectives'] = $index;
                break;
        }
    }
}

    function parseDocument($xml = null) {
        if(isset($xml)) $this->setDocument($xml);
        return $this->getDocument();
    }

    function getDocument() {
        $document = new stdClass();
        $document->title = $this->getTitle();
        $document->patient = $this->getPatient();
        $document->encounter = $this->getEncounter();
        $document->author = $this->getAuthor();
        $document->allergies = $this->getAllergies();
        $document->medications = $this->getMedications();
        $document->problems = $this->getProblems();
        $document->procedures = $this->getProcedures();
        $document->results = $this->getResults();
        $document->encounters = $this->getEncounters();
        $document->advancedirectives = $this->getAdvanceDirectives();
        return $document;
    }

    /**
     * getTitle
     * Get the Title from the CCR Document
     * @return string
     */
    function getTitle() {
        return isset($this->document['ccr:ContinuityOfCareRecord']['ccr:Purpose']['ccr:Description']['ccr:Description']) ?
            $this->document['ccr:ContinuityOfCareRecord']['ccr:Purpose']['ccr:Description']['ccr:Description'] :
            '';
    }

    /**
     * getPatient
     * Get the patient information from the CCR Document
     * @return mixed
     * @throws Exception
     */
    function getPatient() {

        if(empty($this->document['ccr:ContinuityOfCareRecord']['ccr:Patient']['ccr:ActorID']))
            throw new Exception('Error: ClinicalDocument->recordTarget->patientRole->Patient is required');

        $patient = new stdClass();

        // Patient ID
        // We also need this actor id, to extract the actor information
        $patient->pubpid = $this->document['ccr:ContinuityOfCareRecord']['ccr:Patient']['ccr:ActorID'];

        foreach($this->document['ccr:ContinuityOfCareRecord']['ccr:Actors']['ccr:Actors'] as $Actor)
        {
            if($Actor['ccr:ActorObjectID'] == $patient->pubpid){
                // Patient address
                $patient->address = isset($Actor['ccr:Address']['ccr:Line1']) ? $Actor['ccr:Address']['ccr:Line1'] : '';
                $patient->city = isset($Actor['ccr:Address']['ccr:City']) ? $Actor['ccr:Address']['ccr:City'] : '';
                $patient->state = isset($Actor['ccr:Address']['ccr:State']) ? $Actor['ccr:Address']['ccr:State'] : '';
                $patient->zipcode = isset($Actor['ccr:Address']['ccr:PostalCode']) ? $Actor['ccr:Address']['ccr:PostalCode'] : '';
                $patient->country = '';

                // Patient phones
                $patient->home_phone = isset($Actor['ccr:Telephone']['ccr:Value']) ? $Actor['ccr:Telephone']['ccr:Value'] : '';

                //Patient Name - Validate
                if(!isset($Actor['ccr:Person']['ccr:Name']['ccr:BirthName']['ccr:Given']))
                    throw new Exception('Error: Patient given name is required');
                if(!isset($Actor['ccr:Person']['ccr:Name']['ccr:BirthName']['ccr:Family']))
                    throw new Exception('Error: Patient family name is required');
                //Patient Name
                $patient->fname =  isset($Actor['ccr:Person']['ccr:Name']['ccr:BirthName']['ccr:Family']) ? $Actor['ccr:Person']['ccr:Name']['ccr:BirthName']['ccr:Family'] : '';
                $patient->mname =  isset($Actor['ccr:Person']['ccr:Name']['ccr:BirthName']['ccr:Given'][0]) ? $Actor['ccr:Person']['ccr:Name']['ccr:BirthName']['ccr:Given'][0] : '';
                $patient->lname =  isset($Actor['ccr:Person']['ccr:Name']['ccr:BirthName']['ccr:Given'][1]) ? $Actor['ccr:Person']['ccr:Name']['ccr:BirthName']['ccr:Given'][1] : '';
                $patient->name = Person::fullname($patient->fname, $patient->mname, $patient->lname);

                // Gender - Validate
                if(!isset($Actor['ccr:Person']['ccr:Gender']['ccr:Code']['ccr:Value']))
                    throw new Exception('Error: Patient gender is required');
                // Gender
                $patient->sex = $Actor['ccr:Person']['ccr:Gender']['ccr:Code']['ccr:Value'];

                // DOB
                $patient->DOB = $this->dateParser($Actor['ccr:Person']['ccr:DateOfBirth']['ccr:ExactDateTime']);
                // FIX: For date with only the day...  add the time at the end
                if(strlen($patient->DOB) <= 10) $patient->DOB .= ' 00:00:00';

                // Marital StatusCode
                $patient->marital_status = '';

                // Race
                $patient->race = '';

                // Ethinicity
                $patient->ethnicity = '';

                // Labguage Communication
                $patient->language = '';
            }
        }
        unset($Actor);
        return $patient;
    }

    /**
     * @return stdClass
     */
    function getAuthor() {
        $author = new stdClass();

        if(isset($this->document['ccr:ContinuityOfCareRecord']['ccr:From']['ccr:ActorLink']['ccr:ActorID'])){

            $author->id = $this->document['ccr:ContinuityOfCareRecord']['ccr:From']['ccr:ActorLink']['ccr:ActorID'];

            foreach($this->document['ccr:ContinuityOfCareRecord']['ccr:Actors']['ccr:Actors'] as $Actor){
                if($Actor['ccr:ActorObjectID'] == $author->id) {

                    // Authos name
                    $author->fname = $Actor['ccr:Person']['ccr:Name']['ccr:BirthName']['ccr:Given'];
                    $author->mname = $Actor['ccr:Person']['ccr:Name']['ccr:BirthName']['ccr:Family'];
                    $author->lname = '';

                    // Author address
                    $author->address = isset($Actor['ccr:Address']['ccr:Line1']) ? $Actor['ccr:Address']['ccr:Line1'] : '';
                    $author->city = isset($Actor['ccr:Address']['ccr:City']) ? $Actor['ccr:Address']['ccr:City'] : '';
                    $author->state = isset($Actor['ccr:Address']['ccr:State']) ? $Actor['ccr:Address']['ccr:State'] : '';
                    $author->zipcode = isset($Actor['ccr:Address']['ccr:PostalCode']) ? $Actor['ccr:Address']['ccr:PostalCode'] : '';
                    $author->country = '';

                    // Author phone
                    $author->work_phone = isset($Actor['ccr:Telephone']['ccr:Value']) ? $this->telecomHandler($Actor['ccr:Telephone']['ccr:Value']) : '';
                }
            }
        }

        return $author;
    }

    /**
     * getEncounter
     * Get the encounters from the CCR Document
     * @return stdClass
     */
    function getEncounter() {
        $encounter = new stdClass();

        return $encounter;
    }

    /**
     * getAllergies
     * Get the allergies from the CCR Document
     * @return array
     */
    function getAllergies() {
        $allergies = [];

        return $allergies;

    }

    /**
     * getMedications
     * Get the medications from the CCR Document
     * @return array
     */
    function getMedications() {
        $medications = [];

        if(!isset($this->index['medications']) || !isset($this->document['ccr:Body']['ccr:Medications'])){
            return $medications;
        }

        foreach($this->document['ccr:Body']['ccr:Medications']['ccr:Medication'] as $Medication){
            $medication = new stdClass();
            $medication->begin_date = $Medication['ccr:DateTime']['ccr:ApproximateDateTime']['ccr:Text'];
            $medication->end_date = '';
            $medication->STR = $Medication['ccr:Product']['ccr:ProductName']['ccr:Text'];
            $medication->RXCUI = $Medication['ccr:Product']['ccr:ProductName']['ccr:Code']['ccr:Value'];
            $medications[] = $medication;
        }

        return $medications;
    }

    /**
     * getProblems
     * Get patient problems from the CCR Document
     * @return array
     */
    function getProblems() {
        $problems = [];

        if(!isset($this->index['problems']) || !isset($this->document['ccr:Body']['ccr:Problems'])){
            return $problems;
        }

        foreach($this->document['ccr:Body']['ccr:Medications']['ccr:Medication'] as $Problem){
            $problem = new stdClass();
            $problem->begin_date = $Problem['ccr:DateTime']['ccr:ApproximateDateTime']['ccr:Text'];
            $problem->end_date = '';
            $problem->code_text = $Problem['ccr:Description']['ccr:Text'];
            $problem->code = $Problem['ccr:Description']['ccr:Code'][0]['ccr:Value'];
            $problem->code_type = $Problem['ccr:Description']['ccr:Code'][0]['ccr:CodingSystem'];
            $problems[] = $problem;
        }

        return $problems;
    }

    /**
     * getProcedures
     * Get patient procedures from the CCR Document
     * @return array
     */
    function getProcedures() {
        $procedures = [];

        if(!isset($this->index['procedures'])){
            return $procedures;
        }

        return $procedures;
    }

/**
 * @return array
 */
function getResults() {
    $results = [];

    if(!isset($this->index['results'])){
        return $results;
    }

    $section = $this->document['ClinicalDocument']['component']['structuredBody']['component'][$this->index['results']]['section'];

    if(!isset($section['entry'])){
        return $results;
    }

    if($this->isAssoc($section['entry']))
        $section['entry'] = [$section['entry']];

    foreach($section['entry'] as $entry){
        $result = new stdClass();

        $code = $this->codeHandler($entry['organizer']['code']);
        $result->code = $code['code'];
        $result->code_text = $code['code_text'];
        $result->code_type = $code['code_type'];
        unset($code);

        $result_date = '0000-00-00';

        $result->observations = [];

        if($this->isAssoc($entry['organizer']['component']))
            $entry['organizer']['component'] = [$entry['organizer']['component']];

        foreach($entry['organizer']['component'] as $obs){

            if(isset($obs['observation'])){
                $obs = $obs['observation'];

                $observation = new stdClass();
                $code = $this->codeHandler($obs['code']);
                $observation->code = $code['code'];
                $observation->code_text = $code['code_text'];
                $observation->code_type = $code['code_type'];
                unset($code);

                $observation->value = isset($obs['value']['@attributes']['value']) ? $obs['value']['@attributes']['value'] : '';
                $observation->units = isset($obs['value']['@attributes']['unit']) ? $obs['value']['@attributes']['unit'] : '';

                if(isset($obs['referenceRange'])){
                    $observation->reference_rage = "";
                    if(isset($obs['referenceRange']['observationRange']['text'])){
                        $observation->reference_rage = $obs['referenceRange']['observationRange']['text'];
                    } else {
                        if(isset($obs['referenceRange']['observationRange']['value']['low'])){
                            $observation->reference_rage = $obs['referenceRange']['observationRange']['value']['low']['@attributes']['value'];
                        }
                        if(isset($obs['referenceRange']['observationRange']['value']['high'])){
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

                if(
                    isset($obs['interpretationCode']) &&
                    isset($obs['interpretationCode']['@attributes']) &&
                    isset($obs['interpretationCode']['@attributes']['code'])
                ){
                    $observation->abnormal_flag = $obs['interpretationCode']['@attributes']['code'];
                } else {
                    $observation->abnormal_flag = '';
                }

                if(
                    isset($obs['statusCode']) &&
                    isset($obs['statusCode']['@attributes']) &&
                    isset($obs['statusCode']['@attributes']['code'])
                ){
                    $observation->observation_result_status = $obs['statusCode']['@attributes']['code'];
                } else {
                    $observation->observation_result_status = '';
                }

                $dates = $this->datesHandler($obs['effectiveTime']);
                $observation->date_observation = $result_date = $dates['low'];

                $result->observations[] = $observation;

            }elseif(isset($obs['procedure'])){
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
function getEncounters() {
    $encounters = [];

    if(!isset($this->index['encounters'])){
        return $encounters;
    }

    $section = $this->document['ClinicalDocument']['component']['structuredBody']['component'][$this->index['encounters']]['section'];

    if(!isset($section['entry'])){
        return $encounters;
    }

    if($this->isAssoc($section['entry']))
        $section['entry'] = [$section['entry']];

    foreach($section['entry'] as $entry){

        if(!isset($entry['encounter']['entryRelationship']))
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

        if($this->isAssoc($entry['encounter']['entryRelationship'])){
            $entry['encounter']['entryRelationship'] = [$entry['encounter']['entryRelationship']];
        };
        // for each observations
        foreach($entry['encounter']['entryRelationship'] as $obs){

            if(isset($obs['observation'])){
                $obs = $obs['observation'];
            } elseif(isset($obs['act'])) {
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
function getAdvanceDirectives() {
    $directives = [];

    if(!isset($this->index['advancedirectives'])){
        return $directives;
    }

    $section = $this->document['ClinicalDocument']['component']['structuredBody']['component'][$this->index['advancedirectives']]['section'];

    if(!isset($section['entry'])){
        return $directives;
    }

    if($this->isAssoc($section['entry']))
        $section['entry'] = [$section['entry']];

    foreach($section['entry'] as $entry){
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

        if(isset($entry['observation']['participant'])){
            if($this->isAssoc($entry['observation']['participant'])){
                $entry['observation']['participant'] = [$entry['observation']['participant']];
            }

            $directive->contact = '';

            foreach($entry['observation']['participant'] as $participant){
                $participant = $participant['participantRole'];

                if(isset($participant['playingEntity']) && (isset($participant['addr']) || isset($participant['telecom']))){

                    if(isset($participant['addr'])){
                        $address = isset($participant['addr']['streetAddressLine']) ? $participant['addr']['streetAddressLine'] : '';
                        $address .= isset($participant['addr']['city']) ? ' ' . $participant['addr']['city'] : '';
                        $address .= isset($participant['addr']['state']) ? ', ' . $participant['addr']['state'] : '';
                        $address .= isset($participant['addr']['postalCode']) ? ' ' . $participant['addr']['postalCode'] : '';
                        $address .= isset($participant['addr']['country']) ? ' ' . $participant['addr']['country'] : '';
                    }

                    $tel = isset($participant['telecom']) ? $participant['telecom']['@attributes']['value'] : '';

                    $name = $this->nameHandler($participant['playingEntity']['name']);
                    $directive->contact = $name['prefix'] . ' ' . $name['lname'] . ' ' . $name['fname'] . $name['mname'] . ' ~ ' . $tel . $address;

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
function ArrayToJson($array) {
    return json_encode($array);
}

/**
 * @param $xml
 * @return string
 */
function XmlToJson($xml) {
    return $this->ArrayToJson($this->XmlToArray($xml));
}

/**
 * @param $xml
 * @return DOMDocument
 */
function XmlToArray($xml) {
    return XML2Array::createArray($xml);
}

/**
 * @param $telecoms
 * @return array
 */
function telecomHandler($telecoms) {
    $telecoms = !$this->isAssoc($telecoms) ? $telecoms : [$telecoms];
    $results = [];
    foreach($telecoms as $telecom){
        $use = isset($telecom['@attributes']['use']) && $telecom['@attributes']['use'] != '' ? $telecom['@attributes']['use'] : 'HP';
        $results[$use] = isset($telecom['@attributes']['value']) ? $this->parsePhone($telecom['@attributes']['value']) : '';
    }
    return $results;
}

/**
 * @param $name
 * @return array
 */
function nameHandler($name) {
    $results = [];

    $results['prefix'] = isset($name['prefix']) && is_string($name['prefix']) ? $name['prefix'] : '';

    if(is_array($name['given'])){
        $results['fname'] = isset($name['given'][0]) ? $name['given'][0] : '';
        if(!isset($name['given'][1])){
            $results['mname'] = '';
        } elseif(is_string($name['given'][1])) {
            $results['mname'] = isset($name['given'][1]) ? $name['given'][1] : '';
        } elseif(is_array($name['given'][1])) {
            $results['mname'] = isset($name['given'][1]['@value']) ? $name['given'][1]['@value'] : '';
        }
    } else {
        $results['fname'] = isset($name['given']) ? $name['given'] : '';
        $results['mname'] = '';
    }

    $results['lname'] = isset($name['family']) ? $name['family'] : '';
    return $results;
}

/**
 * @param $dates
 * @param $justDate
 * @return array
 */
function datesHandler($dates, $justDate = false) {
    $result = [
        'low' => '0000-00-00',
        'high' => '0000-00-00'
    ];

    if(is_string($dates)){
        $result['low'] = $this->dateParser($dates);
    } else {
        if(isset($dates['@value'])){
            $result['low'] = $this->dateHandler($dates);
        } else {
            if(isset($dates['low'])){
                $result['low'] = $this->dateHandler($dates['low']);
            }
            if(isset($dates['high'])){
                $result['high'] = $this->dateHandler($dates['high']);
            }
        }
    }

    if($justDate){
        $result['low'] = substr($result['low'], 0, 10);
        $result['high'] = substr($result['high'], 0, 10);
    }

    return $result;
}

/**
 * @param $date
 * @return mixed|string
 */
function dateHandler($date) {
    $result = '0000-00-00';
    if(is_string($date)){
        $result = $this->dateParser($date);
    } elseif(isset($date['@attributes']['value'])) {
        $result = $this->dateParser($date['@attributes']['value']);
    }
    return $result;
}

/**
 * @param $code
 * @return array
 */
function codeHandler($code) {
    if(isset($code['@attributes'])){
        return $this->codeHandler($code['@attributes']);
    }
    $result = [];
    $result['code'] = isset($code['code']) ? $code['code'] : '';
    $result['code_type'] = isset($code['codeSystem']) ? $this->getCodeSystemName($code['codeSystem']) : '';
    $result['code_text'] = isset($code['displayName']) ? $code['displayName'] : '';

    if($result['code_text'] == ''){

        if($result['code_type'] == 'SNOMEDCT'){

            if(!isset($this->SnomedCodes)){
                $this->SnomedCodes = new SnomedCodes();
            }
            $text = $this->SnomedCodes->getSnomedTextByConceptId($result['code']);
            $result['code_text'] = $text;

        } elseif($result['code_type'] == 'LOINC') {

            //TODO

        }

    }

    return $result;
}

/**
 * @param $date
 * @return mixed|string
 */
function dateParser($date) {
    $result = '0000-00-00';
    switch(strlen($date)) {
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
function parsePhone($phone) {
    return preg_replace('/tel:/', '', $phone);
}

/**
 * @param $arr
 * @return bool
 */
function isAssoc($arr) {
    return array_keys($arr) !== range(0, count($arr) - 1);
}

/**
 * @param $code
 * @return string
 */
function getCodeSystemName($code) {
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

function getTestCCD($file) {

    $ccd = file_get_contents(ROOT . '/dataProvider/CCDs/' . $file);
    $this->setDocument($ccd);
    return ['ccd' => $this->getDocument()];
}
}


