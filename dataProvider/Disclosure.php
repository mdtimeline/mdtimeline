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
class Disclosure
{

    /**
     * @var MatchaCUP
     */
    private $d;
    /**
     * @var MatchaCUP
     */
    private $ds;

    function __construct()
    {
        $this->d = MatchaModel::setSenchaModel('App.model.patient.Disclosures');
        $this->ds = MatchaModel::setSenchaModel('App.model.patient.DisclosuresDocument');
    }

    private function disclosureHandler($params, &$sql, &$values)
    {

        $where = '';
        $values = [];

        if (isset($params->filter)) {
            $buff = [];
            foreach ($params->filter as $filter) {
                $buff [] = "d.{$filter->property} {$filter->operator} :{$filter->property}";
                $values[":{$filter->property}"] = $filter->value;
            }
            $where = 'WHERE ' . implode(' AND ', $buff);
        }

        $sql = "SELECT d.*,
					   GROUP_CONCAT(CONCAT(pd.path, '/', pd.name) SEPARATOR ',') as document_file_paths,
					   GROUP_CONCAT(CONCAT('_ ', pd.docType,' - ', pd.title,' - id# ',pd.id) SEPARATOR '<br>') as document_inventory,
					   GROUP_CONCAT(pd.id) as document_inventory_ids,
					   COUNT(pd.id) as document_inventory_count
				  FROM patient_disclosures as d
			 LEFT JOIN patient_disclosures_documents as dd ON dd.disclosure_id = d.id
			 LEFT JOIN patient_documents as pd ON pd.id = dd.document_id
			  {$where}
			  GROUP BY d.id";
        return;
    }

    private function createTempDisclosureDir($Disclosure)
    {
        if (!file_exists(site_temp_path) || !is_writable(site_temp_path)) {
            throw new Exception('Temp folder dont exist or is not writable');
        }

        $disclosure_temp_path = site_temp_path . '/' . uniqid('disclosures_');
        $disclosure_temp_documents_path = $disclosure_temp_path . '/documents';

        if (!file_exists($disclosure_temp_path)) {
            if (!mkdir($disclosure_temp_path))
                throw new Exception('Disclosure temp folder could not be created');
        }

        if (!file_exists($disclosure_temp_documents_path)) {
            if (!mkdir($disclosure_temp_documents_path))
                throw new Exception('Disclosure temp documents folder could not be created');
        }

        $this->createSaveCoverLetter($Disclosure, $disclosure_temp_path . '/index.pdf');

        include_once(ROOT . '/dataProvider/DocumentHandler.php');
        $DocumentHandler = new DocumentHandler();
        $document_inventory_ids = explode(',', $Disclosure->document_inventory_ids);

        foreach ($document_inventory_ids as $document_inventory_id) {

            $document = $DocumentHandler->getPatientDocument(['id' => $document_inventory_id], true);
            $document_name = $document['id'] . '_' . $document['name'];
            $document_path = $disclosure_temp_documents_path . '/' . $document_name;
            $document_base64 = base64_decode($document['document']);
            if (!file_put_contents($document_path, $document_base64)) {
                throw new Exception("{$document['name']} could not be saved");
            }

        }

        if(isset($Disclosure->include_encounters) && $Disclosure->include_encounters === true){
            include_once (ROOT . '/dataProvider/Encounter.php');
            include_once (ROOT . '/dataProvider/DocumentHandler.php');
            $Encounter = new Encounter();
            $DocumentHandler = new DocumentHandler();

            $encounters = $Encounter->getEncountersClosedByPid($Disclosure->pid);

            if(count($encounters) > 0){

                foreach($encounters as $encounter){

                    $temp_document = $DocumentHandler->createTempDocument([
                        'pid' =>  $encounter['pid'],
                        'eid' =>  $encounter['eid'],
                        'provider_uid' =>  $encounter['provider_uid'],
                        'templateId' =>  '11',
                        'docType' =>  'EncProgress'
                    ], true);

                    $DocumentHandler->destroyTempDocument($temp_document);

                    if($temp_document !== false){
                        $service_date = date('Ymd', strtotime($encounter['service_date']));
                        $document_name = "{$encounter['eid']}_ENCOUNTER_{$service_date}.pdf";
                        $document_path = "{$disclosure_temp_documents_path}/{$document_name}";
                        $document_binary = base64_decode($temp_document->document);
                        if (!file_put_contents($document_path, $document_binary)) {
                            throw new Exception("Encounter document could not be saved");
                        }
                    }




                }
            }
        }

        return $disclosure_temp_path;
    }

