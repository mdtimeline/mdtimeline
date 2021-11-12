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

class Visits {

	/**
	 * @var MatchaCUP
	 */
	private $v;

	function __construct() {
        $this->v = MatchaModel::setSenchaModel('App.model.patient.Visit');
	}

	public function getVisits(stdClass $params){
		return $this->v->load($params)->leftJoin(
			[
				'pubpid' => 'patient_record_number',
				'fname' => 'patient_fname',
				'mname' => 'patient_mname',
				'lname' => 'patient_lname'
			], 'patient', 'pid', 'pid'
		)->leftJoin(
			[
				'fname' => 'attending_doctor_fname',
				'mname' => 'attending_doctor_mname',
				'lname' => 'attending_doctor_lname'
			], 'referring_providers', 'attending_doctor_id', 'id'
		)->leftJoin(
			[
				'fname' => 'admitting_doctor_fname',
				'mname' => 'admitting_doctor_mname',
				'lname' => 'admitting_doctor_lname'
			], 'referring_providers', 'admitting_doctor_id', 'id'
		)->leftJoin(
			[
				'fname' => 'referring_doctor_fname',
				'mname' => 'referring_doctor_mname',
				'lname' => 'referring_doctor_lname'
			], 'referring_providers', 'referring_doctor_id', 'id'
		)->leftJoin(
			[
				'fname' => 'consulting_doctor_fname',
				'mname' => 'consulting_doctor_mname',
				'lname' => 'consulting_doctor_lname'
			], 'referring_providers', 'consulting_doctor_id', 'id'
		)->all();
	}

    public function getVisit($params){
        return $this->v->load($params)->leftJoin(
            [
                'pubpid' => 'patient_record_number',
                'fname' => 'patient_fname',
                'mname' => 'patient_mname',
                'lname' => 'patient_lname'
            ], 'patient', 'pid', 'pid'
        )->leftJoin(
            [
                'fname' => 'attending_doctor_fname',
                'mname' => 'attending_doctor_mname',
                'lname' => 'attending_doctor_lname'
            ], 'referring_providers', 'attending_doctor_id', 'id'
        )->leftJoin(
            [
                'fname' => 'admitting_doctor_fname',
                'mname' => 'admitting_doctor_mname',
                'lname' => 'admitting_doctor_lname'
            ], 'referring_providers', 'admitting_doctor_id', 'id'
        )->leftJoin(
            [
                'fname' => 'referring_doctor_fname',
                'mname' => 'referring_doctor_mname',
                'lname' => 'referring_doctor_lname'
            ], 'referring_providers', 'referring_doctor_id', 'id'
        )->leftJoin(
            [
                'fname' => 'consulting_doctor_fname',
                'mname' => 'consulting_doctor_mname',
                'lname' => 'consulting_doctor_lname'
            ], 'referring_providers', 'consulting_doctor_id', 'id'
        )->one();
    }

	public function addVisit($params){
		return $this->v->save($params);
	}

	public function updateVisit($params){
		return $this->v->save($params);
	}

	public function destroyVisit($params){
		return $this->v->destroy($params);
	}

}

