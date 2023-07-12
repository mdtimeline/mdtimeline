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

include_once(ROOT . '/classes/Crypt.php');
include_once(ROOT . '/dataProvider/Documents.php');
include_once(ROOT . '/dataProvider/FileSystem.php');
include_once(ROOT . '/dataProvider/DoctorsNotes.php');

class DocumentHandler
{

    private $db;
    private $documents;

    private $pid;
    private $docType;
    private $workingDir;
    private $fileName;

    private $filesPerInstance = 50000;

    /**
     * @var MatchaCUP
     */
    private $dt;

    /**
     * @var MatchaCUP
     */
    public $d;

    /**
     * @var MatchaCUP
     */
    public $ad;

    /**
     * @var MatchaCUP
     */
    private $t;

    /**
     * @var FileSystem
     */
    private $FileSystem;
    /**
     * @var FileSystem
     */
    private $file_systems = [];


    private $storeAsFile = true;

    private $doctorsnotes;

    private $directory_permission = 0755;
    private $document_permission = 0644;

    private $mime_types_ext = [

        'application/pdf' => 'pdf',
        'application/msword' => 'doc',
        'application/xml' => 'xml',
        'image/gif' => 'gif',
        'image/png' => 'png',
        'image/jpg' => 'jpg',
        'image/jpeg' => 'jpg',
        'image/bmp' => 'bmp',
        'image/mpeg' => 'mp3',
        'audio/x-wav' => 'wav',
        'video/mpeg' => 'mpg',
        'video/gif' => 'avi',
        'video/3gpp' => '3gp',
        'text/gif' => 'xml',
        'image/xml' => 'wav',
        'text/plain' => 'txt',
        'text/html' => 'html',
        'image/tiff' => 'tif',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx'
    ];

    function __construct()
    {
        $this->FileSystem = new FileSystem();

        $file_systems = $this->FileSystem->getFileSystems(null);

        foreach ($file_systems as $file_system) {
            $this->file_systems[$file_system['id']] = $file_system;
        }

        return;
    }

    private function setDocumentsTemplatesModel()
    {
        if (!isset($this->dt))
            $this->dt = MatchaModel::setSenchaModel('App.model.administration.DocumentsTemplates');
    }

    private function setPatientDocumentModel()
    {
        if (!isset($this->d))
            $this->d = MatchaModel::setSenchaModel('App.model.patient.PatientDocuments');
    }

    private function setAdministrativeDocumentModel()
    {
        if (!isset($this->ad))
            $this->ad = MatchaModel::setSenchaModel('App.model.documents.AdministrativeDocuments');
    }

    private function setPatientDocumentTempModel()
    {
        if (!isset($this->t))
            $this->t = MatchaModel::setSenchaModel('App.model.patient.PatientDocumentsTemp');
    }

    private function createTempTxtFile($data)
    {
        $temp_path = site_temp_path . '/' . uniqid('tempTXT_') . '.txt';

        if (!file_put_contents($temp_path, $data)) {
            error_log("Could not create temp txt file on {$temp_path}");
            return false;
        }

        return $temp_path;
    }

    /**
     * @param      $params
     * @param bool $includeDocument
     * @param bool $return_binary
     * @param bool $compressed
     *
     * @return mixed
     */
    public function getPatientDocuments($params, $includeDocument = false, $return_binary = false, $compressed = false)
    {
        $this->setPatientDocumentModel();
        $this->d->setOrFilterProperties(['docTypeCode', 'id']);
        $records = $this->d->load($params)->leftJoin(
            ['code_type' => 'docTypeConcept'],
            'combo_lists_options',
            'docTypeCode',
            'option_value',
            '=',
            "`list_key` = 'doc_type_cat'"
        )->sortBy('id','DESC')->all();
        $concepts = [];

        if(ACL::hasPermission('allow_access_general_documents')){
            $concepts[] = 'GENERAL';
        }
        if(ACL::hasPermission('allow_access_admin_documents')){
            $concepts[] = 'ADMIN';
        }
        if(ACL::hasPermission('allow_access_billing_documents')){
            $concepts[] = 'BILLING';
        }
        if(ACL::hasPermission('allow_access_clinical_documents')){
            $concepts[] = 'CLINICAL';
        }
        if(ACL::hasPermission('allow_access_radiology_documents')){
            $concepts[] = 'RADIOLOGY';
        }

        /** lets unset the actual document data */
        if (isset($records['data'])) {
            foreach ($records['data'] as $i => $record) {

                if(isset($record['docTypeConcept']) && $record['docTypeConcept'] !== '' && !in_array($record['docTypeConcept'], $concepts)){
                    unset($records['data'][$i]);
                    continue;
                }

                if ($records['data'][$i]['entered_in_error']) {
                    $records['data'][$i]['docType'] = 'ENTERED IN ERROR';
                    $records['data'][$i]['docTypeCode'] = 'ZZZ';
                }

                if (!$includeDocument) {
                    unset($records['data'][$i]['document']);
                } else {
                    $records['data'][$i]['document'] = $this->getDocumentData($record, $return_binary);
                }
            }
        }

        $records['total'] = count($records['data']);
        $records['data'] = array_values($records['data']);
        return $records;
    }

