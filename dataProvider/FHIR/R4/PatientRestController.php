<?php

namespace MDTL\FHIR;

use HL7\FHIR\R4\PHPFHIRAutoloader;
use HL7\FHIR\R4\FHIRElement\FHIRCode;
use HL7\FHIR\R4\FHIRElement\FHIRCodeableConcept;
use HL7\FHIR\R4\FHIRElement\FHIRCoding;
use HL7\FHIR\R4\FHIRElement\FHIRContactPoint;
use HL7\FHIR\R4\FHIRElement\FHIRContactPointSystem;
use HL7\FHIR\R4\FHIRElement\FHIRContactPointUse;
use HL7\FHIR\R4\FHIRElement\FHIRDateTime;
use HL7\FHIR\R4\FHIRElement\FHIRExtension;
use HL7\FHIR\R4\FHIRElement\FHIRIdentifier;
use HL7\FHIR\R4\FHIRElement\FHIRIdentifierUse;
use HL7\FHIR\R4\FHIRElement\FHIRMeta;
use HL7\FHIR\R4\FHIRElement\FHIRPeriod;
use HL7\FHIR\R4\FHIRElement\FHIRReference;
use HL7\FHIR\R4\FHIRElement\FHIRString;
use HL7\FHIR\R4\FHIRElement\FHIRUri;
use HL7\FHIR\R4\FHIRElement\FHIRBackboneElement\FHIRPatient\FHIRPatientCommunication;
use HL7\FHIR\R4\FHIRElement\FHIRBackboneElement\FHIRProvenance;
use HL7\FHIR\R4\FHIRResource\FHIRDomainResource\FHIRPatient;
use HL7\FHIR\R4\FHIRElement\FHIRAddress;
use HL7\FHIR\R4\FHIRElement\FHIRHumanName;
use HL7\FHIR\R4\FHIRElement\FHIRAdministrativeGender;
use HL7\FHIR\R4\FHIRElement\FHIRId;




class PatientRestController
{
    public function __construct()
    {
        include_once ROOT. '/lib/HL7/FHIR/R4/PHPFHIRAutoloader.php';
        PHPFHIRAutoloader::register();
    }

    public function getPatient($request){
        include_once ROOT . '/dataProvider/Patient.php';
        $Patient =  new \Patient();
        $patient = $Patient->getPatient(['pid' => 1]);
        $patient['uuid'] = '2314.23.231.2134.423.234.123';
        return $this->parseMdtlRecord($patient);

    }

    public function postPatient($request){

    }

    public function putPatient($request){

    }

    public function parseMdtlRecord($dataRecord = array(), $encode = false)
    {
        $patientResource = new FHIRPatient();

        $meta = array('versionId' => '1', 'lastUpdated' => gmdate('c'));
        $patientResource->setMeta(new FHIRMeta($meta));

        $patientResource->setActive(true);
        $id = new FHIRId();
        $id->setValue($dataRecord['uuid']);
        $patientResource->setId($id);

        $this->parseMdtlPatientSummaryText($patientResource, $dataRecord);
        $this->parseMdtlPatientName($patientResource, $dataRecord);
        $this->parseMdtlPatientAddress($patientResource, $dataRecord);
        $this->parseMdtlPatientTelecom($patientResource, $dataRecord);

        $this->parseMdtlDateOfBirth($patientResource, $dataRecord['DOB']);
        $this->parseMdtlGenderAndBirthSex($patientResource, $dataRecord['sex']);
        $this->parseMdtlRaceRecord($patientResource, $dataRecord['race']);
        $this->parseMdtlEthnicityRecord($patientResource, $dataRecord['ethnicity']);
        $this->parseMdtlSocialSecurityRecord($patientResource, $dataRecord['ss']);
        $this->parseMdtlPublicPatientIdentifier($patientResource, $dataRecord['pubpid']);
        $this->parseMdtlCommunicationRecord($patientResource, $dataRecord['language']);

        if ($encode) {
            return json_encode($patientResource, JSON_PRETTY_PRINT);
        } else {
            return $patientResource;
        }
    }

