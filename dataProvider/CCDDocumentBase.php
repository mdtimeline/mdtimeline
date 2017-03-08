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

class CDDDocumentBase
{

    /**
     * @var int
     */
    public $pid = null;
    /**
     * @var int
     */
    public $eid = null;
    /**
     * @var string
     */
    public $dateNow;
    /**
     * @var string
     */
    public $timeNow;
    /**
     * @var Encounter
     */
    public $Encounter;
    /**
     * @var Facilities
     */
    public $Facilities;
    /**
     * @var CombosData
     */
    public $CombosData;
    /**
     * @var Patient
     */
    public $Patient;
    /**
     * @var TransactionLog
     */
    public $TransactionLog;
    /**
     * @var
     */
    public $PatientContacts;
    /**
     * @var User
     */
    public $User;

    /**
     * @var
     */
    public $encounter;
    /**
     * @var
     */
    public $encounterProvider;

    /**
     * @var
     */
    public $encounterFacility;

    /**
     * @var array
     */
    public $facility;
    /**
     * @var array
     */
    public $user;
    /**
     * @var array
     */
    public $primaryProvider;
    /**
     * @var DomDocument
     */
    public $xml;
    /**
     * @var array
     */
    public $xmlData;
    /**
     * @var string toc | ocv | soc
     */
    public $template = 'toc'; // transition of care
    /**
     * @var array
     */
    public $templateIds = [
        'toc' => '2.16.840.1.113883.10.20.22.1.1',
        // transition of Care
        'cov' => '2.16.840.1.113883.10.20.22.1.1',
        // Clinical Office Visit
        'soc' => '2.16.840.1.113883.10.20.22.1.1',
        // Summary of Care
        'ps' => '2.16.840.1.113883.3.88.11.32.1'
        // Patient Summary
    ];

    /**
     * @var array
     */
    public $patientData;
    /**
     * @var bool
     */
    public $requiredAllergies;
    /**
     * @var bool
     */
    public $requiredVitals;
    /**
     * @var bool
     */
    public $requiredImmunization;
    /**
     * @var bool
     */
    public $requiredMedications;
    /**
     * @var bool
     */
    public $requiredProblems;
    /**
     * @var bool
     */
    public $requiredProcedures;
    /**
     * @var bool
     */
    public $requiredPlanOfCare;
    /**
     * @vat bool
     */
    public $requiredCareOfPlan;
    /**
     * @var bool
     */
    public $requiredResults;
    /**
     * @var bool
     */
    public $requiredEncounters;

    /**
     * @var
     */
    public $Globals;
    public $height_measure;
    public $weight_measure;

    /**
     * @var array
     */
    public $exclude = [];


    /**
     * CCDDocument constructor.
     */
    function __construct()
    {
        $this->dateNow = date('Ymd');
        $this->timeNow = date('YmdHisO');
        $this->Encounter = new Encounter();
        $this->Facilities = new Facilities();
        $this->CombosData = new CombosData();
        $this->User = new User();
        $this->Patient = new Patient();
        $this->TransactionLog = new TransactionLog();
        $this->PatientContacts = new PatientContacts();
        $this->Globals = new Globals();
        $this->facility = $this->Facilities->getCurrentFacility(true);

        switch($this->Globals->getGlobal('units_of_measurement'))
        {
            case 'metric':
                $this->height_measure = 'cm';
                $this->weight_measure = 'kg';
                break;
            case 'standard':
                $this->height_measure = 'in';
                $this->weight_measure = 'lbs';
                break;
        }
    }