    /**
     * @param      $params
     * @param bool $includeDocument
     * @param bool $return_binary
     * @param bool $compressed
     *
     * @return mixed
     */
    public function getAdministrativeDocuments($params, $includeDocument = false, $return_binary = false, $compressed = false)
    {
        $this->setAdministrativeDocumentModel();
        $this->ad->setOrFilterProperties(['docTypeCode', 'id']);
        $records = $this->ad->load($params)->leftJoin(
            ['code_type' => 'docTypeConcept'],
            'combo_lists_options',
            'docTypeCode',
            'option_value',
            '=',
            "`list_key` = 'doc_type_admin_cat'"
        )->all();
        $concepts = [];

//        if(ACL::hasPermission('allow_access_administrative_documents')){
//            $concepts[] = 'ADMINISTRATIVE';
//        }

//        if(ACL::hasPermission('allow_access_general_documents')){
//            $concepts[] = 'GENERAL';
//        }
//        if(ACL::hasPermission('allow_access_admin_documents')){
//            $concepts[] = 'ADMIN';
//        }
//        if(ACL::hasPermission('allow_access_billing_documents')){
//            $concepts[] = 'BILLING';
//        }
//        if(ACL::hasPermission('allow_access_clinical_documents')){
//            $concepts[] = 'CLINICAL';
//        }
//        if(ACL::hasPermission('allow_access_radiology_documents')){
//            $concepts[] = 'RADIOLOGY';
//        }

        /** lets unset the actual document data */
        if (isset($records['data'])) {
            foreach ($records['data'] as $i => $record) {

                if(isset($record['docTypeConcept']) && $record['docTypeConcept'] !== '' && !in_array($record['docTypeConcept'], $concepts)){
                    unset($records['data'][$i]);
                    continue;
                }

                if ($records['data'][$i]['entered_in_error']) {
                    $records['data'][$i]['docType'] = 'ENTERED IN ERROR';
                    $records['data'][$i]['docTypeCode'] = 'ZZZ';
                }

                if (!$includeDocument) {
                    unset($records['data'][$i]['document']);
                } else {
                    $records['data'][$i]['document'] = $this->getDocumentData($record, $return_binary);
                }
            }
        }

        $records['total'] = count($records['data']);
        $records['data'] = array_values($records['data']);
        return $records;
    }

    public function getPatientDocumentsBySql($sql, $includeDocument = false, $return_binary = false, $compressed = false)
    {
        $this->setPatientDocumentModel();
        $this->d->setOrFilterProperties(['docTypeCode', 'id']);
        $records = $this->d->sql($sql)->all();

        /** lets unset the actual document data */
        if (isset($records['data'])) {
            foreach ($records['data'] as $i => $record) {

                if ($records['data'][$i]['entered_in_error']) {
                    $records['data'][$i]['docType'] = 'ENTERED IN ERROR';
                    $records['data'][$i]['docTypeCode'] = 'ZZZ';
                }

                if (!$includeDocument) {
                    unset($records['data'][$i]['document']);
                } else {
                    $records['data'][$i]['document'] = $this->getDocumentData($record, $return_binary);
                }
            }
        }
        return $records;
    }

    /**
     * @param $params
     * @param $include_document
     * @param $retun_binary
     *
     * @return mixed
     */
    public function getPatientDocument($params, $include_document = false, $retun_binary = false)
    {
        $this->setPatientDocumentModel();

        if ($include_document) {
            $record = $this->d->load($params)->leftJoin(['dir_path' => 'filesystem_path'], 'filesystems', 'filesystem_id', 'id')->one();
        } else {
            $record = $this->d->load($params)->one();
        }

        if (isset($record['data'])) {
            $record = $record['data'];
        }

        if ($record !== false && $include_document) {

            if (isset($record['filesystem_path']) && isset($record['path'])) {
                $file_path = rtrim($record['filesystem_path'], '/') . '/' . rtrim($record['path'], '/') . '/' . $record['name'];
            } else if (isset($record['path'])) {
                $file_path = rtrim($record['path'], '/') . '/' . $record['name'];
            } else {
                $file_path = '';
            }

            $is_file = file_exists($file_path);

            if ($is_file) {
                $record['document'] = file_get_contents($file_path);
            } elseif (isset($record['document_instance']) && $record['document_instance'] != '') {
                $dd = MatchaModel::setSenchaModel('App.model.administration.DocumentData', false, $record['document_instance']);
                $data = $dd->load($record['document_id'])->one();
                if ($data !== false) {
                    $record['document'] = $data['document'];
                }
            }

            if (isset($record['document']) && !empty($record['document'])) {
                $is_binary = $this->isBinary($record['document']);
                if ($retun_binary && !$is_binary) {
                    $record['document'] = base64_decode($record['document']);
                } elseif (!$retun_binary && $is_binary) {
                    $record['document'] = base64_encode($record['document']);
                }
            }
        }

        return $record;
    }
    public function getPatientDocumentByGlobalId($global_id, $include_document = false, $retun_binary = false)
    {
        $this->setPatientDocumentModel();
        $this->d->addFilter('global_id', $global_id);
        return  $this->getPatientDocument(null, $include_document, $retun_binary);
    }

