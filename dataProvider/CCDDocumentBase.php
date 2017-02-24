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
     * @var array
     */
    public $exclude = [];

    /**
     * @var
     */
    public $allProviders;

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
     * @param $pid
     */
    public function setPid($pid)
    {
        $this->pid = $pid;
    }

    /**
     * @param $eid
     */
    public function setEid($eid)
    {
        $this->eid = $eid == 'null' ? null : $eid;
    }

    /**
     * @param $eid
     */
    public function setAllProviders($flag)
    {
        $this->allProviders = $flag == 'null' ? null : $flag;
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
        // if you need a zero-based array, otheriwse work with $_data
        $data = array_values($_data);
        return $data;
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