    /**
     * Return the pertinent OID of a certain code system name
     * @param $codeSystem
     * @return string
     */
    function codes($codeSystem)
    {
        switch($codeSystem) {
            case 'CPT':
                return '2.16.840.1.113883.6.12';
                break;
            case 'CPT4':
            case 'CPT-4':
                return '2.16.840.1.113883.6.12';
                break;
            case 'ICD9':
            case 'ICD-9':
                return '2.16.840.1.113883.6.42';
                break;
            case 'ICD10':
            case 'ICD-10':
            case 'ICD10-CM':
                return '2.16.840.1.113883.6.3';
                break;
            case 'LN':
            case 'LOINC':
                return '2.16.840.1.113883.6.1';
                break;
            case 'NDC':
                return '2.16.840.1.113883.6.6';
                break;
            case 'RXNORM':
                return '2.16.840.1.113883.6.88';
                break;
            case 'SNOMED':
            case 'SNOMEDCT':
            case 'SNOMED-CT':
                return '2.16.840.1.113883.6.96';
                break;
            case 'NPI':
                return '2.16.840.1.113883.4.6';
                break;
            case 'UNII':
                return '2.16.840.1.113883.4.9';
                break;
            case 'NCI':
                return '2.16.840.1.113883.3.26.1.1';
                break;
            case 'ActPriority':
                return '2.16.840.1.113883.1.11.16866';
                break;
            case 'TAXONOMY':
                return '2.16.840.1.114222.4.11.106';
                break;
            case 'CDCREC':
            case 'PH_RaceAndEthnicity_CDC':
                return '2.16.840.1.113883.6.238';
                break;
            default:
                return '';
        }
    }

    /**
     * Method addSection()
     * @param $section
     */
    public function addSection($section)
    {
        $this->xmlData['component']['structuredBody']['component'][] = $section;
    }

    /**
     * @param $pid
     */
    public function setPid($pid)
    {
        $this->pid = $pid;
    }

    /**
     * @param $eid
     * @param $encounter_indicator
     */
    public function setEid($eid)
    {
        switch ($eid){
            case 'no_enc':
                $this->eid = $eid;
                break;
            case 'all_enc':
                $this->eid = $eid;
                break;
            default:
                $this->eid = ($eid === 'null') ? null : (int)$eid;
                break;
        }
    }

    /**
     * @param $exclude
     */
    public function setExcludes($exclude) {
        $this->exclude = $exclude == '' ? [] : explode(',',$exclude);
    }

    /**
     * @param $session
     * @return bool
     */
    public function isExcluded($session) {
        return array_search($session, $this->exclude) !== false;
    }

    /**
     * @param $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return strtolower(str_replace(
          ' ',
          '',
          $this->pid . "-" . $this->patientData['fname'] . $this->patientData['lname']
        ));
    }

    /**
     * @return mixed
     */
    public function getTemplateId()
    {
        return $this->templateIds[$this->template];
    }

    public function parseDateToText($date) {
        return date('F j, Y', strtotime($date));
    }

    public function parseDateToTextWithTime($date) {
        return date('F j, Y h:M', strtotime($date));
    }

    public function parseDate($date) {
        $dateExplode = explode(' ', $date);
        return str_replace('-', '', $dateExplode[0]);
    }

    public function telecomBuilder($number, $use = null) {
        $phone = [];

        $number = str_replace(['(',')','-',' '], '', trim($number));

        if($number != ''){
            $phone['@attributes'] = [
                'xsi:type' => 'TEL',
                'value' => 'tel:' . $number
            ];
            if(isset($use)){
                $phone['@attributes']['use'] = $use;
            }
        } else {
            $phone['@attributes']['nullFlavor'] = 'UNK';
        }
        return $phone;
    }

    public function addressBuilder(
        $use,
        $streetAddressLine,
        $city,
        $state,
        $zipcode,
        $country,
        $useablePeriod = null) {

        $addr = [];

        if($use !== false){
            $addr['@attributes']['use'] = $use;
        }

        if($streetAddressLine === false){
            // skip...
        } elseif($streetAddressLine != '') {
            $addr['streetAddressLine']['@value'] = $streetAddressLine;
        } else {
            $addr['streetAddressLine']['@attributes']['nullFlavor'] = 'NI';
        }

        if($city === false){
            // skip...
        } elseif($city != '') {
            $addr['city']['@value'] = $city;
        } else {
            $addr['city']['@attributes']['nullFlavor'] = 'UNK';
        }

        if($state === false){
            // skip...
        } elseif($state != '') {
            $addr['state']['@value'] = $state;
        } else {
            $addr['state']['@attributes']['nullFlavor'] = 'UNK';
        }

        if($zipcode === false){
            // skip...
        } elseif($zipcode != '') {
            $addr['postalCode']['@value'] = $zipcode;
        } else {
            $addr['postalCode']['@attributes']['nullFlavor'] = 'UNK';
        }

        if($country === false){
            // skip...
        } elseif($country != '') {
            $addr['country']['@value'] = $country;
        } else {
            $addr['country']['@attributes']['nullFlavor'] = 'UNK';
        }

        if(isset($useablePeriod)){
            $addr['useablePeriod']['@attributes']['xsi:type'] = 'IVL_TS';
            $addr['useablePeriod']['low']['@attributes']['nullFlavor'] = 'NA';
            $addr['useablePeriod']['high']['@attributes']['value'] = $useablePeriod;
        }

        return $addr;
    }