    public function getPatientDocumentBySql($sql, $include_document = false, $retun_binary = false)
    {
        $this->setPatientDocumentModel();
        $record = $this->d->sql($sql)->one();

        if ($record !== false && $include_document) {

            if (isset($record['filesystem_path']) && isset($record['path'])) {
                $file_path = rtrim($record['filesystem_path'], '/') . '/' . rtrim($record['path'], '/') . '/' . $record['name'];
            } else if (isset($record['path'])) {
                $file_path = rtrim($record['path'], '/') . '/' . $record['name'];
            } else {
                $file_path = '';
            }

            $is_file = file_exists($file_path);

            if ($is_file) {
                $record['document'] = file_get_contents($file_path);
            } elseif (isset($record['document_instance']) && $record['document_instance'] != '') {
                $dd = MatchaModel::setSenchaModel('App.model.administration.DocumentData', false, $record['document_instance']);
                $data = $dd->load($record['document_id'])->one();
                if ($data !== false) {
                    $record['document'] = $data['document'];
                }
            }

            if (isset($record['document']) && !empty($record['document'])) {
                $is_binary = $this->isBinary($record['document']);
                if ($retun_binary && !$is_binary) {
                    $record['document'] = base64_decode($record['document']);
                } elseif (!$retun_binary && $is_binary) {
                    $record['document'] = base64_encode($record['document']);
                }
            }
        }

        return $record;
    }

    private function getDocumentData($record, $return_binary)
    {

        $document = '';

        $file_system = isset($this->file_systems[$record['filesystem_id']]) ? $this->file_systems[$record['filesystem_id']] : false;

        if ($file_system !== false) {
            $file_path = rtrim($file_system['dir_path'], '/') . '/' . trim($record['path'], '/') . '/' . ltrim($record['name'], '/');
            $is_file = isset($record['path']) && $record['path'] != '' && file_exists($file_path);
        } else {
            $file_path = '';
            $is_file = false;
        }


        if ($is_file) {
            $document = file_get_contents($file_path);
        } elseif (isset($record['document_instance']) && $record['document_instance'] != '') {
            $dd = MatchaModel::setSenchaModel('App.model.administration.DocumentData', false, $record['document_instance']);
            $data = $dd->load($record['document_id'])->one();
            if ($data !== false) {
                $document = $data['document'];
            }
        }

        if (!empty($document)) {
            $is_binary = $this->isBinary($document);

            if ($return_binary && !$is_binary) {
                $document = base64_decode($document);
            } elseif (!$return_binary && $is_binary) {
                $document = base64_encode($document);
            }
        }

        return $document;

    }

    /**
     * @param $params
     *
     * @return array|object
     */
    public function addPatientDocument($params)
    {
        $this->setPatientDocumentModel();

        Matcha::pauseLog(true);

        if (is_array($params)) {
            foreach ($params as $i => $param) {
                /** remove the mime type */
                $params[$i]->document = $this->trimBase64($params[$i]->document);

                /** encrypted if necessary */
                if (isset($params[$i]->encrypted) && $params[$i]->encrypted) {
                    $params[$i]->document = MatchaUtils::encrypt($params[$i]->document);
                };
                $binary_file = $this->isBinary($params[$i]->document) ?
                    $params[$i]->document : base64_decode($params[$i]->document);
                $params[$i]->hash = hash('sha256', $binary_file);
            }
        } else {
            /** remove the mime type */
            $params->document = $this->trimBase64($params->document);
            /** encrypted if necessary */
            if (isset($params->encrypted) && $params->encrypted) {
                $params->document = MatchaUtils::encrypt($params->document);
            };
            $binary_file = $this->isBinary($params->document) ?
                $params->document : base64_decode($params->document);

            $params->hash = hash('sha256', $binary_file);
        }

        $results = $this->d->save($params);

        if (isset($results['data']) && is_array($results['data'])) {
            foreach ($results['data'] as &$result) {
                if ($this->storeAsFile) {
                    $this->handleDocumentFile($result);
                } else {
                    $this->handleDocumentData($result);
                }
            }
        } else if (isset($results['data']) && is_object($results['data'])) {
            if ($this->storeAsFile) {
                $this->handleDocumentFile($results['data']);
            } else {
                $this->handleDocumentData($results['data']);
            }
        } else {
            if ($this->storeAsFile) {
                $this->handleDocumentFile($results);
            } else {
                $this->handleDocumentData($results);
            }
        }

        Matcha::pauseLog(false);

        return $results;
    }

    /**
     * @param $params
     *
     * @return array|object
     */
    public function addAdministrativeDocument($params)
    {
        $this->setAdministrativeDocumentModel();

        Matcha::pauseLog(true);

        if (is_array($params)) {
            foreach ($params as $i => $param) {
                /** remove the mime type */
                $params[$i]->document = $this->trimBase64($params[$i]->document);

                /** encrypted if necessary */
                if (isset($params[$i]->encrypted) && $params[$i]->encrypted) {
                    $params[$i]->document = MatchaUtils::encrypt($params[$i]->document);
                };
                $binary_file = $this->isBinary($params[$i]->document) ?
                    $params[$i]->document : base64_decode($params[$i]->document);
                $params[$i]->hash = hash('sha256', $binary_file);
            }
        } else {
            /** remove the mime type */
            $params->document = $this->trimBase64($params->document);
            /** encrypted if necessary */
            if (isset($params->encrypted) && $params->encrypted) {
                $params->document = MatchaUtils::encrypt($params->document);
            };
            $binary_file = $this->isBinary($params->document) ?
                $params->document : base64_decode($params->document);

            $params->hash = hash('sha256', $binary_file);
        }

        $results = $this->ad->save($params);

        if (isset($results['data']) && is_array($results['data'])) {
            foreach ($results['data'] as &$result) {
                if ($this->storeAsFile) {
                    $this->handleDocumentFile($result);
                } else {
                    $this->handleDocumentData($result);
                }
            }
        } else if (isset($results['data']) && is_object($results['data'])) {
            if ($this->storeAsFile) {
                $this->handleDocumentFile($results['data']);
            } else {
                $this->handleDocumentData($results['data']);
            }
        } else {
            if ($this->storeAsFile) {
                $this->handleDocumentFile($results);
            } else {
                $this->handleDocumentData($results);
            }
        }

        Matcha::pauseLog(false);

        return $results;
    }

