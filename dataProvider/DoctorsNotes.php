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

class DoctorsNotes {

	/**
	 * @var bool|MatchaCUP
	 */
	private $n;

	function __construct(){
        if($this->n == NULL)
            $this->n = MatchaModel::setSenchaModel('App.model.patient.DoctorsNote');
	}

	public function getDoctorsNotes($params){
		$this->n->setOrFilterProperties(['id']);
		return $this->n->load($params)->all();
	}

	public function getDoctorsNote($params){
		return $this->n->load($params)->one();
	}

	public function addDoctorsNote($params){
		return $this->n->save($params);
	}

	public function updateDoctorsNote($params){
		return $this->n->save($params);
	}

	public function destroyDoctorsNote($params){
		return $this->n->destroy($params);
	}

}
