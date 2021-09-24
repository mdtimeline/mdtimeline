<?php

//require_once (ROOT . '/lib/phptopdf/phpToPDF.php');
require_once (ROOT . '/dataProvider/Patient.php');
require_once (ROOT . '/dataProvider/ReferringProviders.php');
require_once (ROOT . '/dataProvider/DocumentHandler.php');

class DocumentPdfForms {

    private $pipes;
    private $error;
    private $source;

    private $pdftk = 'pdftk';

    /**
     * @var bool|\MatchaCUP
     */
    private $d;

    /**
     * @var DocumentHandler
     */
    private $DocumentHandler;

    /**
     * @var \Patient
     */
    private $Patient;

    /**
     * @var \ReferringProviders
     */
    private $ReferringProviders;

    function __construct(){
        $this->d = \MatchaModel::setSenchaModel('App.model.administration.DocumentForm');
        $this->Patient = new \Patient();
        $this->ReferringProviders = new \ReferringProviders();
        $this->DocumentHandler = new \DocumentHandler();

        if(file_exists('/usr/local/bin/pdftk')){
            $this->pdftk = '/usr/local/bin/pdftk';
        }else if (file_exists('/usr/bin/pdftk')){
            $this->pdftk = '/usr/bin/pdftk';
        }
    }

    public function getDocumentPdfForms($params){
        return $this->d->load($params)->all();
    }

    public function getDocumentPdfForm($params){
        return $this->d->load($params)->one();
    }

    public function addDocumentPdfForm($params){
        return $this->d->save($params);
    }

    public function updateDocumentPdfForm($params){
        return $this->d->save($params);
    }

    public function saveDocumentPdfFormsSigned($params){

        return [
            'success' => true
        ];
    }

    public function generatePdfForms($pdf_form_ids, $pid, $referring_id = null, $custom_fields = null){
        $binary_documents = [];
        foreach ($pdf_form_ids as $pdf_form_id){
            $binary_documents[] = $this->generatePdfForm($pdf_form_id, $pid, $referring_id, $custom_fields, true);
        }

        if(count($binary_documents) === 1){
            $binary_document = $binary_documents[0];
        }else{
            $binary_document = $this->mergeDocuments($binary_documents);
        }

        include_once(ROOT . '/dataProvider/DocumentHandler.php');
        $documentParams = new \stdClass();
        $documentParams->document = base64_encode($binary_document);
        $documentParams->document_name = 'PDF Forms';
        return $this->DocumentHandler->createTempDocument($documentParams);

    }

    private function mergeDocuments($binary_documents){

        $temp_out_document = tempnam(site_temp_path, 'pdf_form_out_');
        chmod($temp_out_document, 0777);
        rename($temp_out_document, $temp_out_document.'.pdf');
        $temp_out_document = $temp_out_document . '.pdf';

        $temp_in_documents_array = [];

        foreach ($binary_documents as $binary_document){
            $temp_file =  tempnam(site_temp_path, 'pdf_form_in_');
            chmod($temp_file, 0777);
            rename($temp_file, $temp_file.'.pdf');
            $temp_file = $temp_file . '.pdf';
            file_put_contents($temp_file, $binary_document);
            $temp_in_documents_array[] = $temp_file;
        }

        $temp_in_documents = implode(' ', $temp_in_documents_array);

        $cmd = "{$this->pdftk} {$temp_in_documents} cat output {$temp_out_document}";
        exec($cmd);

        $binary_out_document = file_get_contents($temp_out_document);

        foreach ($temp_in_documents_array as $temp_in_document){
            unlink($temp_in_document);
        }
        unlink($temp_out_document);

        return $binary_out_document;
    }