    /**
     * This logic is to eventually split the document into multiples tables
     * using the sencha model instance
     *
     * @param $document
     */
    private function handleDocumentData(&$document)
    {

        try {
            $document = (object)$document;
            $instance = floor($document->id / $this->filesPerInstance) + 1;
            $conn = Matcha::getConn();
            $sth = $conn->prepare("SHOW TABLES LIKE 'documents_data_{$instance}'");
            $sth->execute();
            $table = $sth->fetch(PDO::FETCH_ASSOC);

            if ($table === false) {
                $document_model = MatchaModel::setSenchaModel('App.model.administration.DocumentData', true, $instance);
            } else {
                $document_model = MatchaModel::setSenchaModel('App.model.administration.DocumentData', false, $instance);
            }

            if ($document_model === false) {
                throw new Exception("Unable to create App.model.administration.DocumentData model instance '{$instance}'");
            }

            $document->document = $this->base64ToBinary($document->document);
            $file_info = new finfo(FILEINFO_MIME_TYPE);
            $mime_type = $file_info->buffer($document->document);
            if (!isset($this->mime_types_ext[$mime_type])) {
                throw new Exception('File extension not supported. document_id: ' . $document->id . ' mime_type: ' . $mime_type);
            }
            $document_code = isset($document->docTypeCode) ? $document->docTypeCode : '';

            if ($mime_type == 'application/xml' && preg_match('/\<!DOCTYPE html/', $document->document)) {
                $mime_type = 'text/html';
            }

            $ext = $this->mime_types_ext[$mime_type];
            $file_name = $document_code . '_' . $document->id . '_' . $document->pid . '.' . $ext;

            $document->name = $file_name;

            //error_log('DOCUMENT');
            $data = new stdClass();
            $data->pid = $document->pid;
            $data->document = $document->document;
            $record = $document_model->save($data);
            //error_log('DOCUMENT DATA COMPLETED');

            $document->document = '';
            $document->document_instance = $instance;
            $document->document_id = $record->id;
            $sth = $conn->prepare("UPDATE patient_documents SET document = '', `name` = :file_name, document_instance = :doc_ins, document_id = :doc_id WHERE id = :id;");
            $sth->execute([
                ':id' => $document->id,
                ':file_name' => $document->name,
                ':doc_ins' => $document->document_instance,
                ':doc_id' => $document->document_id
            ]);
            //error_log('DOCUMENT COMPLETE');
            unset($document->document);

            unset($data, $record, $document_model);
        } catch (Exception $e) {
            error_log('Error Converting Document');
            error_log($e->getMessage());
        }
    }

    /**
     * @param $document
     */
    private function handleDocumentFile(&$document)
    {

        try {
            $document = (object)$document;
            $conn = Matcha::getConn();

            $filesystem = $this->FileSystem->getOnlineFileSystem();
            if ($filesystem !== false) {
                $filesystem_path = rtrim($filesystem['dir_path'], '/');
                $filesystem_id = $filesystem['id'];
            } else {
                $filesystem_path = '';
                $filesystem_id = 0;
            }

            $document_path = $filesystem_path === '' ? (site_path . '/documents') : '';

            /**
             * change date to path  2016-01-23 => 2016/01/23
             */
            $document_path .= '/' . str_ireplace(['-', ' '], '/', substr($document->date, 0, 10));

            if (!file_exists($filesystem_path . $document_path)) {

                @mkdir($filesystem_path . $document_path, $this->directory_permission, true);
                $directories = explode('/', trim($document_path, '/'));

                if (isset($directories[0]) && $directories[0] !== '') {
                    @chmod($filesystem_path . '/' . $directories[0], $this->directory_permission);
                }
                if (isset($directories[1]) && $directories[1] !== '') {
                    @chmod($filesystem_path . '/' . $directories[0] . '/' . $directories[1], $this->directory_permission);
                }
                if (isset($directories[2]) && $directories[2] !== '') {
                    @chmod($filesystem_path . '/' . $directories[0] . '/' . $directories[1] . '/' . $directories[2], $this->directory_permission);
                }
            }

            if (isset($document->document_instance) && $document->document_instance > 0 && (!isset($document->document) || $document->document == '')) {
                $dd = MatchaModel::setSenchaModel('App.model.administration.DocumentData', false, $document->document_instance);
                if ($dd !== false) {
                    $data = $dd->load($document->document_id)->one();
                    if ($data !== false) {
                        $document->document = $data['document'];
                    }
                    unset($data);
                }
            }

            $document->document = $this->base64ToBinary($document->document);
            $file_info = new finfo(FILEINFO_MIME_TYPE);
            $mime_type = $file_info->buffer($document->document);

            if (!isset($this->mime_types_ext[$mime_type])) {
                $error = 'File extension not supported. document_id: ' . $document->id . ' mime_type: ' . $mime_type;
                error_log($error);
                return [
                    'success' => false,
                    'error' => $error
                ];
            }

            $document_code = isset($document->docTypeCode) ? $document->docTypeCode : '';

            if ($mime_type == 'application/xml' && preg_match('/\<!DOCTYPE html/', $document->document)) {
                $mime_type = 'text/html';
            }

            $ext = $this->mime_types_ext[$mime_type];
            $file_name = $document_code . '_' . $document->id . '_' . $document->pid . '.' . $ext;
            $document->fullpath = $path = $filesystem_path . $document_path . '/' . $file_name;

//            if (file_exists($path) && !unlink($path)) {
//                $error = 'File name exist and unable to unlink. document_id: ' . $document->id . ' path: ' . $path;
//                error_log($error);
//                return [
//                    'success' => false,
//                    'error' => $error
//                ];
//            }

            if (file_put_contents($path, $document->document) === false) {
                $error = 'Unable to write file. document_id: ' . $document->id . ' path: ' . $path;
                error_log($error);
                return [
                    'success' => false,
                    'error' => $error
                ];
            }

            if (!file_exists($path)){
                $error = 'Unable to validate file. document_id: ' . $document->id . ' path: ' . $path;
                error_log($error);
                return [
                    'success' => false,
                    'error' => $error
                ];
            }

            chmod($path, $this->document_permission);

            include_once(ROOT . '/dataProvider/Patient.php');
            $patient = new Patient();
            $p = $patient->getPatientByPid($document->pid);
            $code = isset($document->code) ? $document->code: '';

            if (isset($p) && isset($p['pubpid']) && $code == '') {
                $code = $p['pubpid'] . '~' . $document_code . '~' . $file_name;
            }

            $sth = $conn->prepare("UPDATE patient_documents SET document = '', filesystem_id = :filesystem_id, path = :path, `name` = :name, `code` = :code WHERE id = :id;");
            $sth->execute([
                ':id' => $document->id,
                ':filesystem_id' => $filesystem_id,
                ':path' => $document_path,
                ':name' => $file_name,
                ':code' => $code
            ]);

            //error_log('DOCUMENT COMPLETE');
            unset($document->document);
            unset($data, $record, $document_model);
            unset($patient, $p, $code);

        } catch (Exception $e) {
            error_log('Error Converting Document');
            error_log($e->getMessage());
        }
    }

