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
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

class HealthConcerns {
	/**
	 * @var MatchaCUP
	 */
	private $c;

	function __construct(){
        if($this->c == NULL)
            $this->c = MatchaModel::setSenchaModel('App.model.patient.HealthConcern');
	}

	public function getPatientHealthConcerns($params){

//		$record = [
//			[
//				'health_concern_type' => 'HealthStatusObservation',
//				'code' => '2.16.840.1.113883.6.96',
//				'code_text' => 'Chronically ill',
//				'code_type' => 'SNOMED-CT',
//				'instructions' => '',
//				'active' => '1',
//			],
//			[
//				'health_concern_type' => 'HealthConcernAct',
//				'code' => '',
//				'code_text' => 'Hypertension: BP 145/88',
//				'code_type' => '',
//				'instructions' => '',
//				'active' => '1',
//			],
//			[
//				'health_concern_type' => 'HealthConcernAct',
//				'code' => '',
//				'code_text' => '',
//				'code_type' => '',
//				'instructions' => 'a. Chronic Sickness exhibited by patient
//				b. HealthCare Concerns refer to underlying clinical facts
//  i. Documented HyperTension problem
//  ii. Documented HypoThyroidism problem
//  iii. Watch Weight of patient',
//				'active' => '1',
//			]
//		];
//
//		return $record;


		return $this->c->load($params)->all();
	}

	public function getPatientHealthConcern($params){
		return $this->c->load($params)->one();
	}

	public function addPatientHealthConcern($params){
		return $this->c->save($params);
	}

	public function updatePatientHealthConcern($params){
		return $this->c->save($params);
	}

	public function destroyPatientIntervention($params){
		return $this->c->destroy($params);
	}

	public function destroyPatientHealthConcern($pid){
		$this->c->addFilter('pid', $pid);
		return $this->c->load()->all();
	}

	public function getPatientHealthConcernsByEid($eid){
		$this->c->addFilter('eid', $eid);
		return $this->c->load()->all();
	}

	public function getPatientHealthConcernsByPid($pid){
		$this->c->addFilter('pid', $pid);
		return $this->c->load()->all();
	}


}
