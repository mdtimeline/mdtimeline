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

class Disclosure {

	/**
	 * @var MatchaCUP
	 */
	private $d;
	/**
	 * @var MatchaCUP
	 */
	private $ds;

	function __construct(){
            $this->d = MatchaModel::setSenchaModel('App.model.patient.Disclosures');
            $this->ds = MatchaModel::setSenchaModel('App.model.patient.DisclosuresDocument');
	}

	public function getDisclosures($params){

		$where = '';
		$values = [];

		if(isset($params->filter)){
			$buff = [];
			foreach ($params->filter as $filter){
				$buff [] = "d.{$filter->property} {$filter->operator} :{$filter->property}";
				$values[":{$filter->property}"] = $filter->value;
			}
			$where = 'WHERE ' . implode(' AND ', $buff);
		}

		$sql = "SELECT d.*,
					   GROUP_CONCAT(CONCAT(pd.docType,' - ', pd.title) SEPARATOR '<br>') as document_inventory,
					   GROUP_CONCAT(pd.id) as document_inventory_ids,
					   COUNT(*) as document_inventory_count
				  FROM patient_disclosures as d
			 LEFT JOIN patient_disclosures_documents as dd ON dd.disclosure_id = d.id
			 LEFT JOIN patient_documents as pd ON pd.id = dd.document_id
			  {$where}
			  GROUP BY d.id";

		return $this->d->sql($sql)->all($values);
	}

	public function getDisclosure($params){
		return $this->d->load($params)->one();
	}

	public function addDisclosure($params){
		return $this->d->save($params);
	}

	public function updateDisclosure($params){
		return $this->d->save($params);
	}

	public function destroyDisclosure($params){
		return $this->d->destroy($params);
	}

	public function getDisclosuresDocuments($params){
		return $this->ds->load($params)->all();
	}

	public function getDisclosuresDocument($params){
		return $this->ds->load($params)->one();
	}

	public function addDisclosuresDocument($params){
		return $this->ds->save($params);
	}

	public function updateDisclosuresDocument($params){
		return $this->ds->save($params);
	}

	public function destroyDisclosuresDocument($params){
		return $this->ds->destroy($params);
	}

	public function removeDisclosuresDocumentsById($disclosure_id){
		$this->ds->sql('DELETE FROM patient_disclosures_documents WHERE disclosure_id = :disclosure_id');
		$this->ds->exec([':disclosure_id' => $disclosure_id]);
	}

	public function generateDisclosure($pid, $document_ids){








		return [
			'success' => true
		];
	}



}