    /**
     * @param $base64Document
     * @param $prefix it is use to for pdf file name
     * @return bool|string
     */
    private function saveDocumentOnTempFolder($base64Document, $prefix)
    {
        $path = site_temp_path . '/' . uniqid($prefix) . '.pdf';

        if (!file_put_contents($path, base64_decode($base64Document), FILE_USE_INCLUDE_PATH)) {
            error_log('Temp document could not be saved on this location. {$path}');
            return false;
        }

        return $path;
    }

    private function deleteDocumentOnTempFolder($documentPath)
    {
        return unlink($documentPath);
    }

    /**
     * @param $params
     *
     * @return array
     */
    public function updatePatientDocument($params)
    {
        $this->setPatientDocumentModel();

        Matcha::pauseLog(true);


        if (is_array($params)) {
            foreach ($params as &$param) {
                unset($param->document, $param->hash);
            }
        } else {
            if (!isset($params->edit_document) || $params->edit_document !== true) {
                unset($params->document, $params->hash);
            }
        }

        $results = $this->d->save($params);

        if (isset($params->edit_document) && $params->edit_document === true) {
            if (isset($results['data']) && is_array($results['data'])) {
                foreach ($results['data'] as &$result) {
                    if ($this->storeAsFile) {
                        $this->handleDocumentFile($result);
                    } else {
                        $this->handleDocumentData($result);
                    }
                }
            } else if (isset($results['data']) && is_object($results['data'])) {
                if ($this->storeAsFile) {
                    $this->handleDocumentFile($results['data']);
                } else {
                    $this->handleDocumentData($results['data']);
                }
            } else {
                if ($this->storeAsFile) {
                    $this->handleDocumentFile($results);
                } else {
                    $this->handleDocumentData($results);
                }
            }
        }

        Matcha::pauseLog(false);

        return $results;
    }

    /**
     * @param $params
     *
     * @return array
     */
    public function updateAdministrativeDocument($params)
    {
        $this->setAdministrativeDocumentModel();

        Matcha::pauseLog(true);


        if (is_array($params)) {
            foreach ($params as &$param) {
                unset($param->document, $param->hash);
            }
        } else {
            if (!isset($params->edit_document) || $params->edit_document !== true) {
                unset($params->document, $params->hash);
            }
        }

        $results = $this->ad->save($params);

        if (isset($params->edit_document) && $params->edit_document === true) {
            if (isset($results['data']) && is_array($results['data'])) {
                foreach ($results['data'] as &$result) {
                    if ($this->storeAsFile) {
                        $this->handleDocumentFile($result);
                    } else {
                        $this->handleDocumentData($result);
                    }
                }
            } else if (isset($results['data']) && is_object($results['data'])) {
                if ($this->storeAsFile) {
                    $this->handleDocumentFile($results['data']);
                } else {
                    $this->handleDocumentData($results['data']);
                }
            } else {
                if ($this->storeAsFile) {
                    $this->handleDocumentFile($results);
                } else {
                    $this->handleDocumentData($results);
                }
            }
        }

        Matcha::pauseLog(false);

        return $results;
    }

    /**
     * @param $params
     *
     * @return mixed
     */
    public function destroyPatientDocument($params)
    {
        $this->setPatientDocumentModel();
        return $this->d->destroy($params);
    }

    /**
     * @param $params
     * @param $return_binary
     * @return object|stdClass
     */
    public function getTempDocument($params, $return_binary = false)
    {
        $this->setPatientDocumentTempModel();
        $record = $this->t->load($params)->one();

        $record['document'] = ($return_binary && !$this->isBinary($record['document']) ?
            base64_decode($record['document']) : $record['document']);

        return $record;
    }