    private function createTempBurnerDirectory($Disclosure, $disclosure_temp_path)
    {
        $burner_root = $this->createTempBurnerRootDir($disclosure_temp_path);
        $burner_data_dir = $this->createBurnerDataDir($burner_root);
        $burner_print_dir = $this->createBurnerPrintDir($burner_root);

        if (!is_readable($disclosure_temp_path)) {
            throw new Exception("Directory {$disclosure_temp_path} is not readable or does not have permissions");
        }

        if (!is_readable($disclosure_temp_path . '/documents')) {
            throw new Exception("Directory {$disclosure_temp_path}/documents is not readable or does not have permissions");
        }

        if (!is_writable($burner_data_dir)) {
            throw new Exception("Directory {$burner_data_dir} is not writable or does not have permissions");
        }

        if (!is_writable($burner_print_dir)) {
            throw new Exception("Directory {$burner_print_dir} is not writable or does not have permissions");
        }

        $cover_letter_src = $disclosure_temp_path . '/index.pdf';
        $cover_letter_dst = $burner_data_dir . '/index.pdf';

        if (!copy($cover_letter_src, $cover_letter_dst)) {
            throw new Exception("Could not copy index.pdf from {$cover_letter_src} to {$cover_letter_dst}");
        }

        $documents_src = $disclosure_temp_path . '/documents';
        $documents_dst = $burner_data_dir . '/documents';

        $this->copyDirectory($documents_src, $documents_dst);

        $this->createConfigTextFile($Disclosure, basename($burner_root), $burner_print_dir);

        $this->copyBurnerLabelToDir($burner_print_dir);

        return $burner_root;
    }

    private function createCoverLetter($template, $Disclosure, $getDocument)
    {
        include_once(ROOT . '/dataProvider/DocumentHandler.php');
        $DocumentHandler = new \DocumentHandler();
        $params = (object)[
            'body' => nl2br($template),
            'pid' => $Disclosure->pid,
            'disclosure' => $Disclosure,
            'facility_id' => 0
        ];

        return $DocumentHandler->createTempDocument($params, $getDocument);
    }

    private function createSaveCoverLetter($Disclosure, $path)
    {
        $template = $this->getDisclosureTemplate($Disclosure);

        $cover_letter = $this->createCoverLetter($template, $Disclosure, true);
        if (!file_put_contents($path, base64_decode($cover_letter->document))) throw new Exception('Cover letter could not be saved');
    }

    private function createDisclosureZipFile($Disclosure, $disclosure_temp_path)
    {
        try {
            $disclosure_zip_name = "disclosure-{$Disclosure->id}";
            $disclosure_zip_path = $disclosure_temp_path . '/' . $disclosure_zip_name . '.zip';
            $cover_letter_path = $disclosure_temp_path . '/index.pdf';

            $zip = new ZipArchive();

            if ($zip->open($disclosure_zip_path, ZIPARCHIVE::CREATE) != TRUE) {
                throw new Exception('Disclosure zip folder could not be created');
            }

            if (!$zip->addFile($cover_letter_path, 'index.pdf')) {
                throw new Exception('Disclosure index could not be added to the zip file');
            }

            $file_names = array_diff(scandir($disclosure_temp_path . '/documents'), array('.', '..'));

            foreach ($file_names as $file_name) {
                $file_path = $disclosure_temp_path . '/documents/' . $file_name;
                $local_name = 'documents/' . $file_name;
                if (!$zip->addFile($file_path, $local_name)) {
                    throw new Exception('Disclosure index could not be added to the zip file');
                }
            }

            // close and save archive
            $zip->close();

            return $disclosure_zip_path;
        } catch (Exception $e) {
            throw $e;
        }

    }

    private function createTempBurnerRootDir($disclosure_temp_path)
    {
        $temp_burner_path = $disclosure_temp_path . '/' . date('YmdHisv');
        if (!mkdir($temp_burner_path)) throw new Exception('Could not create disclosure burner temp root directory');

        return $temp_burner_path;
    }

    private function createBurnerDataDir($burner_root_dir)
    {
        $path = $burner_root_dir . '/data';
        if (!mkdir($path)) {
            throw new Exception('Could not create disclosure burner temp root data directory');
        }
        return $path;
    }