    /**
     * Method buildCCD()
     */
    public function createCCD()
    {
        try {

            if(!isset($this->pid)) throw new Exception('PID variable not set');

            $this->xmlData = [
                '@attributes' => [
                    'xmlns' => 'urn:hl7-org:v3',
                    'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
                    'xsi:schemaLocation' => 'urn:hl7-org:v3 CDA.xsd'
                ]
            ];

            /**
             * Note: In here we need to detect, if the user requested compile all the encounters
             */
            if(is_numeric($this->eid)){
                $this->encounter = $this->Encounter->getEncounter($this->eid, false, false);
                $this->encounter = isset($this->encounter['encounter']) ? $this->encounter['encounter'] : $this->encounter;
                $this->encounterProvider = $this->User->getUserByUid($this->encounter['provider_uid']);
                $this->encounterFacility = $this->Facilities->getFacility($this->encounter['facility']);
            }

            $this->setRequirements();
            $this->setHeader();

            /**
             * Array of sections to include in CCD
             */
            $sections = [
                'ReasonOfVisit',
                'Instructions',
                'ReasonForReferral',
                'Procedures',
                'Vitals',
                'Immunizations',
                'Medications',
                'MedicationsAdministered',
                'PlanOfCare',
                'CareOfPlan',
                'Problems',
                'Allergies',
                'SocialHistory',
                'Results',
                'FunctionalStatus',
                'Encounters'
            ];

            /**
             * Run Section method for each section
             */
            foreach($sections AS $Section){
                call_user_func([
                       $this,
                       "set{$Section}Section"
                   ]);
            }

            /**
             * Build the CCR XML Object
             */
            if(stripos(URL, '?')){
                $DeleteQuery = substr(URL, stripos(URL, '?'));
                $URL = str_replace($DeleteQuery, "", URL);
            } else {
                $URL = URL;
            }
            Array2XML::init(
                '1.0',
                'UTF-8',
                true,
                ['xml-stylesheet' => 'type="text/xsl" href="'.$URL.'/lib/CCRCDA/schema/cda2.xsl"']
            );
            $this->xml = Array2XML::createXML('ClinicalDocument', $this->xmlData);
        } catch(Exception $Error) {
            error_log($Error->getMessage());
        }
    }

    /**
     * clean from html characters and html tags
     * @param $string
     * @return string
     */
    public function clean($string){
        // Pass 1
        $cleanIt = html_entity_decode($string);
        // Pass 2
        $cleanIt = strip_tags($cleanIt);
        // Pass 3
        $cleanIt = preg_replace("/&#?[a-z0-9]{2,8};/i", "", $cleanIt);
        // Pass 4
        $cleanIt = preg_replace("/\p{Cc}+/u", "", $cleanIt);
        return $cleanIt;
    }

    /**
     * Method setRequirements()
     */
    public function setRequirements()
    {
        if($this->template == 'toc'){
            $this->requiredAllergies = true;
            $this->requiredVitals = true;
            $this->requiredImmunization = true;
            $this->requiredMedications = true;
            $this->requiredProblems = true;
            $this->requiredProcedures = true;
            $this->requiredPlanOfCare = true;
            $this->requiredCareOfPlan = true;
            $this->requiredResults = true;
            $this->requiredEncounters = false;
        }
    }