    private function parseMdtlPatientSummaryText(FHIRPatient $patientResource, $dataRecord)
    {

        $narrativeText = '';
        if (!empty($dataRecord['fname'])) {
            $narrativeText = $dataRecord['fname'];
        }
        if (!empty($dataRecord['lname'])) {
            $narrativeText .= ' ' . $dataRecord['lname'];
        }
        if (!empty($narrativeText)) {
            $text = array(
                'status' => 'generated',
                'div' => '<div xmlns="http://www.w3.org/1999/xhtml"> <p>' . $narrativeText . '</p></div>'
            );
            $patientResource->setText($text);
        }
    }

    private function parseMdtlDateOfBirth(FHIRPatient $patientResource, $dateOfBirth)
    {
        if (isset($dateOfBirth)) {
            $patientResource->setBirthDate($dateOfBirth);
        }
    }

    private function parseMdtlPatientName(FHIRPatient $patientResource, $dataRecord)
    {

        $name = new FHIRHumanName();
        $name->setUse('official');

        if (!empty($dataRecord['title'])) {
            $name->addPrefix($dataRecord['title']);
        }
        if (!empty($dataRecord['lname'])) {
            $name->setFamily($dataRecord['lname']);
        }

        if (!empty($dataRecord['fname'])) {
            $name->addGiven($dataRecord['fname']);
        }

        if (!empty($dataRecord['mname'])) {
            $name->addGiven($dataRecord['mname']);
        }

        $patientResource->addName($name);
    }

    private function parseMdtlPatientAddress(FHIRPatient $patientResource, $dataRecord)
    {
        $address = UtilsService::createAddressFromRecord($dataRecord);
        if ($address !== null) {
            $patientResource->addAddress($address);
        }
    }

    private function parseMdtlPatientTelecom(FHIRPatient $patientResource, $dataRecord)
    {

        if (!empty($dataRecord['phone_home'])) {
            $patientResource->addTelecom($this->createContactPoint('phone', $dataRecord['phone_home'], 'home'));
        }

        if (!empty($dataRecord['phone_biz'])) {
            $patientResource->addTelecom($this->createContactPoint('phone', $dataRecord['phone_biz'], 'work'));
        }

        if (!empty($dataRecord['phone_cell'])) {
            $patientResource->addTelecom($this->createContactPoint('phone', $dataRecord['phone_cell'], 'mobile'));
        }

        if (!empty($dataRecord['email'])) {
            $patientResource->addTelecom($this->createContactPoint('email', $dataRecord['email'], 'home'));
        }
    }

    private function parseMdtlGenderAndBirthSex(FHIRPatient $patientResource, $sex)
    {
        // @see https://www.hl7.org/fhir/us/core/ValueSet-birthsex.html
        $genderValue = $sex ?? 'Unknown';
        $birthSex = "UNK";
        $gender = new FHIRAdministrativeGender();
        $birthSexExtension = new FHIRExtension();
        if ($genderValue !== 'Unknown') {
            if ($genderValue === 'Male') {
                $birthSex = 'M';
            } else if ($genderValue === 'Female') {
                $birthSex = 'F';
            }
        }
        $gender->setValue(strtolower($genderValue));
        $birthSexExtension->setUrl("http://hl7.org/fhir/us/core/StructureDefinition/us-core-birthsex");
        $birthSexExtension->setValueCode($birthSex);
        $patientResource->addExtension($birthSexExtension);
        $patientResource->setGender($gender);
    }
    private function parseMdtlRaceRecord(FHIRPatient $patientResource, $race)
    {
        $code = 'UNK';
        $display = html_entity_decode("Unknown");
        // race is defined as containing 2 required extensions, text & ombCategory
        $raceExtension = new FHIRExtension();
        $raceExtension->setUrl("http://hl7.org/fhir/us/core/StructureDefinition/us-core-race");

        $ombCategory = new FHIRExtension();
        $ombCategory->setUrl("ombCategory");
        $ombCategoryCoding = new FHIRCoding();
        $ombCategoryCoding->setSystem(new FHIRUri("urn:oid:2.16.840.1.113883.6.238"));
        if (isset($race)) {

            //TODO get from combo data
            $record = $this->listService->getListOption('race', $race);


            if (empty($record)) {
                // TODO: adunsulag need to handle a data missing exception here
            } else if ($race === 'declne_to_specfy') {
                // @see https://www.hl7.org/fhir/us/core/ValueSet-omb-race-category.html
                $code = "ASKU";
                $display = xlt("Asked but no answer");
            } else {
                $code = $record['notes'];
                $display = $record['title'];
            }

            $ombCategoryCoding->setCode($code);
            $ombCategoryCoding->setDisplay(xlt($display));
        }
        $ombCategory->setValueCoding($ombCategoryCoding);
        $raceExtension->addExtension($ombCategory);

        $textExtension = new FHIRExtension();
        $textExtension->setUrl("text");
        $textExtension->setValueString(new FHIRString($ombCategoryCoding->getDisplay()));
        $raceExtension->addExtension($textExtension);
        $patientResource->addExtension($raceExtension);
    }