    private function createBurnerPrintDir($burner_root_dir)
    {
        $path = $burner_root_dir . '/print';
        if (!mkdir($path)) {
            throw new Exception('Could not create disclosure burner temp root print directory');
        }
        return $path;
    }

    private function createConfigTextFile($Disclosure, $session, $print_dir_path)
    {
        $now = date('m/d/Y g:i:s');
        $date = date('m/d/Y', strtotime($now));
        $time = date('g:i:s', strtotime($now));
        $Patient = new Patient($Disclosure->pid);
        $patient_name = $Patient->getPatientFullName(false);
        $data = "DATE={$date}\nTIME={$time}\nSession={$session}\nPatientName={$patient_name}";
        $file_path = $print_dir_path . '/merge.txt';
        if (!file_put_contents($file_path, $data)) throw new Exception('Configuration burner text file (merge.txt) could not be created!');
        return $file_path;
    }

    private function copyTempBurnerDirToBurner($burner_temp_path, $burner)
    {
        if (!isset($burner->path)) throw new Exception('Burner path not configured.');
        if (!is_writable($burner->path)) throw new Exception("Directory {$burner->path} is not writable or does not have permissions");

        $dest = $burner->path . '/' . basename($burner_temp_path);
        $this->copyDirectory($burner_temp_path, $dest);
    }

    private function copyBurnerLabelToDir($path)
    {
        $src = Globals::getGlobal('disclosure_burner_label_location');
        if ($src === false) throw new Exception('Global disclosure_burner_label_location not configured!');
        if (!is_readable($src)) throw new Exception("File {$src} is not readable or does not have permissions");

        $dst = $path . '/' . basename($src);

        if (!copy($src, $dst)) throw new Exception("Could not copy burner label from {$src} to {$path}");
    }