    /**
     * @param $base64_documents
     * @param bool $return_as_base64
     * @return string
     */
    public function mergeDocumentsByBase64($base64_documents, $return_as_base64 = false)
    {
        $documents_paths = [];

        foreach ($base64_documents as $base64_document) {
            $filePath = $this->saveDocumentOnTempFolder($base64_document, 'documentToMerge_');
            if ($filePath === false) return false;

            $documents_paths[] = $filePath;
        }

        $merged_document_data = $this->mergeDocuments($documents_paths, $return_as_base64);

        foreach ($documents_paths as $document_path) {
            $this->deleteDocumentOnTempFolder($document_path);
        }

        if ($merged_document_data === false) return false;

        return $merged_document_data;
    }

    public function mergeDocuments($file_paths, $return_as_base64 = false)
    {
        $Documents = new Documents();

        $merged_document = $Documents->mergeDocuments($file_paths);

        if ($merged_document === false) return false;

        if ($return_as_base64) return base64_encode($merged_document);

        return $merged_document;
    }

    /**
     * @param $params
     * @param bool $getDocument
     * @return object|stdClass|string
     */
    public function createTempDocument($params, $getDocument = false)
    {
        try {
            $this->setPatientDocumentTempModel();
            $template_id = isset($params->template_id) ? $params->template_id : null;
            $template_concept = isset($params->template_concept) ? $params->template_concept : 'default';

            Matcha::pauseLog(true);

            $params = (object)$params;
            $record = new stdClass();
            if (isset($params->document) && $params->document != '') {
                $record->document = $params->document;
            } elseif (isset($params->force_txt) && $params->force_txt === true) {
                $document_base64 = $this->createPDFfromTxt($params->body, true);
                if ($document_base64 === false) {
                    return false;
                }

                $record->document = $document_base64;
            } else {
                $pdf_format = isset($params->pdf_format) ? $params->pdf_format : null;

                $header_data = isset($params->header_data) && is_array($params->header_data) ? $params->header_data : null;
                $footer_data = isset($params->footer_data) && is_array($params->footer_data) ? $params->footer_data : null;
                $mail_cover_info = isset($params->mail_cover_info) && is_array($params->mail_cover_info) ? $params->mail_cover_info : null;

                $Documents = new Documents();
                $record->document = base64_encode(
                    $Documents->PDFDocumentBuilder((object)$params, '', $header_data, $footer_data, '', [], [], $pdf_format, $mail_cover_info, $template_id, $template_concept)
                );
            }
            $record->create_date = date('Y-m-d H:i:s');
            $record->document_name = isset($params->document_name) ? $params->document_name : '';
            $record = (object)$this->t->save($record);
            if (!$getDocument) {
                unset($record->document);
            }

            Matcha::pauseLog(false);
            return $record;
        } catch (Exception $e) {
            return $e->getMessage();
        }

    }

    public function createPDFfromTxt($txt, $return_as_base64 = false)
    {
        $command_args = [];
        $pdf_temp_path = site_temp_path . '/' . uniqid('txt_to_pdf_') . '.pdf';
        $text_path = $this->createTempTxtFile($txt);

        if ($text_path === false) {
            return false;
        }

        $command_args[] = 'wkhtmltopdf -s Letter';
        $command_args[] = $text_path;
        $command_args[] = $pdf_temp_path;

        $command = implode(' ', $command_args);

        $exec_result = exec($command);

        unlink($text_path);

        if ($exec_result != '') {
            error_log("wkhtmltopdf throw the following error ${$exec_result} Command run: ${command}");
            return false;
        }

        if (!file_exists($pdf_temp_path)) {
            error_log("Could not execute ${$command} or could not create file on ${pdf_temp_path}");
            return false;
        }

        $data = file_get_contents($pdf_temp_path);

        unlink($pdf_temp_path);

        if ($data === false) {
            error_log("Could not get the data of ${$pdf_temp_path}");
            return false;
        }

        if ($return_as_base64) {
            return base64_encode($data);
        }

        return $data;
    }


    /**
     * @param $params
     * @return object|stdClass
     */
    public function createRawTempDocument($params)
    {
        $this->setPatientDocumentTempModel();

        Matcha::pauseLog(true);

        $params = (object)$params;
        $record = new stdClass();
        $record->create_date = date('Y-m-d H:i:s');
        $record->document_name = $params->document_name;
        $record->document = base64_encode($params->document);
        $record = (object)$this->t->save($record);
        unset($record->document);

        Matcha::pauseLog(false);

        return $record;
    }

    public function createRawTempDocumentByNameAndDocument($name, $document)
    {
        $this->setPatientDocumentTempModel();

        Matcha::pauseLog(true);

        $record = new stdClass();
        $record->create_date = date('Y-m-d H:i:s');
        $record->document_name = $name;
        $record->document = base64_encode($document);
        $record = (object)$this->t->save($record);
        unset($record->document);

        Matcha::pauseLog(false);

        return $record;
    }

    public function destroyTempDocument($params)
    {
        $this->setPatientDocumentTempModel();
        return $this->t->destroy($params);
    }

