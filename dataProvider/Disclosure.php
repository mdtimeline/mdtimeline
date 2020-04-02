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

    private function createSaveCoverLetter($Disclosure, $path)
    {
        $template = $this->getDisclosureTemplate();

        $cover_letter = $this->createCoverLetter($template, $Disclosure, true);
        if (!file_put_contents($path, base64_decode($cover_letter->document))) throw new Exception('Cover letter could not be saved');
    }

    private function createDisclosureZipFile($Disclosure, $disclosure_temp_path)
    {
        try {
            $disclosure_zip_name = "disclosure-{$Disclosure->id}";
            $disclosure_zip_path = $disclosure_temp_path . '/' . $disclosure_zip_name . '.zip';
            $cover_letter_path = $disclosure_temp_path . '/index.pdf';

            if (!file_exists(site_temp_path) || !is_writable(site_temp_path)) {
                throw new Exception('Temp folder dont exist or is nor writable');
            }

            if (!file_exists($disclosure_temp_path)) {
                if (!mkdir($disclosure_temp_path))
                    throw new Exception('Disclosure temp folder could not be created');
            }

            $zip = new ZipArchive();

            if (file_exists($disclosure_zip_path)) {
                unlink($disclosure_zip_path);
            }
            if (file_exists($cover_letter_path)) {
                unlink($cover_letter_path);
            }

            if ($zip->open($disclosure_zip_path, ZIPARCHIVE::CREATE) != TRUE) {
                throw new Exception('Disclosure zip folder could not be created');
            }

            $this->createSaveCoverLetter($Disclosure, $cover_letter_path);

            if (!$zip->addFile($cover_letter_path, basename($cover_letter_path))) {
                throw new Exception('Disclosure index could not be added to the zip file');
            }

            include_once(ROOT . '/dataProvider/DocumentHandler.php');
            $DocumentHandler = new DocumentHandler();
            $document_inventory_ids = explode(',', $Disclosure->document_inventory_ids);

            foreach ($document_inventory_ids as $document_inventory_id) {

                $document = $DocumentHandler->getPatientDocument(['id' => $document_inventory_id], true);
                $document_base64 = base64_decode($document['document']);
                $document_name = 'documents/' . $document['id'] .'_'.$document['name'];
                if (!$zip->addFromString($document_name, $document_base64)) {
                    throw new Exception("{$document['name']} could not be added to the zip file");
                }

            }

            // close and save archive
            $zip->close();

            return $disclosure_zip_path;
        } catch (Exception $e) {
            throw $e;
        }

    }

    private function deleteFolder($temp_dir)
    {
        if (file_exists($temp_dir)) {
            exec("rm -rf $temp_dir");
        }
    }

    private function getDisclosureTemplate()
    {
        include_once(ROOT . '/dataProvider/ContentManagement.php');
        $ContentManagement = new \ContentManagement();
        $template = $ContentManagement->generateContentManagement('disclosure', 'en', null, null);
        if (!$template) throw new Exception('Disclosure template not configured!');
        return $template;
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

    private function createDisclosureBurnerDir()
    {
        $disclosure_burner_path = site_temp_path . '/' . uniqid('disclosure_burner_');
//        $disclosure_burner_path = site_temp_path . '/disclosure-burner';
        if (file_exists($disclosure_burner_path)) return $disclosure_burner_path;
        if (!mkdir($disclosure_burner_path)) throw new Exception('Could not create disclosure burner temp directory');

        return $disclosure_burner_path;
    }

    private function createTempBurnerRootDir($disclosure_burner_path)
    {
        $temp_burner_path = $disclosure_burner_path . '/' . date('YmdHisv');
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

    private function copyPDFsToDataPath($document_inventory_ids, $data_path)
    {
        include_once(ROOT . '/dataProvider/DocumentHandler.php');
        $DocumentHandler = new DocumentHandler();
        $document_inventory_ids = explode(',', $document_inventory_ids);

        foreach ($document_inventory_ids as $document_inventory_id) {

            $document = $DocumentHandler->getPatientDocument(['id' => $document_inventory_id], false);
            $source = $document['path'] . '/' . $document['name'];
            $destination_path = $data_path . '/' . $document['name'];
            if (!copy($source, $destination_path)){
                throw new Exception("Could not copy pdf from {$source} to {$destination_path}");
            }

        }
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

    private function copyTempBurnerDirToBurner($burner_root_dir)
    {
        $dest = Globals::getGlobal('disclosure_burner_directory');
        if ($dest === false) throw new Exception('Global disclosure_burner_directory not configured!');
        if (!is_writable($dest)) throw new Exception("Directory {$dest} is not writable or does not have permissions");

        $dest = $dest . '/' . basename($burner_root_dir);
        $this->copyDirectory($burner_root_dir, $dest);
    }

    private function copyBurnerLabelToDir($path){
        $src = Globals::getGlobal('disclosure_burner_label_location');
        if ($src === false) throw new Exception('Global disclosure_burner_label_location not configured!');
        if (!is_readable($src)) throw new Exception("File {$src} is not readable or does not have permissions");

        $dst = $path . '/' . basename($src);

        if (!copy($src, $dst)) throw new Exception("Could not copy burner label from {$src} to {$path}");
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

            $template = $this->getDisclosureTemplate();

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

            $disclosure_temp_path = site_temp_path . '/' . uniqid('disclosures_');

            $zip_path = $this->createDisclosureZipFile($Disclosure, $disclosure_temp_path);

            if (!file_exists($zip_path)) throw new Exception('Zip file dont exist');

            header('Content-Type: application/zip');
            header('Content-Transfer-Encoding: Binary');
            header('Content-disposition: attachment; filename="' . basename($zip_path) . '"');
            flush(); // Flush system output buffer
            readfile($zip_path);

            $this->deleteFolder($disclosure_temp_path);

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

    public function burnDisclosure($Disclosure)
    {
        try {
            if (!isset($Disclosure)) throw new Exception('Disclosure Missing');

            $disclosure_burner_dir = $this->createDisclosureBurnerDir();

            $root_dir = $this->createTempBurnerRootDir($disclosure_burner_dir);

            $data_dir = $this->createBurnerDataDir($root_dir);

            $print_dir = $this->createBurnerPrintDir($root_dir);

            $this->copyPDFsToDataPath($Disclosure->document_inventory_ids, $data_dir);

            $this->createSaveCoverLetter($Disclosure, $data_dir . '/index.pdf');

            $config_file_path = $this->createConfigTextFile($Disclosure, basename($root_dir), $print_dir);

            $this->copyBurnerLabelToDir($print_dir);

            $this->copyTempBurnerDirToBurner($root_dir);

            $this->deleteFolder($disclosure_burner_dir);

            return (object)[
                'success' => true,
                'errorMsg' => ''
            ];

        } catch (Exception $e) {
            if (isset($disclosure_burner_dir)) $this->deleteFolder($disclosure_burner_dir);

            error_log($e->getMessage());

            return (object)[
                'success' => false,
                'errorMsg' => $e->getMessage()
            ];
        }
    }
}