    private function parseMdtlEthnicityRecord(FHIRPatient $patientResource, $ethnicity)
    {
        // TODO: this is a required field, so not sure what we want to do if this is missing?
        if (!empty($ethnicity)) {
            $ethnicityExtension = new FHIRExtension();
            $ethnicityExtension->setUrl("http://hl7.org/fhir/us/core/StructureDefinition/us-core-ethnicity");

            $ombCategoryExtension = new FHIRExtension();
            $ombCategoryExtension->setUrl("ombCategory");

            $textExtension = new FHIRExtension();
            $textExtension->setUrl("text");

            $coding = new FHIRCoding();
            $coding->setSystem(new FHIRUri("http://terminology.hl7.org/CodeSystem/v3-Ethnicity"));

            $record = $this->listService->getListOption('ethnicity', $ethnicity);
            if (empty($record)) {
                // TODO: stephen put a data missing reason where the coding could not be found for some reason
            } else {
                $coding->setCode($record['notes']);
                $coding->setDisplay($record['title']);
                $coding->setSystem("urn:oid:2.16.840.1.113883.6.238");
                $textExtension->setValueString($record['title']);
            }

            $ombCategoryExtension->setValueCoding($coding);
            $ethnicityExtension->addExtension($ombCategoryExtension);
            $ethnicityExtension->addExtension($textExtension);

            $patientResource->addExtension($ethnicityExtension);
        }
    }


    private function parseMdtlSocialSecurityRecord(FHIRPatient $patientResource, $ssn)
    {
        // Not sure what to do here but this is on the 2021 HL7 US Core page about SSN
        // * The Patient’s Social Security Numbers SHOULD NOT be used as a patient identifier in Patient.identifier.value.
        // There is increasing concern over the use of Social Security Numbers in healthcare due to the risk of identity
        // theft and related issues. Many payers and providers have actively purged them from their systems and
        // filter them out of incoming data.
        // @see http://hl7.org/fhir/us/core/2021Jan/StructureDefinition-us-core-patient.html#FHIR-27731
        if (!empty($ssn)) {
            $patientResource->addIdentifier(
                $this->createIdentifier(
                    'official',
                    'http://terminology.hl7.org/CodeSystem/v2-0203',
                    'SS',
                    'http://hl7.org/fhir/sid/us-ssn',
                    $ssn
                )
            );
        }
    }

    private function parseMdtlPublicPatientIdentifier(FHIRPatient $patientResource, $pubpid)
    {
        if (!empty($pubpid)) {
            $patientResource->addIdentifier(
            // not sure if the SystemURI for PT should be the same or not.
                $this->createIdentifier(
                    'official',
                    'http://terminology.hl7.org/CodeSystem/v2-0203',
                    'PT',
                    'http://terminology.hl7.org/CodeSystem/v2-0203',
                    $pubpid
                )
            );
        }
    }

    private function parseMdtlCommunicationRecord(FHIRPatient $patientResource, $language)
    {
        $record = $this->listService->getListOption('language', $language);
        if (!empty($record)) {
            $communication = new FHIRPatientCommunication();
            $languageConcept = new FHIRCodeableConcept();
            $language = new FHIRCoding();
            $language->setSystem(new FHIRUri("http://hl7.org/fhir/us/core/ValueSet/simple-language"));
            $language->setCode(new FHIRCode($record['notes']));
            $language->setDisplay(xlt($record['title']));
            $languageConcept->addCoding($language);
            $languageConcept->setText(xlt($record['title']));
            $communication->setLanguage($languageConcept);
            $patientResource->addCommunication($communication);
        }
    }