    private function copyDirectory($src, $dst)
    {
        $dir = opendir($src);

        mkdir($dst);

        // Loop through the files in source directory
        while ($file = readdir($dir)) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    $this->copyDirectory($src . '/' . $file, $dst . '/' . $file);
                } else {
                    $source = $src . '/' . $file;
                    $destination = $dst . '/' . $file;
                    if (!copy($source, $destination)) throw new Exception("Could not copy Directory from {$src} to {$dst}");
                }
            }
        }

        closedir($dir);
    }

    private function deleteFolder($temp_dir)
    {
        if (file_exists($temp_dir)) {
            exec("rm -rf $temp_dir");
        }
    }

    private function getDisclosureTemplate($Disclosure)
    {
        include_once(ROOT . '/dataProvider/ContentManagement.php');
        include_once(ROOT . '/dataProvider/Patient.php');

        $Patient = new Patient($Disclosure->pid);
        $patient = $Patient->getPatient();
        $language = isset($patient['language']) ? $patient['language'] : 'es';

        switch ($language){
	        case 'esp':
	        case 'es':
		        $lan = 'es';
		        break;
	        default:
	        	$lan = 'en';
        }

        $ContentManagement = new \ContentManagement();
        $template = $ContentManagement->generateContentManagement('disclosure', $lan, null, null);
        if (!$template) throw new Exception('Disclosure template not configured!');
        return $template;
    }

    public function getDisclosures($params)
    {
        $this->disclosureHandler($params, $sql, $values);
        return $this->d->sql($sql)->all($values);
    }

    public function getDisclosure($params)
    {
        $this->disclosureHandler($params, $sql, $values);
        return $this->d->sql($sql)->one($values);
    }

    public function addDisclosure($params)
    {
        return $this->d->save($params);
    }

    public function updateDisclosure($params)
    {
        return $this->d->save($params);
    }

    public function destroyDisclosure($params)
    {
        return $this->d->destroy($params);
    }

    public function getDisclosuresDocuments($params)
    {
        return $this->ds->load($params)->all();
    }

    public function getDisclosuresDocument($params)
    {
        return $this->ds->load($params)->one();
    }

    public function addDisclosuresDocument($params)
    {
        return $this->ds->save($params);
    }

    public function updateDisclosuresDocument($params)
    {
        return $this->ds->save($params);
    }

    public function destroyDisclosuresDocument($params)
    {
        return $this->ds->destroy($params);
    }

    public function removeDisclosuresDocumentsById($disclosure_id)
    {
        $this->ds->sql('DELETE FROM patient_disclosures_documents WHERE disclosure_id = :disclosure_id');
        $this->ds->exec([':disclosure_id' => $disclosure_id]);
    }

    public function printDisclosure($Disclosure, $printer_id)
    {
        try {
            if (!isset($Disclosure) || !isset($printer_id)) throw new Exception('Disclosure Missing or Printer is not configured.');

            if (is_string($Disclosure->document_inventory_ids)) {
                $ids = explode(",", $Disclosure->document_inventory_ids);
                for ($i = 0; $i < count($ids); $i++) {
                    $ids[$i] = (int)$ids[$i];
                }
                $Disclosure->document_inventory_ids = $ids;
            }

            $template = $this->getDisclosureTemplate($Disclosure);

            include_once(ROOT . '/dataProvider/Printer.php');
            $Printer = new \Printer();
            $cover_letter = $this->createCoverLetter($template, $Disclosure, false);
            $Printer->doTempDocumentPrint($printer_id, $cover_letter->id);

            foreach ($Disclosure->document_inventory_ids as $id) {
                $Printer->doDocumentPrint($printer_id, $id);
            }

            return (object)[
                'success' => true,
                'errorMsg' => ''
            ];

        } catch (Exception $e) {
            error_log($e->getMessage());

            return (object)[
                'success' => false,
                'errorMsg' => $e->getMessage()
            ];
        }
    }

    public function downloadDisclosureDocuments($Disclosure)
    {
        try {
            if (!isset($Disclosure)) throw new Exception('Disclosure Missing');

            $temp_disclosure_path = $this->createTempDisclosureDir($Disclosure);

            $zip_path = $this->createDisclosureZipFile($Disclosure, $temp_disclosure_path);

            if (!file_exists($zip_path)) throw new Exception('Zip file dont exist');

            header('Content-Type: application/zip');
            header('Content-Transfer-Encoding: Binary');
            header('Content-disposition: attachment; filename="' . basename($zip_path) . '"');
            flush(); // Flush system output buffer
            readfile($zip_path);

            $this->deleteFolder($temp_disclosure_path);

            return (object)[
                'success' => true,
                'errorMsg' => ''
            ];

        } catch (Exception $e) {
            if (isset($temp_disclosure_path)) $this->deleteFolder($temp_disclosure_path);

            error_log($e->getMessage());

            return (object)[
                'success' => false,
                'errorMsg' => $e->getMessage()
            ];
        }
    }

    public function packageDisclosureDocuments($Disclosure)
    {
        try {
            if (!isset($Disclosure)) throw new Exception('Disclosure Missing');

            $temp_disclosure_path = $this->createTempDisclosureDir($Disclosure);

            $zip_path = $this->createDisclosureZipFile($Disclosure, $temp_disclosure_path);

            if (!file_exists($zip_path)) throw new Exception('Zip file dont exist');

//            header('Content-Type: application/zip');
//            header('Content-Transfer-Encoding: Binary');
//            header('Content-disposition: attachment; filename="' . basename($zip_path) . '"');
//            flush(); // Flush system output buffer
//            readfile($zip_path);

            //$this->deleteFolder($temp_disclosure_path);

            return (object)[
                'success' => true,
                'zip_path' => $zip_path
            ];

        } catch (Exception $e) {
            if (isset($temp_disclosure_path)) $this->deleteFolder($temp_disclosure_path);

            error_log($e->getMessage());

            return (object)[
                'success' => false,
                'errorMsg' => $e->getMessage()
            ];
        }
    }

    public function burnDisclosure($Disclosure,$burner)
    {
        try {
            if (!isset($Disclosure)) throw new Exception('Disclosure Missing');
            if (!isset($burner)) throw new Exception('Burner Missing');

            $temp_disclosure_path = $this->createTempDisclosureDir($Disclosure);

            $burner_temp_path = $this->createTempBurnerDirectory($Disclosure, $temp_disclosure_path);

            $this->copyTempBurnerDirToBurner($burner_temp_path,$burner);

            $this->deleteFolder($temp_disclosure_path);

            return (object)[
                'success' => true,
                'errorMsg' => ''
            ];

        } catch (Exception $e) {
            if (isset($temp_disclosure_path)) $this->deleteFolder($temp_disclosure_path);

            error_log($e->getMessage());

            return (object)[
                'success' => false,
                'errorMsg' => $e->getMessage()
            ];
        }
    }
}
