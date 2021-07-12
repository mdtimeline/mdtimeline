<?php


use MDTL\FHIR\PatientRestController;

include_once ROOT . '/dataProvider/FHIR/R4/PatientRestController.php';

class FHIRTest
{

    public function getPatient(){
        $PatientRestController = new PatientRestController();
        $request = [];
        return $PatientRestController->getPatient($request);
    }

}