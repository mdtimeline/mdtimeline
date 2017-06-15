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

class DocumentHandler {

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
	private $d;

	/**
	 * @var MatchaCUP
	 */
	private $t;

	/**
	 * @var FileSystem
	 */
	private $FileSystem;


	private $storeAsFile = true;

	private $doctorsnotes;

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
		'text/html' => 'html'
	];

	function __construct(){
		$this->db = new MatchaHelper();
		$this->FileSystem = new FileSystem();
		return;
	}

	private function setPatientDocumentModel(){
		if(!isset($this->d))
			$this->d = MatchaModel::setSenchaModel('App.model.patient.PatientDocuments');
	}

	private function setPatientDocumentTempModel(){
		if(!isset($this->t))
			$this->t = MatchaModel::setSenchaModel('App.model.patient.PatientDocumentsTemp');
	}

	/**
	 * @param      $params
	 * @param bool $includeDocument
	 *
	 * @return mixed
	 */
	public function getPatientDocuments($params, $includeDocument = false){
		$this->setPatientDocumentModel();
		$records = $this->d->load($params)->all();

		/** lets unset the actual document data */
		if(isset($records['data'])){
			foreach($records['data'] as $i => $record){

				if($records['data'][$i]['entered_in_error']){
					$records['data'][$i]['docType'] = 'ENTERED IN ERROR';
					$records['data'][$i]['docTypeCode'] = 'ZZZ';
				}

				if(!$includeDocument){
					unset($records['data'][$i]['document']);
				}
			}
		}
		return $records;
	}

	/**
	 * @param $params
	 * @param $includeDocument
	 *
	 * @return mixed
	 */
	public function getPatientDocument($params, $includeDocument = false){
		$this->setPatientDocumentModel();
		$record = $this->d->load($params)->one();

		if($record !== false && $includeDocument){

			$file_path = $record['path'] . '/' . $record['name'];
			$is_file = isset($record['path']) && $record['path'] != '' && file_exists($file_path);

			if ($is_file) {
				$record['document'] = file_get_contents($file_path);
			} elseif(isset($record['document_instance']) && $record['document_instance'] != ''){
				$dd = MatchaModel::setSenchaModel('App.model.administration.DocumentData', false, $record['document_instance']);
				$data = $dd->load($record['document_id'])->one();
				if($data !== false){
					$record['document'] = $data['document'];
				}
			}

			if(isset($record['document']) && $this->isBinary($record['document'])){
				$record['document'] = base64_encode($record['document']);
			}
		}

		return $record;
	}

	/**
	 * @param $params
	 *
	 * @return array
	 */
	public function addPatientDocument($params){
		$this->setPatientDocumentModel();
		if(is_array($params)){
			foreach($params as $i => $param){
				/** remove the mime type */
				$params[$i]->document = $this->trimBase64($params[$i]->document);

				/** encrypted if necessary */
				if(isset($params[$i]->encrypted) && $params[$i]->encrypted){
					$params[$i]->document = MatchaUtils::encrypt($params[$i]->document);
				};
				$binary_file = $this->isBinary($params[$i]->document) ?
					$params[$i]->document : base64_decode($params[$i]->document);
				$params[$i]->hash = hash('sha256', $binary_file);
			}
		}else{
			/** remove the mime type */
			$params->document = $this->trimBase64($params->document);
			/** encrypted if necessary */
			if(isset($params->encrypted) && $params->encrypted){
				$params->document = MatchaUtils::encrypt($params->document);
			};
			$binary_file = $this->isBinary($params->document) ?
				$params->document : base64_decode($params->document);

			$params->hash = hash('sha256', $binary_file);
		}

		$results = $this->d->save($params);

		if(is_array($results)){
			foreach($results as &$result){
				if($this->storeAsFile){
					$this->handleDocumentFile($result);
				}else{
					$this->handleDocumentData($result);
				}
			}
		}else{
			if($this->storeAsFile){
				$this->handleDocumentFile($results);
			}else{
				$this->handleDocumentData($results);
			}
		}
		return $results;
	}

	/**
	 * This logic is to eventually split the document into multiples tables
	 * using the sencha model instance
	 *
	 * @param $document
	 */
	private function handleDocumentData(&$document){

		try{
			$document = (object) $document;
			$instance = floor($document->id / $this->filesPerInstance) + 1;
			$conn = Matcha::getConn();
			$sth = $conn->prepare("SHOW TABLES LIKE 'documents_data_{$instance}'");
			$sth->execute();
			$table = $sth->fetch(PDO::FETCH_ASSOC);

			if($table === false){
				$document_model = MatchaModel::setSenchaModel('App.model.administration.DocumentData', true, $instance);
			}else{
				$document_model = MatchaModel::setSenchaModel('App.model.administration.DocumentData', false, $instance);
			}

			if($document_model === false) {
				throw new Exception("Unable to create App.model.administration.DocumentData model instance '{$instance}'");
			}

			$document->document = $this->base64ToBinary($document->document);
			$file_info = new finfo(FILEINFO_MIME_TYPE);
			$mime_type = $file_info->buffer($document->document);
			if(!isset($this->mime_types_ext[$mime_type])){
				throw new Exception('File extension not supported. document_id: ' . $document->id . ' mime_type: '. $mime_type);
			}
			$document_code = isset($document->docTypeCode) ? $document->docTypeCode : '';

			if($mime_type == 'application/xml' && preg_match('/\<!DOCTYPE html/', $document->document)){
				$mime_type = 'text/html';
			}

			$ext = $this->mime_types_ext[$mime_type];
			$file_name = $document_code .'_' .$document->id . '_' . $document->pid . '.' . $ext;

			$document->name = $file_name;

			//error_log('DOCUMENT');
			$data = new stdClass();
			$data->pid = $document->pid;
			$data->document = $document->document;
			$record = $document_model->save($data);
			//error_log('DOCUMENT DATA COMPLETED');

			$document->document ='';
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
		}catch(Exception $e){
			error_log('Error Converting Document');
			error_log($e->getMessage());
		}
	}

	/**
	 * @param $document
	 */
	private function handleDocumentFile(&$document){

		try{
			$document = (object)$document;
			$conn = Matcha::getConn();

			$filesystem = $this->FileSystem->getOnlineFileSystem();
			if($filesystem !== false){
				$filesystem_path = rtrim($filesystem['dir_path'], '/');
				$filesystem_id = $filesystem['id'];
			}else{
				$filesystem_path = '';
				$filesystem_id = 0;
			}

			$document_path = $filesystem_path === '' ? (site_path . '/documents') : '';

			/**
			 * change date to path  2016-01-23 => 2016/01/23
			 */
			$document_path .= '/' . str_ireplace(['-', ' '], '/', substr($document->date, 0, 10));

			if(!file_exists($filesystem_path . $document_path)){
				mkdir($filesystem_path . $document_path, 0777, true);
				chmod($filesystem_path . $document_path, 755);
			}

			if(isset($document->document_instance) && $document->document_instance > 0 && (!isset($document->document) || $document->document == '')){
				$dd = MatchaModel::setSenchaModel('App.model.administration.DocumentData', false, $document->document_instance);
				if($dd !== false){
					$data = $dd->load($document->document_id)->one();
					if($data !== false){
						$document->document = $data['document'];
					}
					unset($data);
				}
			}

			$document->document = $this->base64ToBinary($document->document);
			$file_info = new finfo(FILEINFO_MIME_TYPE);
			$mime_type = $file_info->buffer($document->document);

			if(!isset($this->mime_types_ext[$mime_type])){
				throw new Exception('File extension not supported. document_id: ' . $document->id . ' mime_type: ' . $mime_type);
			}

			$document_code = isset($document->docTypeCode) ? $document->docTypeCode : '';

			if($mime_type == 'application/xml' && preg_match('/\<!DOCTYPE html/', $document->document)){
				$mime_type = 'text/html';
			}

			$ext = $this->mime_types_ext[$mime_type];
			$file_name = $document_code . '_' . $document->id . '_' . $document->pid . '.' . $ext;
			$path = $filesystem_path . $document_path . '/' . $file_name;

			if(file_exists($path) && !unlink($path)){
				throw new Exception('File name exist. document_id: ' . $document->id . ' path: ' . $path);
			}

			if(file_put_contents($path, $document->document) === false){
				throw new Exception('Unable to write file. document_id: ' . $document->id . ' path: ' . $path);
			}

			$sth = $conn->prepare("UPDATE patient_documents SET filesystem_id = :filesystem_id, path = :path, `name` = :name WHERE id = :id;");
			$sth->execute([
				':id' => $document->id,
				':filesystem_id' => $filesystem_id,
				':path' => $document_path,
				':name' => $file_name
			]);

			//error_log('DOCUMENT COMPLETE');
			unset($document->document);
			unset($data, $record, $document_model);

		} catch(Exception $e){
			error_log('Error Converting Document');
			error_log($e->getMessage());
		}
	}

	/**
	 * @param $params
	 *
	 * @return array
	 */
	public function updatePatientDocument($params){
		$this->setPatientDocumentModel();

		if(is_array($params)){
			foreach($params as &$param){
				unset($param->document, $param->hash);
			}
		}else{
			unset($params->document, $params->hash);
		}

		return $this->d->save($params);
	}

	/**
	 * @param $params
	 *
	 * @return mixed
	 */
	public function destroyPatientDocument($params){
		$this->setPatientDocumentModel();
		return $this->d->destroy($params);
	}

	/**
	 * @param $params
	 * @return object|stdClass
	 */
	public function createTempDocument($params){
		$this->setPatientDocumentTempModel();
		$params = (object) $params;
		$record = new stdClass();
		if(isset($params->document) && $params->document != ''){
			$record->document = $params->document;
		}else{
			$this->documents = new Documents();
			$record->document = base64_encode($this->documents->PDFDocumentBuilder((object) $params));;
		}
		$record->create_date = date('Y-m-d H:i:s');
		$record->document_name = isset($params->document_name) ? $params->document_name : '';
		$record = (object) $this->t->save($record);
		unset($record->document);
		return $record;
	}

	/**
	 * @param $params
	 * @return object|stdClass
	 */
	public function createRawTempDocument($params){
		$this->setPatientDocumentTempModel();
		$params = (object) $params;
		$record = new stdClass();
		$record->create_date = date('Y-m-d H:i:s');
		$record->document_name = $params->document_name;
		$record->document = base64_encode($params->document);
		$record = (object) $this->t->save($record);
		unset($record->document);
		return $record;
	}

	public function destroyTempDocument($params){
		$this->setPatientDocumentTempModel();
		return $this->t->destroy($params);
	}

	/**
	 * @param $params
	 *
	 * @return array|mixed
	 */
	public function transferTempDocument($params){
		$this->setPatientDocumentModel();
		$this->setPatientDocumentTempModel();
		$record = $this->t->load($params)->one();
		if($record == false) return ['success' => false];

		$params->document = $record['document'];
		$params->date = date('Y-m-d H:i:s');
		$params->name = 'transferred.pdf';
		unset($params->id);

		$params = $this->addPatientDocument($params);
		unset($params['data']->document);
		return ['success' => true, 'record' => $params['data']];
	}

	private function trimBase64($base64){

		if(!preg_match('/data:/', $base64)){
			return $base64;
		}
		$pos = strpos($base64, ',');
		if($pos === false) return $base64;
		return substr($base64, $pos + 1);
	}

	/**
	 * @return string
	 */
	protected function getDocumentUrl(){
		return $_SESSION['site']['url'] . '/patients/' . $this->pid . '/' . strtolower(str_replace(' ', '_', $this->docType)) . '/' . $this->fileName;
	}

	/**
	 * @param $id
	 * @return string
	 */
	public function getDocumentPathById($id){
		$this->db->setSQL("SELECT * FROM patient_documents WHERE id = '$id'");
		$doc = $this->db->fetchRecord(PDO::FETCH_ASSOC);
		return site_path . '/patients/' . $doc['pid'] . '/' . strtolower(str_replace(' ', '_', $doc['docType'])) . '/' . $doc['name'];
	}

	/**
	 * @param $file
	 * @return string
	 */
	protected function reNameFile($file){
		$foo = explode('.', $file['filePath']['name']);
		$ext = end($foo);
		return $this->fileName = $this->setName() . '.' . $ext;
	}

	/**
	 * @return string
	 */
	protected function nameFile(){
		return $this->fileName = $this->setName() . '.pdf';
	}

	/**
	 * @return int
	 */
	protected function setName(){
		$name = time();
		while(file_exists($this->workingDir . '/' . $name)){
			$name = time();
		}
		return $name;
	}

	/**
	 * @param $params
	 * @return string
	 */
	protected function getPatientDir($params){
		if(is_array($params)){
			$this->pid = $params['pid'];
			$this->docType = (isset($params['docType'])) ? $params['docType'] : 'orphanDocuments';
		} else{
			$this->pid = $params->pid;
			$this->docType = (isset($params->docType)) ? $params->docType : 'orphanDocuments';
		}
		$path = site_path . '/patients/' . $this->pid . '/' . strtolower(str_replace(' ', '_', $this->docType)) . '/';
		if(is_dir($path) || mkdir($path, 0774, true)){
			chmod($path, 0774);
		}
		return $this->workingDir = $path;
	}

	/**
	 * @return array
	 */
	public function getDocumentsTemplates(){
		$this->db->setSQL("SELECT * FROM documents_templates WHERE template_type = 'documenttemplate'");
		return $this->db->fetchRecords(PDO::FETCH_ASSOC);
	}

	/**
	 * @return array
	 */
	public function getDefaultDocumentsTemplates(){
		$this->db->setSQL("SELECT * FROM documents_templates WHERE template_type = 'defaulttemplate'");
		return $this->db->fetchRecords(PDO::FETCH_ASSOC);
	}

	/**
	 * @return array
	 */
	public function getHeadersAndFootersTemplates(){
		$this->db->setSQL("SELECT * FROM documents_templates WHERE template_type = 'headerorfootertemplate'");
		return $this->db->fetchRecords(PDO::FETCH_ASSOC);
	}

	/**
	 * @param stdClass $params
	 * @return stdClass
	 */
	public function addDocumentsTemplates(stdClass $params){
		$data = get_object_vars($params);
		$data['created_by_uid'] = $_SESSION['user']['id'];
		$this->db->setSQL($this->db->sqlBind($data, 'documents_templates', 'I'));
		$this->db->execLog();
		$params->id = $this->db->lastInsertId;
		return $params;
	}

	/**
	 * @param stdClass $params
	 * @return stdClass
	 */
	public function updateDocumentsTemplates(stdClass $params){
		$data = get_object_vars($params);
		$data['updated_by_uid'] = $_SESSION['user']['id'];
		unset($data['id']);
		$this->db->setSQL($this->db->sqlBind($data, 'documents_templates', 'U', ['id' => $params->id]));
		$this->db->execLog();
		return $params;

	}

	/**
	 * @param $doc
	 * @return array
	 */
	public function checkDocHash($doc){
		$doc = $this->getPatientDocument($doc->id, true);

		$binary_file = $this->isBinary($doc['document']) ?
			$doc['document'] : base64_decode($doc['document']);

		$sha1  = hash('sha1', $binary_file);
		$sha256  = hash('sha256', $binary_file);
		$sha512  = hash('sha512', $binary_file);
		$md5  = hash('md5', $binary_file);

		$msg = "<div style='white-space: nowrap'>
					<b>sha1:</b> {$sha1}<br>
					<b>sha256:</b> {$sha256}<br>
					<b>sha512:</b> {$sha512}<br>
					<b>md5:</b> {$md5}<br>
				</div>";

		return [ 'success' => true, 'msg' => $msg ];
	}

	public function convertDocuments($quantity = 100){

		ini_set('memory_limit', '-1');

		$this->setPatientDocumentModel();
		$this->d->addFilter('document_instance', null, '=');

		//error_log('LOAD RECORDS');
		$records = $this->d->load()->limit(0, $quantity)->all();
		//error_log('LOAD RECORDS COMPLETED');

		foreach($records as $record){
			$this->handleDocumentData($record);
		}

		return [ 'success' => true, 'total' => count($records) ];
	}

	public function convertToPath($quantity = 100){

		ini_set('memory_limit', '-1');

		$this->setPatientDocumentModel();
		$this->d->addFilter('path', null, '=');

		$records = $this->d->load()->limit(0, $quantity)->all();

		foreach($records as $record){
			$this->handleDocumentFile($record);
		}

		return [ 'success' => true, 'total' => count($records) ];
	}

	public function isBinary($document){
		if(function_exists('is_binary')) {
			return is_binary($document);
		}
		return preg_match('~[^\x20-\x7E\t\r\n]~', $document) > 0;
	}

	public function base64ToBinary($document, $encrypted = false) {
		// handle binary documents
		if($this->isBinary($document)){
			return $document;
		}else{
			return base64_decode($document);
		}
	}
}

//$d = new DocumentHandler();
//$d->reHashDocs();