    public function generatePdfForm($pdf_form_id, $pid, $referring_id = null, $custom_fields = null, $return_binary_document = false) {

        $pdf_form = $this->getDocumentPdfForm($pdf_form_id);
        $fields_data = $this->Patient->getPatientTokenByPid($pid);

        /**
        PATIENT_RECORD_NUMBER,
        PATIENT_TITLE,
        PATIENT_FNAME,
        PATIENT_MNAME,
        PATIENT_LNAME,
        PATIENT_NAME,
        PATIENT_AGE,
        PATIENT_DOB,
        PATIENT_SEX,

        PATIENT_PHONE_HOME,
        PATIENT_PHONE_WORK,
        PATIENT_PHONE_MOBILE,
        PATIENT_EMAIL,

        PATIENT_POSTAL_ADDRESS1,
        PATIENT_POSTAL_ADDRESS2,
        PATIENT_POSTAL_CITY,
        PATIENT_POSTAL_STATE,
        PATIENT_POSTAL_ZIP,

        PATIENT_PHYSICAL_ADDRESS1,
        PATIENT_PHYSICAL_ADDRESS2,
        PATIENT_PHYSICAL_CITY,
        PATIENT_PHYSICAL_STATE,
        PATIENT_PHYSICAL_ZIP,

        PATIENT_EMPLOYER,
        PATIENT_OCCUPATION,

        PATIENT_FATHER_FNAME,
        PATIENT_FATHER_MNAME,
        PATIENT_FATHER_LNAME,
        PATIENT_FATHER_NAME,

        PATIENT_MOTHER_FNAME,
        PATIENT_MOTHER_MNAME,
        PATIENT_MOTHER_LNAME,
        PATIENT_MOTHER_NAME,

        PATIENT_SPOUSE_FNAME,
        PATIENT_SPOUSE_MNAME,
        PATIENT_SPOUSE_LNAME,
        PATIENT_SPOUSE_NAME,

        PATIENT_AUTHORIZED1_FNAME,
        PATIENT_AUTHORIZED1_MNAME,
        PATIENT_AUTHORIZED1_LNAME,
        PATIENT_AUTHORIZED1_NAME,
        PATIENT_AUTHORIZED1_PHONE,
        PATIENT_AUTHORIZED1_RELATION,

        PATIENT_AUTHORIZED2_FNAME,
        PATIENT_AUTHORIZED2_MNAME,
        PATIENT_AUTHORIZED2_LNAME,
        PATIENT_AUTHORIZED2_NAME,
        PATIENT_AUTHORIZED2_PHONE,
        PATIENT_AUTHORIZED2_RELATION

         */

        if(isset($referring_id)){
            $referring_tokens = $this->ReferringProviders->getReferringTokenById($referring_id);
            $fields_data = array_merge($fields_data, $referring_tokens);
        }

        if(isset($custom_fields)){
            $custom_fields = (array)$custom_fields;
            $fields_data = array_merge($fields_data, $custom_fields);
        }

        $fields_data['[DATE]'] = date('m/d/Y');
        $fields_data['[TIME]'] = date('g:i a');
        $fields_data['[DATETIME]'] = date('m/d/Y g:i a');

        $pdf_form_title = $pdf_form['document_title'];
        $pdf_form_path = $pdf_form['document_path'];

        $binary_document = $this->fillForm($fields_data, $pdf_form_path);

        // return binary document
        if($return_binary_document === true){
            return $binary_document;
        }

        // there is an error
        if($binary_document === false){
            return false;
        }

        include_once(ROOT . '/dataProvider/DocumentHandler.php');
        $documentParams = new \stdClass();
        $documentParams->document = base64_encode($binary_document);
        $documentParams->document_name = $pdf_form_title;
        return $this->DocumentHandler->createTempDocument($documentParams);
    }