    /**
     * Method view()
     */
    public function archive()
    {
        try {
            header('Content-type: application/xml');
            $xml = $this->xml->saveXML();
            $name = $this->getFileName() . '.xml';
            $date = date('Y-m-d H:i:s');
            $document = new stdClass();
            $document->pid = $this->pid;
            $document->eid = $this->eid;
            $document->uid = $_SESSION['user']['id'];
            $document->docType = 'C-CDA';
            $document->name = $name;
            $document->date = $date;
            $document->note = '';
            $document->title = 'C-CDA';
            $document->encrypted = 0;
            $document->document = base64_encode(html_entity_decode(strip_tags($xml)));
            include_once(ROOT . '/dataProvider/DocumentHandler.php');
            $DocumentHandler = new DocumentHandler();
            $DocumentHandler->addPatientDocument($document);
            unset($DocumentHandler, $document, $name, $date);
            print $xml;
        } catch(Exception $Error) {
            error_log($Error->getMessage());
        }
    }

    /**
     * Method view()
     */
    public function view()
    {
        try {
            header('Content-type: application/xml');
            print $this->xml->saveXML();
        } catch(Exception $Error) {
            error_log($Error->getMessage());
        }
    }

    public function removeDuplicateKeys($key,$data){

        $_data = array();

        foreach ($data as $v) {
            if (isset($_data[$v[$key]])) {
                // found duplicate
                continue;
            }
            // remember unique item
            $_data[$v[$key]] = $v;
        }
        // if you need a zero-based array, otherwise work with $_data
        $data = array_values($_data);
        return $data;
    }

    /**
     * Method get()
     */
    public function get()
    {
        try {
            return $this->xml->saveXML();
        } catch(Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Method export()
     * @param bool $return
     * @return array
     */
    public function export($return = false)
    {
        try {
            // Create a ZIP archive for delivery
            $dir = site_temp_path . '/';
            $filename = $this->getFileName();
            $file = $this->zipIt($dir, $filename);
            $filename .= '.zip';

            if($return){
                $handle = fopen($file, "r");
                $data = html_entity_decode(strip_tags(fread($handle, filesize($file))));
                fclose($handle);

                $fileData = [];
                $fileData['filename'] = $filename;
                $fileData['data'] = $data;
                unlink($file);
                return $fileData;
            }

            // Stream the file to the client
            header('Content-Type: application/zip');
            header('Content-Length: ' . filesize($file));
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            readfile($file);
            unlink($file);
        } catch(Exception $Error) {
            error_log($Error->getMessage());
        }
    }

    /**
     * Method save()
     * @param $toDir
     * @param $fileName
     */
    public function save($toDir, $fileName)
    {
        try {
            $filename = $fileName ? $fileName : $this->getFileName();
            $this->zipIt($toDir, $filename);
        } catch(Exception $e) {
            print $e->getMessage();
        }
    }

    /**
     * Method zipIt()
     */
    public function zipIt($dir, $filename)
    {
        $zip = new ZipArchive();
        $file = $dir . $filename . '.zip';
        if($zip->open($file, ZipArchive::CREATE) !== true)
            exit("cannot open <$filename.zip>\n");

        $xml = $this->xml->saveXML();
        $xml = preg_replace('/href="(.*)"/', 'href="cda2.xsl"', $xml, 1);

        $sha1  = hash('sha1', $xml);
        $sha256  = hash('sha256', $xml);
        $sha512  = hash('sha512', $xml);
        $md5  = hash('md5', $xml);

        $hashes = <<<INTRUCTIONS
{$filename}.xml hashes

SHA-1: {$sha1}
SHA-256: {$sha256}
SHA-512: {$sha512}
MD5: {$md5}

Info:

A hash value (or simply hash), also called a message digest, is a number
generated from a string of text. The hash is substantially smaller than
the text itself, and is generated by a formula in such a way that it is
extremely unlikely that some other text will produce the same hash value.

Hashes play a role in security systems where they're used to ensure that 
transmitted messages have not been tampered with. The sender generates a 
hash of the message, encrypts it, and sends it with the message itself. 
The recipient then decrypts both the message and the hash, produces another 
hash from the received message, and compares the two hashes. If they're the same,
there is a very high probability that the message was transmitted intact.

More info about applications to verify hash values at
https://www.raymond.cc/blog/7-tools-verify-file-integrity-using-md5-sha1-hashes/
INTRUCTIONS;

        $zip->addFromString('hashes.txt', $hashes);
        $zip->addFromString($filename . '.xml', $xml);
        $zip->addFromString('cda2.xsl', file_get_contents(ROOT . '/lib/CCRCDA/schema/cda2.xsl'));
        $zip->close();
        return $file;
    }

}