    /**
     * @param $params
     *
     * @return array|mixed
     */
    public function transferTempDocument($params)
    {
        $this->setPatientDocumentModel();
        $this->setPatientDocumentTempModel();

        Matcha::pauseLog(false);

        if (isset($params->site) && isset($GLOBALS['worklist_dbs'][$params->site])) {
            \Matcha::$__conn = null;
            \Matcha::connect($GLOBALS['worklist_dbs'][$params->site]);
        }

        $record = $this->t->load($params)->one();

        if ($record === false) {
            error_log('ERROR: Temporary Document Record Not Found. PARAMS:');
            error_log(print_r($params, true));

            return [
                'success' => false,
                'error' => 'Temporary Document Record Not Found'
            ];
        }

        $params->document = $record['document'];
        $params->date = date('Y-m-d H:i:s');
        $params->name = 'transferred.pdf';
        unset($params->id);

        $params = $this->addPatientDocument($params);

        if (is_object($params) && isset($params->fullpath) && !file_exists($params->fullpath)) {
            error_log("Saved File Not Found on Directory. ID: {{$params->site}} SITE: {$params->site} PATH: {$params->fullpath}");
            error_log(print_r($params, true));
            return [
                'success' => false,
                'error' => "Saved File Not Found on Directory. ID: {{$params->site}} SITE: {$params->site} PATH: {$params->fullpath}"
            ];
        }

        \Matcha::$__conn = null;
        \Matcha::connect([
            'host' => site_db_host,
            'port' => site_db_port,
            'name' => site_db_database,
            'user' => site_db_username,
            'pass' => site_db_password,
            'app' => ROOT . '/app'
        ]);

        unset($params['data']->document);

        Matcha::pauseLog(true);

        return ['success' => true, 'record' => $params['data']];
    }

    private function trimBase64($base64)
    {

        if (!preg_match('/data:/', $base64)) {
            return $base64;
        }
        $pos = strpos($base64, ',');
        if ($pos === false) return $base64;
        return substr($base64, $pos + 1);
    }

    /**
     * @return string
     */
    protected function getDocumentUrl()
    {
        return $_SESSION['site']['url'] . '/patients/' . $this->pid . '/' . strtolower(str_replace(' ', '_', $this->docType)) . '/' . $this->fileName;
    }

    /**
     * @param $id
     * @return string
     */
    public function getDocumentPathById($id)
    {
        $conn = Matcha::getConn();
        $sth = $conn->prepare("SELECT * FROM patient_documents WHERE id = '{$id}'");
        $doc = $sth->fetch(PDO::FETCH_ASSOC);
        return site_path . '/patients/' . $doc['pid'] . '/' . strtolower(str_replace(' ', '_', $doc['docType'])) . '/' . $doc['name'];
    }

    /**
     * @param $file
     * @return string
     */
    protected function reNameFile($file)
    {
        $foo = explode('.', $file['filePath']['name']);
        $ext = end($foo);
        return $this->fileName = $this->setName() . '.' . $ext;
    }

    /**
     * @return string
     */
    protected function nameFile()
    {
        return $this->fileName = $this->setName() . '.pdf';
    }

    /**
     * @return int
     */
    protected function setName()
    {
        $name = time();
        while (file_exists($this->workingDir . '/' . $name)) {
            $name = time();
        }
        return $name;
    }

    /**
     * @param $params
     * @return string
     */
    protected function getPatientDir($params)
    {
        if (is_array($params)) {
            $this->pid = $params['pid'];
            $this->docType = (isset($params['docType'])) ? $params['docType'] : 'orphanDocuments';
        } else {
            $this->pid = $params->pid;
            $this->docType = (isset($params->docType)) ? $params->docType : 'orphanDocuments';
        }
        $path = site_path . '/patients/' . $this->pid . '/' . strtolower(str_replace(' ', '_', $this->docType)) . '/';
        if (is_dir($path) || mkdir($path, 0774, true)) {
            chmod($path, 0774);
        }
        return $this->workingDir = $path;
    }

    /**
     * @return array
     */
    public function getDocumentsTemplates()
    {
        $this->setDocumentsTemplatesModel();
        $this->dt->clearFilters();
        $this->dt->addFilter('template_type', 'documenttemplate');
        return $this->dt->load()->all();
    }

    /**
     * @return array
     */
    public function getDefaultDocumentsTemplates()
    {
        $this->setDocumentsTemplatesModel();
        $this->dt->clearFilters();
        $this->dt->addFilter('template_type', 'defaulttemplate');
        return $this->dt->load()->all();
    }

    /**
     * @return array
     */
    public function getHeadersAndFootersTemplates()
    {
        $this->setDocumentsTemplatesModel();
        $this->dt->clearFilters();
        $this->dt->addFilter('template_type', 'headerorfootertemplate');
        return $this->dt->load()->all();
    }

    /**
     * @param stdClass $params
     * @return stdClass
     */
    public function addDocumentsTemplates(stdClass $params)
    {
        $this->setDocumentsTemplatesModel();
        $params->created_by_uid = $_SESSION['user']['id'];
        return $this->dt->save($params);
    }

    /**
     * @param stdClass $params
     * @return stdClass
     */
    public function updateDocumentsTemplates(stdClass $params)
    {
        $this->setDocumentsTemplatesModel();
        $params->updated_by_uid = $_SESSION['user']['id'];
        return $this->dt->save($params);
    }