    /**
     * @param array $fields_data
     * @param string $pdf_form_path
     */
    public function fillForm($fields_data, $pdf_form_path) {

        if(empty($fields_data)){
            error_log("ERROR: DocumentPdfForms->fillForm fields_data is empty");
            return false;
        }

        if(!file_exists($pdf_form_path)){
            error_log("ERROR: DocumentPdfForms->fillForm {$pdf_form_path} form does not exists");
            return false;
        }

        $jar = ROOT . '/lib/PdfFormFiller/pdfformfiller.jar';
        $cmd = "java -jar {$jar} {$pdf_form_path} -flatten 2>&1";

        $descriptorspec = array(
            0 => array('pipe', 'r'), // stdin is a pipe that the child will read from
            1 => array('pipe', 'w'), // stdout is a pipe that the child will write to
            2 => array('pipe', 'w')  // Actually, stderr is sent to stdin " 2>&1"
        );

        $path = null;
        $env = array('LANG' => 'en_US.UTF-8');
        $this->source = proc_open($cmd, $descriptorspec, $this->pipes, $path, $env);

        foreach ($fields_data as $key => $value){
            $this->writeField($key, $value);
        }

        $this->error |= false === fclose($this->pipes[0]);
        $result = stream_get_contents($this->pipes[1]);
        $this->error |= false === $result;
        $this->error |= false === fclose($this->pipes[1]);
        $return_value = proc_close($this->source);
        $this->error |= $return_value != 0;

        if (!$this->error) {
            return $result;
        }else{
            if ($return_value == -1) {
                error_log("ERROR: DocumentPdfForms->fillForm error accessing pdfformfiller.jar.");
            }else {
                error_log("ERROR: DocumentPdfForms->fillForm pdfformfiller.jar returned error code: $return_value");
            }
            return false;
        }
    }

    private function signPdfForm($params){
        $pdf_form = $this->getDocumentPdfForm($params->form_id);
        $temp_document = $this->DocumentHandler->getTempDocument(['id' => $params->document_id], true);

        if($pdf_form === false){
            return [
                'success' => false,
                'error' => 'PDF Form not found'
            ];
        }

        if($temp_document === false){
            return [
                'success' => false,
                'error' => 'Document to sign not found'
            ];
        }

        if(!isset($params->signature)){
            return [
                'success' => false,
                'error' => 'Signature image missing'
            ];
        }

        include_once(ROOT . '/lib/tcpdf/tcpdf.php');
        include_once(ROOT . '/lib/FPDI/fpdi.php');

        $signed_pdf = tempnam(site_temp_path, 'signed_pdf_');
        $handle = fopen($signed_pdf, "w");
        fwrite($handle, $temp_document['document']);
        fclose($handle);

        $FPDI = new FPDI();
        $FPDI->setSourceFile($signed_pdf);

        /**
         * NOTES:
         * - To create self-signed signature: openssl req -x509 -nodes -days 365000 -newkey rsa:1024 -keyout tcpdf.crt -out tcpdf.crt
         * - To export crt to p12: openssl pkcs12 -export -in tcpdf.crt -out tcpdf.p12
         * - To convert pfx certificate to pem: openssl pkcs12 -in tcpdf.pfx -out tcpdf.crt -nodes
         */

        // set certificate file
        $certificate = $this->getSignatureCert();

        // set additional information
        $info = array(
            'Name' => $pdf_form['document_title'],
            'Location' => $pdf_form['facility_name'],
            'Reason' => $pdf_form['document_title'],
            'ContactInfo' => 'https://mdtimeline.com',
        );

        // set document signature
        $FPDI->setSignature($certificate, $certificate, 'S3cur3#mdtl', '', 1, $info);

        // create content for signature (image and/or text)
        $signature_image = tempnam(site_temp_path, 'pdf_signature_image_');
        $handle = fopen($signature_image, "w");
        fwrite($handle, base64_decode($params->signature));
        fclose($handle);

        $FPDI->Image(
            $signature_image,
            $pdf_form['signature_x'],
            $pdf_form['signature_y'],
            $pdf_form['signature_w'],
            $pdf_form['signature_h'],
            'PNG');

        $FPDI->setSignatureAppearance(
            $pdf_form['signature_x'],
            $pdf_form['signature_y'],
            $pdf_form['signature_w'],
            $pdf_form['signature_h']
        );

        $document = $FPDI->Output('signed_document.pdf', 'S');

        // delete temp pdf and signature image
        unlink($signed_pdf);
        unlink($signature_image);

        $temp_document->title = $pdf_form['document_title'] . ' (Signed)';
        $temp_document->document = base64_encode($document);
        $temp_document->date = date('Y-m-d H:i:s');
        $temp_document->name = 'signed_document.pdf';
        unset($temp_document->id);

        $document_record = $this->addPatientDocument($temp_document);

        return [
            'success'=> true,
            'record' => $document_record
        ];

    }

