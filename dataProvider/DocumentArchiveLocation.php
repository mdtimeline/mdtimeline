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

class DocumentArchiveLocation {

	/**
	 * @var MatchaCUP
	 */
	private $dl;
	/**
	 * @var MatchaCUP
	 */
	private $pdl;

	function __construct(){
        $this->dl = MatchaModel::setSenchaModel('App.model.patient.DocumentArchiveLocation');
        $this->pdl = MatchaModel::setSenchaModel('App.model.patient.PatientDocumentArchiveLocation');
	}


	public function addDocumentLocationSearch($params){

		$results = [];
		$wheres = [];
		$wheres_params = [];

		if($params->is_worklist){
			$wheres[] = '`date` >= :date_from';
			$wheres[] = '`date` <= :date_to';

			$wheres_params['date_from'] = $params->date_from;
			$wheres_params['date_to'] = $params->date_to;

			if(isset($params->pid)){
				$wheres[] = '`pid` <= :pid';
				$wheres_params['pid'] = $params->pid;
			}

			if(isset($params->uid)){
				$wheres[] = '`uid` <= :uid';
				$wheres_params['uid'] = $params->uid;
			}

			if(isset($params->facility_id)){
				$wheres[] = '`facility_id` <= :facility_id';
				$wheres_params['facility_id'] = $params->facility_id;
			}

			$is_archive = $params->archived ? 'IS NOT' : 'IS';

			$wheres = implode(' AND ', $wheres);

			$sql = "
				SELECT l.id as id,
					   a.reference_number as archive_reference_number,
					   a.description as archive_description,
					   d.id as document_id,
					   l.location_id as location_id,
					   d.date as scanned_date,
					   d.document_info as document_info,
				 	   su.fname AS scanned_by_fname,
				 	   su.mname AS scanned_by_mname,
				 	   su.lname AS scanned_by_lname,
				 	   au.fname AS archived_by_fname,
				 	   au.mname AS archived_by_mname,
			      	   au.lname AS archived_by_lname,
				 	   p.pubpid AS patient_record_number,
				 	   p.fname AS patient_fname,
				 	   p.mname AS patient_mname,
			      	   p.lname AS patient_lname,
				 	   f.name as facility,
				 	   l.notes as notes,
				 	   l.create_uid as create_uid,
				 	   l.update_uid as update_uid,
				 	   l.create_date as create_date,
				 	   l.update_date as update_date,
				 	   l.create_date as create_date
				 FROM (SELECT 
					id,
					pid,
					`date`,
					uid,
					facility_id,
					CONCAT(docTypeCode, ' - ', docType) AS document_info
				FROM patient_documents
				WHERE {$wheres} LIMIT 500) AS d
				LEFT JOIN facility AS f ON f.id = d.facility_id
				LEFT JOIN patient_documents_archive_locations AS l ON d.id = l.document_id
                LEFT JOIN users AS su ON su.id = d.uid
				LEFT JOIN users AS au ON au.id = l.create_uid
				LEFT JOIN patient AS p ON p.pid = d.pid
				LEFT JOIN documents_archive_locations AS a ON a.id = l.location_id
				WHERE l.id {$is_archive} NULL
				GROUP BY d.id
			";


			$results = $this->pdl->sql($sql)->all($wheres_params);

		}else{


			$wheres[] = '`location_id` = :location_id';
			$wheres_params['location_id'] = $params->location_id;
			$wheres = implode(' AND ', $wheres);

			$sql = "
				SELECT l.*,
					   a.reference_number as archive_reference_number,
					   a.description as archive_description,
					   d.`date` as scanned_date,
					   CONCAT(d.docTypeCode, ' - ', d.docType) AS document_info,
				       su.fname AS scanned_by_fname,
				       su.mname AS scanned_by_mname,
				       su.lname AS scanned_by_lname,
				       au.fname AS archived_by_fname,
				       au.mname AS archived_by_mname,
				       au.lname AS archived_by_lname,
				       p.pubpid AS patient_record_number,
				       p.fname AS patient_fname,
				 	   p.mname AS patient_mname,
			      	   p.lname AS patient_lname,
				       f.name as facility
				FROM (SELECT pdal.* FROM patient_documents_archive_locations AS pdal  WHERE {$wheres}) AS l
				LEFT JOIN patient_documents AS d ON d.id = l.document_id
				LEFT JOIN facility AS f ON f.id = d.facility_id
				LEFT JOIN users AS su ON su.id = d.uid
				LEFT JOIN users AS au ON au.id = l.create_uid
				LEFT JOIN patient AS p ON p.pid = d.pid
				LEFT JOIN documents_archive_locations AS a ON a.id = l.location_id
				GROUP BY d.id
			";

			$results = $this->pdl->sql($sql)->all($wheres_params);
		}

		return [
			'total' => count($results),
			'data' => array_splice($results, $params->start, $params->limit)
		];
	}


	public function archiveDocuments($params){

	}

	public function unArchiveDocuments($params){

	}

	public function getDocumentLocations($params){
		return $this->dl->load($params)->all();
	}

	public function getDocumentLocation($params){
		return $this->dl->load($params)->one();
	}

	public function addDocumentLocation($params){
		return $this->dl->save($params);
	}

	public function updateDocumentLocation($params){
		return $this->dl->save($params);
	}

	public function destroyDocumentLocation($params){
		return $this->dl->destroy($params);
	}

	public function getPatientDocumentLocations($params){
		return $this->pdl->load($params)->all();
	}

	public function getPatientDocumentLocation($params){
		return $this->pdl->load($params)->one();
	}

	public function addPatientDocumentLocation($params){
		return $this->pdl->save($params);
	}

	public function updatePatientDocumentLocation($params){
		return $this->pdl->save($params);
	}

	public function destroyPatientDocumentLocation($params){
		return $this->pdl->destroy($params);
	}


}
