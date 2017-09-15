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

class ProcedureHistory {
	/**
	 * @var bool|MatchaCUP
	 */
	private $p;

	function __construct() {
        if(!isset($this->p))
            $this->p = MatchaModel::setSenchaModel('App.model.patient.ProcedureHistory');
	}

	public function getProcedureHistories($params) {
		return $this->p->load($params)->all();
	}

	public function getProcedureHistory($params) {
		return $this->p->load($params)->one();
	}

	public function addProcedureHistory($params) {
		return $this->p->save($params);
	}

	public function updateProcedureHistory($params) {
		return $this->p->save($params);
	}

	public function destroyProcedureHistory($params) {
		return $this->p->destroy($params);
	}

}