    private function valueEscape($str){
        if($str === 'Yes'){
            return $str;
        }
        $str = str_replace("\\", "\\\\", $str);
        $str = str_replace("\n", "\\n", $str);
        return strtoupper($str);
    }

    private function writeField($field, $text){
        $this->error |= false === fwrite($this->pipes[0], $field . ' ' . $this->valueEscape($text) . "\n");
    }

    private function getSignatureCert(){
        $crt = 'QmFnIEF0dHJpYnV0ZXMKICAgIGxvY2FsS2V5SUQ6IDIzIEE1IEIyIEU0IDI4IEExIDVBIDYxIEE3IDBGIENCIDQxIDYyIERGIDk0IEVBIDdCIDgxIDJBIEI4CnN1YmplY3Q9L0M9VVMvU1Q9UFIvTD1TYW4gSnVhbi9PPU1EVElNRUxJTkUgTExDL09VPU1EVElNRUxJTkUgTExDL0NOPW1kdGltZWxpbmUuY29tL2VtYWlsQWRkcmVzcz1zdXBwb3J0QG1kdGltZWxpbmUuY29tCmlzc3Vlcj0vQz1VUy9TVD1QUi9MPVNhbiBKdWFuL089TURUSU1FTElORSBMTEMvT1U9TURUSU1FTElORSBMTEMvQ049bWR0aW1lbGluZS5jb20vZW1haWxBZGRyZXNzPXN1cHBvcnRAbWR0aW1lbGluZS5jb20KLS0tLS1CRUdJTiBDRVJUSUZJQ0FURS0tLS0tCk1JSUN1VENDQWlJQ0NRQ2F3R3pKRlJmOUxUQU5CZ2txaGtpRzl3MEJBUXNGQURDQm56RUxNQWtHQTFVRUJoTUMKVlZNeEN6QUpCZ05WQkFnTUFsQlNNUkV3RHdZRFZRUUhEQWhUWVc0Z1NuVmhiakVYTUJVR0ExVUVDZ3dPVFVSVQpTVTFGVEVsT1JTQk1URU14RnpBVkJnTlZCQXNNRGsxRVZFbE5SVXhKVGtVZ1RFeERNUmN3RlFZRFZRUUREQTV0ClpIUnBiV1ZzYVc1bExtTnZiVEVsTUNNR0NTcUdTSWIzRFFFSkFSWVdjM1Z3Y0c5eWRFQnRaSFJwYldWc2FXNWwKTG1OdmJUQWdGdzB5TURFeU1EY3lNakk1TURaYUdBOHpNREl3TURRd09USXlNamt3Tmxvd2daOHhDekFKQmdOVgpCQVlUQWxWVE1Rc3dDUVlEVlFRSURBSlFVakVSTUE4R0ExVUVCd3dJVTJGdUlFcDFZVzR4RnpBVkJnTlZCQW9NCkRrMUVWRWxOUlV4SlRrVWdURXhETVJjd0ZRWURWUVFMREE1TlJGUkpUVVZNU1U1RklFeE1RekVYTUJVR0ExVUUKQXd3T2JXUjBhVzFsYkdsdVpTNWpiMjB4SlRBakJna3Foa2lHOXcwQkNRRVdGbk4xY0hCdmNuUkFiV1IwYVcxbApiR2x1WlM1amIyMHdnWjh3RFFZSktvWklodmNOQVFFQkJRQURnWTBBTUlHSkFvR0JBTnBvSEN3NkdkdnFTRW1ECjNWRXcrTEh5ajRDL0o1d04rVnhXTnlvdmhaZ0JGZFE4WlpCYVdrRkJkZHBGYkpMWWx3Y0pHQ1NiYXJ3cUlwcXQKdm1MR0NMMDVOWDk2ZzE1Z2loL2tpUHV3MWRHVnpMeVZrQmdyN2ptNmFkMlhDM2VwT2FSR2tMTGFDcGhlcU9BbAp5Qm8rZFd4Y29jNUJ5Y2ttVVd1dUdlMVNIQmNIQWdNQkFBRXdEUVlKS29aSWh2Y05BUUVMQlFBRGdZRUFGOGdsCkdxaTlTZWR0Y2phK1VZMGFJeEc3bWNDMGdEZEVIckYzNW43eXpJdFM0c0NHQ1JBczlPVEVEUTkranQ1bVNtN08KMnUwZzdkOEhVUmdMZDlPUHl4ZmRmOU1lUnRnUmVScEJzdTNqYWZ3N080QVduM0x1QURjMEE4N1l4dWVTKzNLVgo3MWVMTnczZGE0TXJJRzdheUNINW1NeGtPR2xKNkNRR3dQeWIxZUk9Ci0tLS0tRU5EIENFUlRJRklDQVRFLS0tLS0KQmFnIEF0dHJpYnV0ZXMKICAgIGxvY2FsS2V5SUQ6IDIzIEE1IEIyIEU0IDI4IEExIDVBIDYxIEE3IDBGIENCIDQxIDYyIERGIDk0IEVBIDdCIDgxIDJBIEI4CktleSBBdHRyaWJ1dGVzOiA8Tm8gQXR0cmlidXRlcz4KLS0tLS1CRUdJTiBQUklWQVRFIEtFWS0tLS0tCk1JSUNlQUlCQURBTkJna3Foa2lHOXcwQkFRRUZBQVNDQW1Jd2dnSmVBZ0VBQW9HQkFOcG9IQ3c2R2R2cVNFbUQKM1ZFdytMSHlqNEMvSjV3TitWeFdOeW92aFpnQkZkUThaWkJhV2tGQmRkcEZiSkxZbHdjSkdDU2JhcndxSXBxdAp2bUxHQ0wwNU5YOTZnMTVnaWgva2lQdXcxZEdWekx5VmtCZ3I3am02YWQyWEMzZXBPYVJHa0xMYUNwaGVxT0FsCnlCbytkV3hjb2M1Qnlja21VV3V1R2UxU0hCY0hBZ01CQUFFQ2dZRUF1Uzh0TkJMUGVqZmJzdm1yM2Z1MzRxblkKSTBIeFE5QlZib1Zyb01sS2JPZitxa1hMbCtvRVBQQlVEUTV3VU5KMHUvSnFGaC94RURwcHZOMDBZR3VwVzdaNAoyV0VQaitzM0M4U3NHTUZSTmNrTW1QK3FVano5cTlPMDJxOUM1L3RsellWU1NEaExuUTJCNlpockh6REw3aGJECnBtYVFvTkpNTlFkbHZKUGZKSUVDUVFENG9GN1RjekRYRWRHbURhTVVWelFHU0l6Zlp1Zlg2YStDNEd0VTNsbWkKUUdlQTZ5aDZQa2dzd1F4ZnYvYmFWTGhycU1Heko5NTRWR0VZYkV6OFBPRnJBa0VBNE9KTjNhUm9QRU1OeENpQgptMWZQZlY5U09jdFlaTUFyTWt6YzVtcWlEV1dOWEhZVTV6QTl6SHU3eFExRFU3WnFneUo4NjR6ZitsNERES3cxCkhWcmIxUUpCQUx1NUFzQXZDbUpwRHQyTkVHUU1UN2lxME1yaFBRNTJZRkcyTnZRMmlvRUtsZHZ1TW5yU1NkYVkKMEVuL2ZGaHZmV3UwV01SM3h1OGQ3czNzLzllMzlBOENRUUM0VFA5Zm5EUEkzM05TeFplaWhnNklReXlTTFBLZQpiMWQzZThLMkc4SC9sTENMakVLSlRlSDN5WmdUWUZGRE5BYUt2aUwrc0krSEVyRVJHN2pUKy9pWkFrQmZqT3hDCnMybFdvRFdraDh5N1ZZYmduZzJDQUp1Rm5OVTVhMmFEcU1sZG8wZ0NNL0NuNU1OZXc1U1czN2JMaC9Pb0szNlQKU0pMOXFKdGRRU0hSWFI3eQotLS0tLUVORCBQUklWQVRFIEtFWS0tLS0t';
        return base64_decode($crt);
    }

}