    private function createIdentifier($use, $system, $code, $systemUri, $value): FHIRIdentifier
    {
        $identifier = new FHIRIdentifier();
        $idUse = new FHIRIdentifierUse();
        $idUse->setValue($use);
        $identifier->setUse($idUse);
        $idType = new FHIRCodeableConcept();
        $idTypeCoding = new FHIRCoding();
        $idTypeCoding->setSystem(new FHIRUri($system));
        $idTypeCoding->setCode(new FHIRCode($code));
        $idType->addCoding($idTypeCoding);
        $identifier->setType($idType);
        $identifier->setSystem(new FHIRUri($systemUri));
        $identifier->setValue(new FHIRString($value));
        return $identifier;
    }

    private function createContactPoint($system, $value, $use): FHIRContactPoint
    {
        $contactPoint = new FHIRContactPoint();
        $contactPoint->setSystem(new FHIRContactPointSystem(['value' => $system]));
        $contactPoint->setValue(new FHIRString($value));
        $contactPoint->setUse(new FHIRContactPointUse(['value' => $use]));
        return $contactPoint;
    }

    public function parseFhirResource($fhirResource = array())
    {
        // TODO: ONC certification only deals with READ operations, the mapping of FHIR values such as language,ethnicity
        // etc are NOT being done here and so the creation/updating of resources is currently NOT correct, this will
        // need to be addressed by future development work.
        $data = array();

        if (isset($fhirResource['id'])) {
            $data['uuid'] = $fhirResource['id'];
        }

        if (isset($fhirResource['name'])) {
            $name = [];
            foreach ($fhirResource['name'] as $sub_name) {
                if ($sub_name['use'] === 'official') {
                    $name = $sub_name;
                    break;
                }
            }
            if (isset($name['family'])) {
                $data['lname'] = $name['family'];
            }
            if ($name['given'][0]) {
                $data['fname'] = $name['given'][0];
            }
            if (isset($name['given'][1])) {
                $data['mname'] = $name['given'][1];
            }
            if (isset($name['prefix'][0])) {
                $data['title'] = $name['prefix'][0];
            }
        }
        if (isset($fhirResource['address'])) {
            if (isset($fhirResource['address'][0]['line'][0])) {
                $data['street'] = $fhirResource['address'][0]['line'][0];
            }
            if (isset($fhirResource['address'][0]['postalCode'][0])) {
                $data['postal_code'] = $fhirResource['address'][0]['postalCode'];
            }
            if (isset($fhirResource['address'][0]['city'][0])) {
                $data['city'] = $fhirResource['address'][0]['city'];
            }
            if (isset($fhirResource['address'][0]['state'][0])) {
                $data['state'] = $fhirResource['address'][0]['state'];
            }
            if (isset($fhirResource['address'][0]['country'][0])) {
                $data['country_code'] = $fhirResource['address'][0]['country'];
            }
        }
        if (isset($fhirResource['telecom'])) {
            foreach ($fhirResource['telecom'] as $telecom) {
                switch ($telecom['system']) {
                    case 'phone':
                        switch ($telecom['use']) {
                            case 'mobile':
                                $data['phone_cell'] = $telecom['value'];
                                break;
                            case 'home':
                                $data['phone_home'] = $telecom['value'];
                                break;
                            case 'work':
                                $data['phone_biz'] = $telecom['value'];
                                break;
                        }
                        break;
                    case 'email':
                        $data['email'] = $telecom['value'];
                        break;
                    default:
                        //Should give Error for incapability
                        break;
                }
            }
        }
        if (isset($fhirResource['birthDate'])) {
            $data['DOB'] = $fhirResource['birthDate'];
        }
        if (isset($fhirResource['gender'])) {
            $data['sex'] = $fhirResource['gender'];
        }

        foreach ($fhirResource['identifier'] as $index => $identifier) {
            if (!isset($identifier['type']['coding'][0])) {
                continue;
            }

            $code = $identifier['type']['coding'][0]['code'];
            switch ($code) {
                case 'SS':
                    $data['ss'] = $identifier['value'];
                    break;
                case 'PT':
                    $data['pubpid'] = $identifier['value'];
                    break;
            }
        }
        return $data;
    }

}