    /**
     * @param $doc
     * @return array
     */
    public function checkDocHash($doc)
    {
        $doc = $this->getPatientDocument($doc->id, true);

        $binary_file = $this->isBinary($doc['document']) ?
            $doc['document'] : base64_decode($doc['document']);

        $sha1 = hash('sha1', $binary_file);
        $sha256 = hash('sha256', $binary_file);
        $sha512 = hash('sha512', $binary_file);
        $md5 = hash('md5', $binary_file);

        $msg = "<div style='white-space: nowrap'>
					<b>sha1:</b> {$sha1}<br>
					<b>sha256:</b> {$sha256}<br>
					<b>sha512:</b> {$sha512}<br>
					<b>md5:</b> {$md5}<br>
				</div>";

        return ['success' => true, 'msg' => $msg];
    }

    public function convertDocuments($quantity = 100)
    {

        ini_set('memory_limit', '-1');

        $this->setPatientDocumentModel();

        if (isset($this->d))
            $this->d->addFilter('document_instance', null, '=');
        else
            $this->ad->addFilter('document_instance', null, '=');

        //error_log('LOAD RECORDS');
        if (isset($this->d))
            $records = $this->d->load()->limit(0, $quantity)->all();
        else
            $records = $this->ad->load()->limit(0, $quantity)->all();
        //error_log('LOAD RECORDS COMPLETED');

        foreach ($records as $record) {
            $this->handleDocumentData($record);
        }

        return ['success' => true, 'total' => count($records)];
    }

    public function convertToPath($quantity = 100)
    {

        ini_set('memory_limit', '-1');


        $this->setPatientDocumentModel();
        $this->d->setOrFilterProperties(['filesystem_id']);
        $this->d->addFilter('filesystem_id', null, '=');
        //$this->d->addFilter('filesystem_id', '', '=');

        $records = $this->d->load()->sortBy('id', 'DESC')->limit(0, $quantity)->all();

        foreach ($records as $record) {
            $this->handleDocumentFile($record);
        }

        return ['success' => true, 'total' => count($records)];
    }

    public function convertToPathById($id)
    {

        ini_set('memory_limit', '-1');

        $this->setPatientDocumentModel();
        $record = $this->d->load(['id' => $id])->one();

        if ($record !== false) {
            $this->handleDocumentFile($record);
        }

        return ['success' => true, 'record' => $record];
    }

    public function isBinary($document)
    {
        if (function_exists('is_binary')) {
            return is_binary($document);
        }
        return preg_match('~[^\x20-\x7E\t\r\n]~', $document) > 0;
    }

    public function base64ToBinary($document, $encrypted = false)
    {
        // handle binary documents
        if ($this->isBinary($document)) {
            return $document;
        } else {
            return base64_decode($document);
        }
    }


    public function documentSyncer()
    {


//		$server_url = 'http://local.tranextgen.com/mdtimeline';
//		$site = 'default';
//		$key = 'WDV2-RR2B-8RBI-V96Z-R0X3';
//		$interval_hours = 8000;

        $server_url = Globals::getGlobal('master_server_url');
        $site = Globals::getGlobal('master_server_site');
        $key = Globals::getGlobal('master_server_key');
        $threshold_hours = Globals::getGlobal('master_server_sync_threshold_hrs');

        $threshold = date('Y-m-d: h:i:s', strtotime("-{$threshold_hours} hours"));
        $wsdl = rtrim($server_url, '/') . '/dataProvider/SOAP/wsdl.php?wsdl';
        $SoapClient = new SoapClient($wsdl);

        $request = new stdClass();
        $request->SecureKey = $key;
        $request->ServerSite = $site;
        $request->DocumentIds = [];

        $filters = new stdClass();
        $foo = new stdClass();
        $foo->property = 'date';
        $foo->value = $threshold;
        $foo->operator = '>';
        $filters->filter[] = $foo;

        $document_records = $this->getPatientDocuments($filters, true);

        if ($document_records['total'] === 0) {
            return [];
        }

        $document_records_buff = [];

        foreach ($document_records['data'] as $document_record) {
            if (empty($document_record['document'])) continue;
            $request->DocumentIds[] = $document_record['id'];
            $document_records_buff[$document_record['id']] = $document_record;
        }

        if (empty($request->DocumentIds)) {
            return [];
        }

        $request->DocumentIds = implode(',', $request->DocumentIds);

        $results = $SoapClient->GetDocuments($request);

        if (!$results->Success) {
            error_log("Document Syncer: Unsuccess SOAP Call");
            return [];
        }

        if (!isset($results->Document)) {
            return [];
        }

        $documents = is_array($results->Document) ? $results->Document : [$results->Document];

        foreach ($documents as $document) {


            $document_id = $document->Id;
            $document_record = isset($document_records_buff[$document_id]) ? $document_records_buff[$document_id] : false;

            if ($document_record === false) {
                error_log("Document Syncer: Document Record Buff Not Found - Documetn ID = {$document_id}");
                continue;
            }

            $file_system = isset($this->file_systems[$document_record['filesystem_id']]) ? $this->file_systems[$document_record['filesystem_id']] : false;

            if ($file_system === false) {
                error_log("Document Syncer: FileSystem Not Found - FileSystem ID = {$document_record['filesystem_id']}");
                continue;
            }

            $file_path = rtrim($file_system['dir_path'], '/') . '/' . trim($document_record['path'], '/') . '/' . ltrim($document_record['name'], '/');


            if (file_exists($file_path)) {
                error_log("Document Syncer: File Exist - {$file_path}");
                continue;
            }

            $success = file_put_contents($file_path, base64_decode($document->Base64Data));

            if (!$success) {
                error_log("Document Syncer: Unable to Create File - {$file_path}");
            }

        }


        return [];
    }
}

//$d = new DocumentHandler();
//$d->reHashDocs();
