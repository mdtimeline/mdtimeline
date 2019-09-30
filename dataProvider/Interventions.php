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

class Interventions {
	/**
	 * @var MatchaCUP
	 */
	private $i;

	function __construct(){
        if($this->i == NULL)
            $this->i = MatchaModel::setSenchaModel('App.model.patient.Intervention', true);
	}

	public function getPatientInterventions($params){
		return $this->i->load($params)->leftJoin(['service_date' => 'service_date'], 'encounter', 'eid', 'eid')->all();
	}

	public function getPatientIntervention($params){
		return $this->i->load($params)->leftJoin(['service_date' => 'service_date'], 'encounter', 'eid', 'eid')->one();
	}

	public function addPatientIntervention($params){
		return $this->i->save($params);
	}

	public function updatePatientIntervention($params){
		return $this->i->save($params);
	}

	public function destroyPatientIntervention($params){
		return $this->i->destroy($params);
	}

	public function getPatientInterventionsByPid($pid){
		$this->i->addFilter('pid', $pid);
		return $this->i->load()->leftJoin(['service_date' => 'service_date'], 'encounter', 'eid', 'eid')->all();
	}

	public function getPatientInterventionsByPidAndDates($pid){

		$params = new stdClass();
		$params->sort[0] = new stdClass();
		$params->sort[0]->property = 'date';
		$params->sort[0]->direction = 'DESC';

		$this->i->addFilter('pid', $pid);
		if(isset($start)){
			$this->i->addFilter('date', $start, '>=');
		}
		if(isset($end)) {
			$this->i->addFilter('date', $end, '<=');
		}

		return $this->i->load()->leftJoin(['service_date' => 'service_date'], 'encounter', 'eid', 'eid')->all();
	}


}
